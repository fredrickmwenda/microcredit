<?php

namespace Modules\Loan\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Branch\Entities\Branch;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanProduct;
use Modules\Loan\Exports\LoanExport;
use Modules\User\Entities\User;
use PDF;
use Yajra\DataTables\Facades\DataTables;


class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:loan.loans.reports.repayments'])->only(['repayment']);
        $this->middleware(['permission:loan.loans.reports.collection_sheet'])->only(['collection_sheet']);
        $this->middleware(['permission:loan.loans.reports.expected_repayments'])->only(['expected_repayment']);
        $this->middleware(['permission:loan.loans.reports.arrears'])->only(['arrears']);
        $this->middleware(['permission:loan.loans.reports.disbursements'])->only(['disbursement']);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {  
        return theme_view('loan::report.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function collection_sheet(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $loan_product_id = $request->loan_product_id;
        $loan_officer_id = $request->loan_officer_id;
        // $users = User::whereHas('roles', function ($query) {
        //     return $query->where('name', '!=', 'client');
        // })->get();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['client', 'admin']);
        })->get();
        $data = [];
        $branches = Branch::all();
        $loan_products = LoanProduct::all();
        if (!empty($start_date)) {
            $data = DB::table("loan_repayment_schedules")
                ->join("loans", "loan_repayment_schedules.loan_id", "loans.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_repayment_schedules.due_date', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                    $query->where('loans.loan_officer_id', $loan_officer_id);
                })
                ->when($loan_product_id, function ($query) use ($loan_product_id) {
                    $query->where('loans.loan_product_id', $loan_product_id);
                })
                ->where('loans.status', 'active')
                ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) loan_officer,branches.name branch,clients.mobile,loans.client_id,loan_products.name loan_product,loan_repayment_schedules.loan_id,loans.expected_maturity_date,loan_repayment_schedules.total_due,(loan_repayment_schedules.principal+loan_repayment_schedules.interest+loan_repayment_schedules.fees+loan_repayment_schedules.penalties-loan_repayment_schedules.principal_written_off_derived-loan_repayment_schedules.interest_written_off_derived-loan_repayment_schedules.fees_written_off_derived-loan_repayment_schedules.penalties_written_off_derived-loan_repayment_schedules.interest_waived_derived-loan_repayment_schedules.fees_waived_derived-loan_repayment_schedules.penalties_waived_derived) expected_amount,loan_repayment_schedules.due_date")
                ->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.collection_sheet_pdf'), compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches', 'users', 'loan_officer_id', 'loan_product_id', 'loan_products'));
                    return $pdf->download(trans_choice('loan::general.collection_sheet', 1) . '(' . $start_date . ' to ' . $end_date . ').pdf');
                }
                $view = theme_view('loan::report.collection_sheet_pdf',
                    compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches', 'users', 'loan_officer_id', 'loan_product_id', 'loan_products'));
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.collection_sheet', 1) . '(' . $start_date . ' to ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.collection_sheet', 1) . '(' . $start_date . ' to ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.collection_sheet', 1) . '(' . $start_date . ' to ' . $end_date . ').csv');
                }
            }
        }
        return theme_view('loan::report.collection_sheet',
            compact('start_date',
                'end_date', 'branch_id', 'data', 'branches', 'users', 'loan_officer_id', 'loan_product_id', 'loan_products'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function repayment(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        // $users = User::whereHas('roles', function ($query) {
        //     return $query->where('name', '!=', 'client');
        // })->get();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['client', 'admin']);
        })->get();
        $data = [];
        $branches = Branch::all();
        if (!empty($start_date)) {
            $data = DB::table("loan_transactions")
                ->join("loans", "loan_transactions.loan_id", "loans.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                ->leftJoin("payment_details", "loan_transactions.payment_detail_id", "payment_details.id")
                ->leftJoin("payment_types", "payment_details.payment_type_id", "payment_types.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_transactions.submitted_on', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->where('loan_transaction_type_id', 2)
                ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) loan_officer,branches.name branch,loans.client_id,loan_transactions.id,loan_transactions.loan_id,loan_transactions.principal_repaid_derived,loan_transactions.interest_repaid_derived,loan_transactions.fees_repaid_derived,loan_transactions.penalties_repaid_derived,loan_transactions.submitted_on,payment_types.name payment_type")
                ->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.repayment_pdf'), compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches'));
                    return $pdf->download(trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').pdf');
                }
                $view = theme_view('loan::report.repayment_pdf',
                    compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches'));
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').csv');
                }
            }
        }
        return theme_view('loan::report.repayment',
            compact('start_date',
                'end_date', 'branch_id', 'data', 'branches'));
    }

    public function expected_repayment(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $data = [];
        $branches = Branch::all();
        if (!empty($start_date)) {
            $data = DB::table("loan_repayment_schedules")
                ->join("loans", "loan_repayment_schedules.loan_id", "loans.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_repayment_schedules.due_date', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->where('loans.status', 'active')
                ->selectRaw("branches.name branch,loans.branch_id,coalesce(sum(loan_repayment_schedules.principal-loan_repayment_schedules.principal_written_off_derived),0) principal,coalesce(sum(loan_repayment_schedules.interest-loan_repayment_schedules.interest_written_off_derived-loan_repayment_schedules.interest_waived_derived),0) interest,coalesce(sum(loan_repayment_schedules.fees-loan_repayment_schedules.fees_written_off_derived-loan_repayment_schedules.fees_waived_derived),0) fees,coalesce(sum(loan_repayment_schedules.penalties-loan_repayment_schedules.penalties_written_off_derived-loan_repayment_schedules.penalties_waived_derived),0) penalties,coalesce(sum(loan_repayment_schedules.principal_repaid_derived),0) principal_repaid_derived,coalesce(sum(loan_repayment_schedules.interest_repaid_derived),0) interest_repaid_derived,coalesce(sum(loan_repayment_schedules.fees_repaid_derived),0) fees_repaid_derived,coalesce(sum(loan_repayment_schedules.penalties_repaid_derived),0) penalties_repaid_derived")
                ->groupBy('branches.id')
                ->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.expected_repayment_pdf'), compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches'));
                    return $pdf->download(trans_choice('loan::general.expected', 1) . ' ' . trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').pdf');
                }
                $view = theme_view('loan::report.expected_repayment_pdf',
                    compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches'));
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.expected', 1) . ' ' . trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.expected', 1) . ' ' . trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.expected', 1) . ' ' . trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').csv');
                }
            }
        }
       // dd($data);
        return theme_view('loan::report.expected_repayment',
            compact('start_date',
                'end_date', 'branch_id', 'data', 'branches'));
    }


    public function specific_expected_repayment(Request $request)
    {
        $start_date = $request->start_date ?? date('Y-m-d');
        $branch_id = $request->branch_id;
        $data = [];
        $branches = Branch::all();
    
        if (!empty($start_date)) {
            $data = DB::table("loan_repayment_schedules")
            ->join("loans", "loan_repayment_schedules.loan_id", "=", "loans.id")
            ->join("branches", "loans.branch_id", "=", "branches.id")
            ->join("clients", "loans.client_id", "=", "clients.id")
            ->when($start_date, function ($query) use ($start_date) {
                $query->where('loan_repayment_schedules.due_date', $start_date);
            })
            ->when($branch_id, function ($query) use ($branch_id) {
                $query->where('loans.branch_id', $branch_id);
            })
            ->where('loans.status', 'active')
            ->selectRaw("
                clients.id as id,
                clients.first_name,
                clients.middle_name,
                clients.last_name,
                clients.mobile,
                branches.name as branch,
                loans.branch_id,
                coalesce(sum(loan_repayment_schedules.principal-loan_repayment_schedules.principal_written_off_derived), 0) as principal,
                coalesce(sum(loan_repayment_schedules.interest-loan_repayment_schedules.interest_written_off_derived-loan_repayment_schedules.interest_waived_derived), 0) as interest,
                coalesce(sum(loan_repayment_schedules.fees-loan_repayment_schedules.fees_written_off_derived-loan_repayment_schedules.fees_waived_derived), 0) as fees,
                coalesce(sum(loan_repayment_schedules.penalties-loan_repayment_schedules.penalties_written_off_derived-loan_repayment_schedules.penalties_waived_derived), 0) as penalties,
                coalesce(sum(loan_repayment_schedules.principal_repaid_derived), 0) as principal_repaid_derived,
                coalesce(sum(loan_repayment_schedules.interest_repaid_derived), 0) as interest_repaid_derived,
                coalesce(sum(loan_repayment_schedules.fees_repaid_derived), 0) as fees_repaid_derived,
                coalesce(sum(loan_repayment_schedules.penalties_repaid_derived), 0) as penalties_repaid_derived
            ")
            ->groupBy('clients.id', 'clients.first_name', 'clients.middle_name', 'clients.last_name', 'clients.mobile', 'branches.name', 'loans.branch_id')
            ->get();
        
        
    
            // Check if we should download
            if ($request->download) {
                $view = theme_view('loan::report.specific_expected_repayment_pdf', compact('start_date', 'branch_id', 'data', 'branches'));
    
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView($view);
                    return $pdf->download(trans_choice('loan::general.expected', 1) . ' ' . trans_choice('loan::general.repayment', 1) . '(' . $start_date . ').pdf');
                }
                
                $exportType = [
                    'excel_2007' => 'xlsx',
                    'excel' => 'xls',
                    'csv' => 'csv'
                ][$request->type] ?? null;
                
                if ($exportType) {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.expected', 1) . ' ' . trans_choice('loan::general.repayment', 1) . '(' . $start_date . ').' . $exportType);
                }
            }
        }
    
        return theme_view('loan::report.specific_expected_repayment', compact('start_date', 'branch_id', 'data', 'branches'));
    }
    


    public function arrears(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $data = [];
        $branches = Branch::all();
        if (!empty($end_date)) {
            $data = Loan::with("repayment_schedules")
                ->join(DB::raw("(select*from loan_repayment_schedules where loan_repayment_schedules.due_date<'$end_date' and total_due>0) loan_repayment_schedules"), "loan_repayment_schedules.loan_id", "loans.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->where('loans.status', 'active')
                ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,clients.mobile,concat(users.first_name,' ',users.last_name) loan_officer,branches.name branch,clients.mobile,loans.client_id,loan_products.name loan_product,loans.expected_maturity_date,loans.disbursed_on_date,loans.id,(SELECT submitted_on FROM loan_transactions WHERE loan_id=loans.id ORDER BY submitted_on DESC LIMIT 1) last_payment_date,loans.principal")
                ->groupBy('loans.id')
                ->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.arrears_pdf'), compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches'))->setPaper('A4', 'landscape');
                    return $pdf->download(trans_choice('loan::general.arrears', 1) . '( as at ' . $end_date . ').pdf');
                }
                $view = theme_view('loan::report.arrears_pdf',
                    compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches'));
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.arrears', 1) . '(as at ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.arrears', 1) . '(as at ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.arrears', 1) . '(as at' . $end_date . ').csv');
                }
            }
        }
        return theme_view('loan::report.arrears',
            compact('start_date',
                'end_date', 'branch_id', 'data', 'branches'));
    }

    public function disbursement(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $branch_id = $request->branch_id;
        $loan_product_id = $request->loan_product_id;
        $status = $request->status;
        $loan_officer_id = $request->loan_officer_id;
        // $users = User::whereHas('roles', function ($query) {
        //     return $query->where('name', '!=', 'client');
        // })->get();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['client', 'admin']);
        })->get();
        $loan_products = LoanProduct::all();
        $data = [];
        $branches = Branch::all();
        if (!empty($start_date)) {
            $data = Loan::with("repayment_schedules")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("funds", "loans.fund_id", "funds.id")
                ->join("loan_purposes", "loans.loan_purpose_id", "loan_purposes.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loans.disbursed_on_date', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                    $query->where('loans.loan_officer_id', $loan_officer_id);
                })
                ->when($loan_product_id, function ($query) use ($loan_product_id) {
                    $query->where('loans.loan_product_id', $loan_product_id);
                })
                ->when($status, function ($query) use ($status) {
                    $query->where('loans.status', $status);
                })
                ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,clients.gender,clients.dob,clients.mobile,concat(users.first_name,' ',users.last_name) loan_officer,loan_purposes.name loan_purpose,funds.name fund,branches.name branch,clients.mobile,loans.client_id,loan_products.name loan_product,loans.expected_maturity_date,loans.disbursed_on_date,loans.id,loans.principal,loans.status,loans.repayment_frequency,loans.repayment_frequency_type")
                ->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.disbursement_pdf'), compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches', 'loan_officer_id', 'loan_product_id', 'loan_products', 'users', 'status'))->setPaper('A4', 'landscape');
                    return $pdf->download(trans_choice('loan::general.disbursement', 1) . '(' . $start_date . '  to ' . $end_date . ').pdf');
                }
                $view = theme_view('loan::report.arrears_pdf',
                    compact('start_date',
                        'end_date', 'branch_id', 'data', 'branches', 'loan_officer_id', 'loan_product_id', 'loan_products', 'users', 'status'));
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.disbursement', 1) . '(' . $start_date . ' to ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.disbursement', 1) . '(' . $start_date . ' to ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.disbursement', 1) . '(' . $start_date . ' to ' . $end_date . ').csv');
                }
            }
        }
        return theme_view('loan::report.disbursement',
            compact('start_date',
                'end_date', 'branch_id', 'data', 'branches', 'loan_officer_id', 'loan_product_id', 'loan_products', 'users', 'status'));
    }


    

    public function dailyLoanCollectionReport(Request $request)
    {
        if ($request->ajax()) {
            // For modal details when clicking "View More" on a specific officer
            if ($request->has('officer_id') && !$request->has('draw')) {
                $officerId = $request->officer_id;
                $start_date = $request->start_date;
                $end_date = $request->end_date;
                $branch_id = $request->branch_id;
                $loan_product_id = $request->loan_product_id;

                // Disbursements (loans) for this officer
                $disbursements = DB::table('loans')
                    ->select('id', 'loan_officer_id', 'branch_id', 'loan_product_id', 'principal', 'disbursed_on_date', 'client_id')
                    ->where('loan_officer_id', $officerId)
                    ->whereNotNull('disbursed_on_date')
                    ->when($start_date, fn($q) => $q->whereDate('disbursed_on_date', '>=', $start_date))
                    ->when($end_date, fn($q) => $q->whereDate('disbursed_on_date', '<=', $end_date))
                    ->when($branch_id, fn($q) => $q->where('branch_id', $branch_id))
                    ->when($loan_product_id, fn($q) => $q->where('loan_product_id', $loan_product_id))
                    ->with(['client:id,first_name,last_name', 'loanProduct:id,name'])
                    ->get();

                // Collections (repayments) for loans of this officer
                $collections = DB::table('loan_transactions')
                    ->join('loans', 'loan_transactions.loan_id', '=', 'loans.id')
                    ->select('loan_transactions.*', 'loans.loan_officer_id')
                    ->where('loans.loan_officer_id', $officerId)
                    ->where('loan_transactions.loan_transaction_type_id', 2) // repayment
                    ->where('loan_transactions.reversed', 0)
                    ->when($start_date, fn($q) => $q->whereDate('loan_transactions.submitted_on', '>=', $start_date))
                    ->when($end_date, fn($q) => $q->whereDate('loan_transactions.submitted_on', '<=', $end_date))
                    ->when($branch_id, fn($q) => $q->where('loans.branch_id', $branch_id))
                    ->when($loan_product_id, fn($q) => $q->where('loans.loan_product_id', $loan_product_id))
                    ->with(['loan.client:id,first_name,last_name', 'loan.loanProduct:id,name'])
                    ->get();

                return response()->json([
                    'disbursements' => $disbursements,
                    'collections' => $collections,
                ]);
            }

            // Main DataTable response – grouped by loan officer
            $start_date = $request->start_date;
            $end_date   = $request->end_date;
            $branch_id  = $request->branch_id;
            $loan_officer_id = $request->loan_officer_id;
            $loan_product_id = $request->loan_product_id;

            // 1. Get all loan officer IDs that have either disbursements or collections within the period
            $disbursementOfficers = DB::table('loans')
                ->select('loan_officer_id')
                ->whereNotNull('disbursed_on_date')
                ->when($start_date, fn($q) => $q->whereDate('disbursed_on_date', '>=', $start_date))
                ->when($end_date, fn($q) => $q->whereDate('disbursed_on_date', '<=', $end_date))
                ->when($branch_id, fn($q) => $q->where('branch_id', $branch_id))
                ->when($loan_product_id, fn($q) => $q->where('loan_product_id', $loan_product_id))
                ->distinct();

            $collectionOfficers = DB::table('loan_transactions')
                ->join('loans', 'loan_transactions.loan_id', '=', 'loans.id')
                ->select('loans.loan_officer_id')
                ->where('loan_transactions.loan_transaction_type_id', 2)
                ->where('loan_transactions.reversed', 0)
                ->when($start_date, fn($q) => $q->whereDate('loan_transactions.submitted_on', '>=', $start_date))
                ->when($end_date, fn($q) => $q->whereDate('loan_transactions.submitted_on', '<=', $end_date))
                ->when($branch_id, fn($q) => $q->where('loans.branch_id', $branch_id))
                ->when($loan_product_id, fn($q) => $q->where('loans.loan_product_id', $loan_product_id))
                ->distinct();

            $officerIds = $disbursementOfficers->union($collectionOfficers)->pluck('loan_officer_id');

            // If no officers found, return empty DataTable
            if ($officerIds->isEmpty()) {
                return DataTables::of(collect([]))
                    ->with(['totals' => (object)[
                        'total_loans_issued' => 0,
                        'total_disbursed' => 0,
                        'total_collection_count' => 0,
                        'total_collected' => 0,
                        'net_collection_total' => 0,
                    ]])
                    ->make(true);
            }

            // 2. Subquery for disbursements per officer (only among those IDs)
            $disbursements = DB::table('loans')
                ->selectRaw('loan_officer_id, COUNT(*) as loans_issued, SUM(principal) as total_disbursed')
                ->whereIn('loan_officer_id', $officerIds)
                ->whereNotNull('disbursed_on_date')
                ->when($start_date, fn($q) => $q->whereDate('disbursed_on_date', '>=', $start_date))
                ->when($end_date, fn($q) => $q->whereDate('disbursed_on_date', '<=', $end_date))
                ->when($branch_id, fn($q) => $q->where('branch_id', $branch_id))
                ->when($loan_product_id, fn($q) => $q->where('loan_product_id', $loan_product_id))
                ->groupBy('loan_officer_id');

            // 3. Subquery for collections per officer (only among those IDs)
            $collections = DB::table('loan_transactions')
                ->join('loans', 'loan_transactions.loan_id', '=', 'loans.id')
                ->selectRaw('loans.loan_officer_id, COUNT(*) as collection_count, SUM(loan_transactions.amount) as total_collected')
                ->whereIn('loans.loan_officer_id', $officerIds)
                ->where('loan_transactions.loan_transaction_type_id', 2)
                ->where('loan_transactions.reversed', 0)
                ->when($start_date, fn($q) => $q->whereDate('loan_transactions.submitted_on', '>=', $start_date))
                ->when($end_date, fn($q) => $q->whereDate('loan_transactions.submitted_on', '<=', $end_date))
                ->when($branch_id, fn($q) => $q->where('loans.branch_id', $branch_id))
                ->when($loan_product_id, fn($q) => $q->where('loans.loan_product_id', $loan_product_id))
                ->groupBy('loans.loan_officer_id');

            // 4. Combine with users table to get officer names
            $query = DB::table('users')
                ->joinSub($disbursements, 'disb', 'users.id', '=', 'disb.loan_officer_id', 'left')
                ->joinSub($collections, 'coll', 'users.id', '=', 'coll.loan_officer_id', 'left')
                ->select(
                    'users.id as officer_id',
                    DB::raw("CONCAT(users.first_name, ' ', users.last_name) as officer_name"),
                    DB::raw("COALESCE(disb.loans_issued, 0) as loans_issued"),
                    DB::raw("COALESCE(disb.total_disbursed, 0) as total_disbursed"),
                    DB::raw("COALESCE(coll.collection_count, 0) as collection_count"),
                    DB::raw("COALESCE(coll.total_collected, 0) as total_collected"),
                    DB::raw("COALESCE(coll.total_collected, 0) - COALESCE(disb.total_disbursed, 0) as net_collection")
                )
                ->whereIn('users.id', $officerIds)
                ->when($loan_officer_id, fn($q) => $q->where('users.id', $loan_officer_id))
                ->orderBy('officer_name');

            // 5. Totals across all displayed officers
            $totals = (object)[
                'total_loans_issued'    => $query->sum('loans_issued'),
                'total_disbursed'       => $query->sum('total_disbursed'),
                'total_collection_count'=> $query->sum('collection_count'),
                'total_collected'       => $query->sum('total_collected'),
                'net_collection_total'  => $query->sum('net_collection'),
            ];

            return DataTables::of($query)
                ->editColumn('total_disbursed', fn($row) => number_format($row->total_disbursed, 2))
                ->editColumn('total_collected', fn($row) => number_format($row->total_collected, 2))
                ->editColumn('net_collection', fn($row) => number_format($row->net_collection, 2))
                ->addColumn('action', function($row) {
                    return '<button class="btn btn-sm btn-info view-more-btn" data-officer-id="'.$row->officer_id.'" data-officer-name="'.$row->officer_name.'">View More</button>';
                })
                ->with(['totals' => $totals])
                ->rawColumns(['action'])
                ->make(true);
        }

        // For initial page load: get filter options
        $branches       = Branch::all();
        $loan_products  = LoanProduct::all();
        $loan_officers  = User::whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['client', 'admin']))->get();

        return theme_view('loan::report.daily_loan_collection', compact('branches', 'loan_products', 'loan_officers'));
    }
}
