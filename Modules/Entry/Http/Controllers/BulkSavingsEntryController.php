<?php

namespace Modules\Entry\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Modules\Entry\Exports\BulkSavingsEntryExport;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laracasts\Flash\Flash;
use Modules\Client\Entities\Client;
use Modules\Entry\Entities\BulkSavingsEntry;
use Modules\Entry\Entities\BulkSavingsEntryItem;
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
        $this->middleware(['permission:entry.savings_bulk_entry.index'])->only(['index', 'show']);
        $this->middleware(['permission:entry.savings_bulk_entry.create'])->only(['create', 'store']);
        $this->middleware(['permission:entry.savings_bulk_entry.verify'])->only(['verify', 'verify_entries', 'reject_entries']);
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
        $date = $request->date;

        $query = BulkSavingsEntry::with(['savings_officer', 'created_by', 'verified_by'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($created_by, function ($query) use ($created_by) {
                $query->where('created_by_user_id', $created_by);
            })
            ->when($officer_id, function ($query) use ($officer_id) {
                $query->where('savings_officer_id', $officer_id);
            });

        // Filter by date (default today)
        if ($date) {
            $query->whereDate('created_at', $date);
        } else {
            $query->whereDate('created_at', Carbon::today());
        }

        $data = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->input());

        return theme_view('entry::bulk_entry.index', compact('data'));
    }

    /**
     * Export bulk entries
     */
    public function export(Request $request)
    {
        $status = $request->status;
        $created_by = $request->created_by;
        $officer_id = $request->officer_id;
        $date = $request->date;
        $format = $request->format ?? 'xlsx';

        $query = BulkSavingsEntry::with(['savings_officer', 'created_by', 'verified_by'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($created_by, function ($query) use ($created_by) {
                $query->where('created_by_user_id', $created_by);
            })
            ->when($officer_id, function ($query) use ($officer_id) {
                $query->where('savings_officer_id', $officer_id);
            });

        if ($date) {
            $query->whereDate('created_at', $date);
        } else {
            $query->whereDate('created_at', Carbon::today());
        }

        $export = new BulkSavingsEntryExport($query);
        $filename = 'bulk_savings_entries_' . ($date ?? Carbon::today()->toDateString()) . '.' . $format;

        if ($format === 'csv') {
            return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::CSV);
        }
        return Excel::download($export, $filename);
    }

    /**
     * Show entry details
     */
    public function show($id)
    {
        $entry = BulkSavingsEntry::with(['items', 'savings_officer', 'created_by', 'verified_by'])->findOrFail($id);
        $stats = $entry->getStats();
        
        return theme_view('entry::bulk_entry.show', compact('entry', 'stats'));
    }

    /**
     * Show form to select officer and create new entry
     */
    public function create(Request $request)
    {
        

        // Get savings officers who have created savings but are not in any roles
        $savings_officers = User::whereIn('id', function ($query) {
            $query->select('savings_officer_id')
                ->from('savings')
                ->whereNotNull('savings_officer_id')
                ->distinct();
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

        return theme_view('entry::bulk_entry.create', compact('savings_officers', 'selected_officer_id', 'clients', 'savings_data'));
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
            'entries.*.amount' => 'required|numeric|min:0.01',
        ]);

        // Get transaction type from the request (from the tab)
        $transaction_type = $request->input('entries.0.transaction_type', $request->input('transaction_type', 'deposit'));

        try {
            DB::beginTransaction();

            // Create bulk entry
            $bulkEntry = BulkSavingsEntry::create([
                'savings_officer_id' => $request->savings_officer_id,
                'created_by_user_id' => Auth::id(),
                'status' => 'pending'
            ]);

            // Only save entries with amount > 0
            foreach ($request->entries as $entry) {
                if (!isset($entry['amount']) || floatval($entry['amount']) <= 0) continue;
                $saving = Savings::findOrFail($entry['savings_id']);
                BulkSavingsEntryItem::create([
                    'bulk_savings_entry_id' => $bulkEntry->id,
                    'savings_id' => $entry['savings_id'],
                    'client_id' => $saving->client_id,
                    'transaction_type' => $transaction_type,
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
        
        return theme_view('entry::bulk_entry.verify', compact('entry', 'stats'));
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
        $today = now()->toDateString();
        $year = now()->format('Y');
        $month = now()->format('m');

        if ($item->transaction_type === 'deposit') {
            // Create deposit transaction - adds to savings
            $transaction = new SavingsTransaction();
            $transaction->savings_id = $item->savings_id;
            $transaction->created_by_id = Auth::id();
            $transaction->credit = $item->amount;  // Deposit adds credit
            $transaction->debit = 0;
            $transaction->amount = $item->amount;
            $transaction->savings_transaction_type_id = 1;
            $transaction->created_on = $today;
            $transaction->submitted_on = $today;
            $transaction->status = 'approved';  // Auto-approved by bulk entry verification
            $transaction->description = 'Bulk Entry Deposit';
            $transaction->reference = 'BULK-ENTRY-' . $item->bulk_savings_entry_id . '-' . $item->id;
            $transaction->remarks = $item->notes ?? 'Bulk savings deposit from verified entry';
            $transaction->reversible = 1;
            $transaction->save();

            $item->update(['savings_transaction_id' => $transaction->id]);

        } elseif ($item->transaction_type === 'withdrawal') {
            // Create withdrawal transaction - deducts from savings
            $transaction = new SavingsTransaction();
            $transaction->savings_id = $item->savings_id;
            $transaction->created_by_id = Auth::id();
            $transaction->credit = 0;
            $transaction->debit = $item->amount;  // Withdrawal is debit
            $transaction->amount = $item->amount;
            $transaction->savings_transaction_type_id = 2;
            $transaction->created_on = $today;
            $transaction->submitted_on = $today; 
            $transaction->status = 'approved';  // Auto-approved by bulk entry verification
            $transaction->description = 'Bulk Entry Withdrawal';
            $transaction->reference = 'BULK-ENTRY-' . $item->bulk_savings_entry_id . '-' . $item->id;
            $transaction->remarks = $item->notes ?? 'Bulk savings withdrawal from verified entry';
            $transaction->reversible = 1;
            $transaction->save();

            $item->update(['savings_transaction_id' => $transaction->id]);
        }
    }
}
