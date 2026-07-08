<?php

namespace Modules\Savings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\Client;
use Modules\Savings\Entities\BulkSavingsEntry;
use Modules\Savings\Entities\BulkSavingsEntryItem;
use Modules\Savings\Entities\Savings;
use Modules\Savings\Entities\SavingsTransaction;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class BulkSavingsEntryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:savings.bulk_entry.index'])->only(['index', 'show']);
        $this->middleware(['permission:savings.bulk_entry.create'])->only(['create', 'store']);
        $this->middleware(['permission:savings.bulk_entry.verify'])->only(['verify', 'verify_entries', 'reject_entries']);
    }

    /**
     * Display bulk entries list
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $status = $request->status;
        $created_by = $request->created_by;
        $officer_id = $request->officer_id;

        $data = BulkSavingsEntry::with(['savings_officer', 'created_by', 'verified_by'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($created_by, function ($query) use ($created_by) {
                $query->where('created_by_user_id', $created_by);
            })
            ->when($officer_id, function ($query) use ($officer_id) {
                $query->where('savings_officer_id', $officer_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->input());

        return theme_view('savings::bulk_entry.index', compact('data'));
    }

    /**
     * Show entry details
     */
    public function show($id)
    {
        $entry = BulkSavingsEntry::with(['items', 'savings_officer', 'created_by', 'verified_by'])->findOrFail($id);
        $stats = $entry->getStats();
        
        return theme_view('savings::bulk_entry.show', compact('entry', 'stats'));
    }

    /**
     * Show form to select officer and create new entry
     */
    public function create(Request $request)
    {
        $officers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Savings Officer');
        })->get();

        $selected_officer_id = $request->officer_id;
        $clients = [];
        $savings_data = [];

        if ($selected_officer_id) {
            // Get all clients of selected officer
            $clients = Client::whereHas('savings', function ($query) use ($selected_officer_id) {
                $query->where('savings_officer_id', $selected_officer_id)
                    ->where('status', 'active');
            })->with(['savings' => function ($query) use ($selected_officer_id) {
                $query->where('savings_officer_id', $selected_officer_id)
                    ->where('status', 'active');
            }])->get();

            // Get savings data for each client
            foreach ($clients as $client) {
                foreach ($client->savings as $saving) {
                    $balance = $this->getSavingsBalance($saving->id);
                    $savings_data[$saving->id] = [
                        'client_id' => $client->id,
                        'client_name' => $client->first_name . ' ' . $client->last_name,
                        'account_number' => $saving->account_number,
                        'balance' => $balance
                    ];
                }
            }
        }

        return theme_view('savings::bulk_entry.create', compact('officers', 'selected_officer_id', 'clients', 'savings_data'));
    }

    /**
     * Store bulk entry
     */
    public function store(Request $request)
    {
        $request->validate([
            'savings_officer_id' => 'required|integer|exists:users,id',
            'entries' => 'required|array|min:1',
            'entries.*.savings_id' => 'required|integer|exists:savings,id',
            'entries.*.transaction_type' => 'required|in:deposit,withdrawal',
            'entries.*.amount' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            // Create bulk entry
            $bulkEntry = BulkSavingsEntry::create([
                'savings_officer_id' => $request->savings_officer_id,
                'created_by_user_id' => Auth::id(),
                'status' => 'pending'
            ]);

            // Create entry items
            foreach ($request->entries as $entry) {
                $saving = Savings::findOrFail($entry['savings_id']);
                
                BulkSavingsEntryItem::create([
                    'bulk_savings_entry_id' => $bulkEntry->id,
                    'savings_id' => $entry['savings_id'],
                    'client_id' => $saving->client_id,
                    'transaction_type' => $entry['transaction_type'],
                    'amount' => $entry['amount'],
                    'notes' => $entry['notes'] ?? null
                ]);
            }

            DB::commit();

            Flash::success('Bulk entry submitted for verification');
            return redirect()->route('bulk_entry.show', $bulkEntry->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Flash::error('Error creating bulk entry: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show verification page for Savings Operator
     */
    public function verify($id)
    {
        $entry = BulkSavingsEntry::with(['items', 'savings_officer', 'created_by'])->findOrFail($id);
        
        if ($entry->status !== 'pending') {
            Flash::warning('This entry has already been ' . $entry->status);
            return redirect()->route('bulk_entry.show', $id);
        }

        $stats = $entry->getStats();
        
        return theme_view('savings::bulk_entry.verify', compact('entry', 'stats'));
    }

    /**
     * Verify and process all entries
     */
    public function verify_entries(Request $request, $id)
    {
        $entry = BulkSavingsEntry::findOrFail($id);

        if ($entry->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Entry already processed'], 400);
        }

        try {
            DB::beginTransaction();

            // Process each item
            foreach ($entry->items as $item) {
                $this->createSavingsTransaction($item);
            }

            // Mark as verified
            $entry->update([
                'status' => 'verified',
                'verified_by_user_id' => Auth::id(),
                'verified_at' => now()
            ]);

            DB::commit();

            Flash::success('Bulk entry verified and processed successfully');
            return redirect()->route('bulk_entry.show', $id);

        } catch (\Exception $e) {
            DB::rollBack();
            Flash::error('Error verifying entries: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Reject entries
     */
    public function reject_entries(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $entry = BulkSavingsEntry::findOrFail($id);

        if ($entry->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Entry already processed'], 400);
        }

        $entry->update([
            'status' => 'rejected',
            'verified_by_user_id' => Auth::id(),
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now()
        ]);

        Flash::warning('Bulk entry rejected');
        return redirect()->route('bulk_entry.show', $id);
    }

    /**
     * Get savings balance
     */
    private function getSavingsBalance($savings_id)
    {
        $balance = DB::table('savings_transactions')
            ->where('savings_id', $savings_id)
            ->selectRaw('(COALESCE(SUM(credit), 0) - COALESCE(SUM(debit), 0)) as balance')
            ->first();

        return $balance->balance ?? 0;
    }

    /**
     * Create a savings transaction from entry item
     */
    private function createSavingsTransaction($item)
    {
        $saving = $item->savings;

        if ($item->transaction_type === 'deposit') {
            // Create deposit transaction
            $transaction = new SavingsTransaction();
            $transaction->savings_id = $item->savings_id;
            $transaction->credit = $item->amount;
            $transaction->debit = 0;
            $transaction->created_by_id = Auth::id();
            $transaction->save();

            $item->update(['savings_transaction_id' => $transaction->id]);

        } elseif ($item->transaction_type === 'withdrawal') {
            // Create withdrawal transaction
            $transaction = new SavingsTransaction();
            $transaction->savings_id = $item->savings_id;
            $transaction->credit = 0;
            $transaction->debit = $item->amount;
            $transaction->created_by_id = Auth::id();
            $transaction->save();

            $item->update(['savings_transaction_id' => $transaction->id]);
        }
    }
}
