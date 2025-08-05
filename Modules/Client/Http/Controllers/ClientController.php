<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laracasts\Flash\Flash;
use Modules\Branch\Entities\Branch;
use Modules\Client\Entities\Client;
use Modules\Client\Entities\ClientType;
use Modules\Client\Entities\ClientUser;
use Modules\Client\Entities\ClientGroup;
use Modules\Client\Entities\Profession;
use Modules\Client\Entities\Title;
use Modules\Core\Entities\Country;
use Modules\CustomField\Entities\CustomField;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Client\Entities\Blacklist;
use Modules\Client\Entities\ClientImport;
use Modules\Communication\Entities\SmsGateway;
use Modules\Communication\Entities\CommunicationLog;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:client.clients.index'])->only(['index', 'show', 'get_clients']);
        $this->middleware(['permission:client.clients.create'])->only(['create', 'store']);
        $this->middleware(['permission:client.clients.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:client.clients.destroy'])->only(['destroy']);
        $this->middleware(['permission:client.clients.user.create'])->only(['store_user', 'create_user']);
        $this->middleware(['permission:client.clients.user.destroy'])->only(['destroy_user']);
        $this->middleware(['permission:client.clients.activate'])->only(['change_status']);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $orderByDir = $request->order_by_dir;
        $search = $request->s;
        $status = $request->status;
        $client_group_id = $request->client_group_id;
        $client_groups = ClientGroup::all();
        $data = Client::leftJoin("branches", "branches.id", "clients.branch_id")
            ->leftJoin("users", "users.id", "clients.loan_officer_id")
            ->leftJoin("client_groups", "clients.client_group_id", "client_groups.id")
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('clients.first_name', 'like', "%$search%");
                $query->orWhere('clients.last_name', 'like', "%$search%");
                $query->orWhere('clients.account_number', 'like', "%$search%");
                $query->orWhere('clients.mobile', 'like', "%$search%");
                $query->orWhere('clients.external_id', 'like', "%$search%");
                $query->orWhere('clients.email', 'like', "%$search%");
            })
            ->when($status, function ($query) use ($status) {
                $query->where('clients.status', $status);
            })
            ->when($client_group_id, function ($query) use ($client_group_id) {
                $query->where('clients.client_group_id', $client_group_id);
            });

        $data = $data->selectRaw("branches.name branch,concat(users.first_name,' ',users.last_name) staff,clients.id,client_group_id,client_groups.name client_group_name,clients.loan_officer_id,clients.first_name,clients.last_name,clients.gender,clients.mobile,clients.email,clients.external_id,clients.status")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('client::client.index', compact('data', 'client_groups'));
    }

    public function get_clients(Request $request)
    {

        $status = $request->status;
        $query = DB::table("clients")
            ->leftJoin("branches", "branches.id", "clients.branch_id")
            ->leftJoin("users", "users.id", "clients.loan_officer_id")
            ->selectRaw("branches.name branch,concat(users.first_name,' ',users.last_name) staff,clients.id,clients.loan_officer_id,concat(clients.first_name,' ',clients.last_name) name,clients.gender,clients.mobile,clients.email,clients.external_id,clients.status")
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            });
        return DataTables::of($query)->editColumn('staff', function ($data) {
            return $data->staff;
        })->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            $action .= '<li><a href="' . url('client/' . $data->id . '/show') . '" class="">' . trans_choice('user::general.detail', 2) . '</a></li>';
            if (Auth::user()->hasPermissionTo('client.clients.edit')) {
                $action .= '<li><a href="' . url('client/' . $data->id . '/edit') . '" class="">' . trans_choice('user::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('client.clients.destroy')) {
                $action .= '<li><a href="' . url('client/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('user::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('client/' . $data->id . '/show') . '">' . $data->id . '</a>';
        })->editColumn('name', function ($data) {
            return '<a href="' . url('client/' . $data->id . '/show') . '">' . $data->name . '</a>';
        })->editColumn('gender', function ($data) {
            if ($data->gender == "male") {
                return trans_choice('core::general.male', 1);
            }
            if ($data->gender == "female") {
                return trans_choice('core::general.female', 1);
            }
            if ($data->gender == "other") {
                return trans_choice('core::general.other', 1);
            }
            if ($data->gender == "unspecified") {
                return trans_choice('core::general.unspecified', 1);
            }
        })->editColumn('status', function ($data) {
            if ($data->status == "pending") {
                return trans_choice('core::general.pending', 1);
            }
            if ($data->status == "active") {
                return trans_choice('core::general.active', 1);
            }
            if ($data->status == "inactive") {
                return trans_choice('core::general.inactive', 1);
            }
            if ($data->gender == "deceased") {
                return trans_choice('client::general.deceased', 1);
            }
            if ($data->gender == "unspecified") {
                return trans_choice('core::general.unspecified', 1);
            }
        })->rawColumns(['id', 'name', 'action'])->make(true);
    }

    public function getDormantClients(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $status = $request->status;
        $dormant_duration = $request->dormant_duration;
        $dormant_clients = DB::table("clients")
            ->leftJoin("branches", "branches.id", "clients.branch_id")
            ->leftJoin("users", "users.id", "clients.loan_officer_id")
            ->leftJoin("client_groups", "clients.client_group_id", "client_groups.id")
            ->selectRaw("branches.name branch,concat(users.first_name,' ',users.last_name) staff,clients.id,clients.id,client_group_id,client_groups.name client_group_name,clients.loan_officer_id,concat(clients.first_name,' ',clients.last_name) name,clients.gender,clients.mobile,clients.email,clients.external_id,clients.status")
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })->get();

        $data = array();
        foreach ($dormant_clients as $key => $client) {
            $clientLastLoanTransaction = DB::table('loans')
                ->leftJoin("loan_transactions", "loans.id", "loan_transactions.loan_id")
                ->selectRaw("loan_transactions.id,loan_transactions.name,loan_transactions.amount,loan_transactions.debit,loan_transactions.credit,loan_transactions.submitted_on")
                ->where("loan_transactions.id", "!=", null)
                ->where("loans.client_id", "=", $client->id)
                ->orderBy("loan_transactions.submitted_on", 'desc')
                ->first();
            $clientLastSavingTransaction = DB::table('savings')
                ->leftJoin("savings_transactions", "savings.id", "savings_transactions.savings_id")
                ->selectRaw("savings_transactions.id,savings_transactions.name,savings_transactions.amount,savings_transactions.debit,savings_transactions.credit,savings_transactions.submitted_on")
                ->where("savings_transactions.id", "!=", null)
                ->where("savings_transactions.id", "!=", null)
                ->where("savings.client_id", "=", $client->id)
                ->orderBy("savings_transactions.submitted_on", 'desc')
                ->first();
            $clientLastShareTransaction = DB::table('shares')
                ->leftJoin("share_transactions", "shares.id", "share_transactions.share_id")
                ->selectRaw("share_transactions.id,share_transactions.name,share_transactions.amount,share_transactions.debit,share_transactions.credit,share_transactions.submitted_on")
                ->where("share_transactions.id", "!=", null)
                ->where("shares.client_id", "=", $client->id)
                ->orderBy("share_transactions.submitted_on", 'desc')
                ->first();
            $dormant_clients[$key]->last_transaction_date = $clientLastSavingTransaction ? $clientLastSavingTransaction->submitted_on : 'N/A';
            $dormant_clients[$key]->amount = $clientLastSavingTransaction ? $clientLastSavingTransaction->amount : 'N/A';
            $dormant_clients[$key]->transaction_name = $clientLastSavingTransaction ? $clientLastSavingTransaction->name : "N/A";
            if ($clientLastSavingTransaction) {
                $now = time();
                $your_date = strtotime($dormant_clients[$key]->last_transaction_date);
                $dateDiff = $now - $your_date;
                $dormant_clients[$key]->dormant_duration = round($dateDiff / (60 * 60 * 24));
            } else {
                $dormant_clients[$key]->dormant_duration = "N/A";
            }

            if ($dormant_duration == "") {
                array_push($data, $dormant_clients[$key]);
            } elseif ($dormant_duration == "N/A" && $dormant_clients[$key]->dormant_duration == "N/A") {
                array_push($data, $dormant_clients[$key]);
            } elseif ($dormant_duration == 30 && $dormant_clients[$key]->dormant_duration > 30) {
                array_push($data, $dormant_clients[$key]);
            } elseif ($dormant_duration == 60 && $dormant_clients[$key]->dormant_duration > 60) {
                array_push($data, $dormant_clients[$key]);
            } elseif ($dormant_duration == 90 && $dormant_clients[$key]->dormant_duration > 90) {
                array_push($data, $dormant_clients[$key]);
            } else {
                //array_push($data,$dormant_clients[$key]);
            }
        }
        $dormant_clients = $data;
        //$dormant_clients = $dormant_clients->paginate($perPage)
        //    ->appends($request->input());
        //return $clientsData;
        return theme_view('client::client.dormant_clients', compact('dormant_clients', 'dormant_duration'));
    }



    public function getClientTotalDepositReport(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->status;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $client_id = $request->client_id;
            $query = DB::table("clients")
                ->leftJoin("branches", "branches.id", "clients.branch_id")
                ->leftJoin("users", "users.id", "clients.loan_officer_id")
                ->leftJoin("savings", "clients.id", "savings.client_id")
                ->leftJoin("savings_transactions", "savings.id", "savings_transactions.id")
                ->selectRaw("branches.name branch,clients.id,clients.loan_officer_id,
                            concat(clients.first_name,' ',clients.last_name) name,
                            clients.mobile,clients.email,clients.external_id,clients.status,
                            savings_transactions.name trx_name, sum(savings_transactions.amount) as deposit,savings_transactions.submitted_on")
                ->when($status, function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->when($client_id, function ($query) use ($client_id) {
                    $query->where('clients.id', $client_id);
                })
                ->when($start_date, function ($query) use ($start_date) {
                    $query->whereDate('savings_transactions.submitted_on', '>=', $start_date);
                })
                ->when($end_date, function ($query) use ($end_date) {
                    $query->whereDate('savings_transactions.submitted_on', '<=', $end_date);
                })
                ->groupBy('savings_transactions.submitted_on');

            return DataTables::of($query)->editColumn('id', function ($data) {
                return '<a href="' . url('client/' . $data->id . '/show') . '">' . $data->id . '</a>';
            })->editColumn('name', function ($data) {
                return '<a href="' . url('client/' . $data->id . '/show') . '">' . $data->name . '</a>';
            })->editColumn('status', function ($data) {
                if ($data->status == "pending") {
                    return trans_choice('core::general.pending', 1);
                }
                if ($data->status == "active") {
                    return trans_choice('core::general.active', 1);
                }
                if ($data->status == "inactive") {
                    return trans_choice('core::general.inactive', 1);
                }
                if ($data->gender == "deceased") {
                    return trans_choice('client::general.deceased', 1);
                }
                if ($data->gender == "unspecified") {
                    return trans_choice('core::general.unspecified', 1);
                }
            })->rawColumns(['id', 'name', 'action'])->make(true);
        }

        $client_groups = ClientGroup::all();
        $clients = Client::all();
        return theme_view('client::client.client_deposit_report', compact('client_groups', 'clients'));
    }

    public function getClientBalanceReport(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->status;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $client_id = $request->client_id;
            $branch_id = $request->branch_id;
            $queryClients = DB::table("clients")
                ->leftJoin("branches", "branches.id", "clients.branch_id")
                ->leftJoin("users", "users.id", "clients.loan_officer_id")
                ->selectRaw("branches.name branch,clients.id,clients.branch_id,clients.loan_officer_id,
                            concat(clients.first_name,' ',clients.last_name) name,
                            clients.mobile,clients.email,clients.external_id,clients.status")
                ->when($status, function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('clients.branch_id', $branch_id);
                })
                ->when($client_id, function ($query) use ($client_id) {
                    $query->where('clients.id', $client_id);
                })->get();

            foreach ($queryClients as $key => $client) {

                $savingBalance = DB::table("savings")
                    ->leftJoin("savings_transactions", "savings.id", "savings_transactions.savings_id")
                    ->selectRaw("COALESCE(sum(savings_transactions.debit),0) saving_debit, COALESCE(sum(savings_transactions.credit),0) saving_credit,
                    savings_transactions.savings_id, (COALESCE(sum(savings_transactions.credit),0) - COALESCE(sum(savings_transactions.debit),0)) saving_balance,savings.client_id")
                    ->where("savings.client_id", $client->id)
                    ->when($start_date, function ($query) use ($start_date) {
                        $query->whereDate('savings_transactions.submitted_on', '>=', $start_date);
                    })
                    ->when($end_date, function ($query) use ($end_date) {
                        $query->whereDate('savings_transactions.submitted_on', '<=', $end_date);
                    })
                    ->groupBy('savings.client_id')->first();
                $queryClients[$key]->saving_balance = $savingBalance != null ? number_format($savingBalance->saving_balance, 2) : 0.00;


                //not applicable there are many conditions

                $loanBalance = DB::table("loans")
                    ->leftJoin("loan_transactions", "loans.id", "loan_transactions.loan_id")
                    ->selectRaw("COALESCE(sum(loan_transactions.debit),0) loan_debit, COALESCE(sum(loan_transactions.credit),0) loan_credit,
                    loan_transactions.loan_id, (COALESCE(sum(loan_transactions.debit),0) - COALESCE(sum(loan_transactions.credit),0)) loan_balance,loans.client_id,
                    loan_transactions.loan_transaction_type_id")
                    ->where("loans.client_id", $client->id)
                    ->whereIn("loan_transactions.loan_transaction_type_id", [1, 2, 4, 6, 8, 9, 10, 11])
                    ->when($start_date, function ($query) use ($start_date) {
                        $query->whereDate('loan_transactions.submitted_on', '>=', $start_date);
                    })
                    ->when($end_date, function ($query) use ($end_date) {
                        $query->whereDate('loan_transactions.submitted_on', '<=', $end_date);
                    })
                    ->groupBy('loans.client_id')->first();
                $queryClients[$key]->loan_balance = $loanBalance != null ? number_format($loanBalance->loan_balance, 2) : 0.00;

                /*$data = DB::table("loans")
                        ->leftJoin("clients", "clients.id", "loans.client_id")
                        ->leftJoin("loan_repayment_schedules", "loan_repayment_schedules.loan_id", "loans.id")
                        ->leftJoin("loan_products", "loan_products.id", "loans.loan_product_id")
                        ->leftJoin("branches", "branches.id", "loans.branch_id")
                        ->leftJoin("users", "users.id", "loans.loan_officer_id")
                        ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) loan_officer,loans.id,loans.client_id,loans.applied_amount,loans.principal,loans.disbursed_on_date,loans.expected_maturity_date,loan_products.name loan_product,loans.status,loans.decimals,branches.name branch, SUM(loan_repayment_schedules.principal) total_principal, SUM(loan_repayment_schedules.principal_written_off_derived) principal_written_off_derived, SUM(loan_repayment_schedules.principal_repaid_derived) principal_repaid_derived, SUM(loan_repayment_schedules.interest) total_interest, SUM(loan_repayment_schedules.interest_waived_derived) interest_waived_derived,SUM(loan_repayment_schedules.interest_written_off_derived) interest_written_off_derived,  SUM(loan_repayment_schedules.interest_repaid_derived) interest_repaid_derived,SUM(loan_repayment_schedules.fees) total_fees, SUM(loan_repayment_schedules.fees_waived_derived) fees_waived_derived, SUM(loan_repayment_schedules.fees_written_off_derived) fees_written_off_derived, SUM(loan_repayment_schedules.fees_repaid_derived) fees_repaid_derived,SUM(loan_repayment_schedules.penalties) total_penalties, SUM(loan_repayment_schedules.penalties_waived_derived) penalties_waived_derived, SUM(loan_repayment_schedules.penalties_written_off_derived) penalties_written_off_derived, SUM(loan_repayment_schedules.penalties_repaid_derived) penalties_repaid_derived")
                        ->where("loans.client_id", $client_id)
                        ->when($start_date, function ($query) use ($start_date) {
                            //$query->whereDate('loan_transactions.submitted_on', '>=', $start_date);
                        })
                        ->when($end_date, function ($query) use ($end_date) {
                            //$query->whereDate('loan_transactions.submitted_on', '<=',$end_date);
                        })
                        ->groupBy('loans.client_id')->first();
                if($data != null){
                    $queryClients[$key]->loan_balance =  number_format(($data->total_principal - $data->principal_repaid_derived - $data->principal_written_off_derived) + ($data->total_interest - $data->interest_repaid_derived - $data->interest_written_off_derived - $data->interest_waived_derived) + ($data->total_fees - $data->fees_repaid_derived - $data->fees_written_off_derived - $data->fees_waived_derived) + ($data->total_penalties - $data->penalties_repaid_derived - $data->penalties_written_off_derived - $data->penalties_waived_derived), 2);
                }else{
                    $queryClients[$key]->loan_balance = 0.00;
                }*/
            }

            return DataTables::of($queryClients)->editColumn('id', function ($data) {
                return '<a href="' . url('client/' . $data->id . '/show') . '">' . $data->id . '</a>';
            })->editColumn('name', function ($data) {
                return '<a href="' . url('client/' . $data->id . '/show') . '">' . $data->name . '</a>';
            })
                ->editColumn('branch', function ($data) {
                    return '<a href="' . url('branch/' . $data->branch_id . '/show') . '">' . $data->branch . '</a>';
                })->editColumn('status', function ($data) {
                    if ($data->status == "pending") {
                        return trans_choice('core::general.pending', 1);
                    }
                    if ($data->status == "active") {
                        return trans_choice('core::general.active', 1);
                    }
                    if ($data->status == "inactive") {
                        return trans_choice('core::general.inactive', 1);
                    }
                    if ($data->gender == "deceased") {
                        return trans_choice('client::general.deceased', 1);
                    }
                    if ($data->gender == "unspecified") {
                        return trans_choice('core::general.unspecified', 1);
                    }
                })->rawColumns(['id', 'name', 'action', 'branch'])->make(true);
        }
        $branches = Branch::all();
        $client_groups = ClientGroup::all();
        $clients = Client::all();
        return theme_view('client::client.client_balance_report', compact('client_groups', 'clients', 'branches'));
    }

    public function getClientInfo(Request $request)
    {
        $client_id = $request->client_id;
        $client = Client::with('loan_officer')->find($client_id);
        return json_encode($client);
    }

    public function getClientStatement(Request $request)
    {
        $branches = Branch::all();
        $client_groups = ClientGroup::all();
        $clients = Client::all();
        return theme_view('client::client.client_balance_statement', compact('client_groups', 'clients', 'branches'));
    }

    public function getSavingOpeningBalance($client_id, $end_date)
    {
        $openingBalance = DB::table("savings")
            ->leftJoin("savings_transactions", "savings.id", "savings_transactions.savings_id")
            ->selectRaw("COALESCE(sum(savings_transactions.debit),0) saving_debit, COALESCE(sum(savings_transactions.credit),0) saving_credit,
        savings_transactions.savings_id, (COALESCE(sum(savings_transactions.credit),0) - COALESCE(sum(savings_transactions.debit),0)) saving_balance,savings.client_id")
            ->where("savings.client_id", $client_id)
            ->when($end_date, function ($query) use ($end_date) {
                $query->whereDate('savings_transactions.submitted_on', '<', $end_date);
            })
            ->groupBy('savings.client_id')->first();

        return $openingBalance != null ? $openingBalance->saving_balance : 0.00;
    }

    public function getSavingClosingBalance($client_id, $end_date)
    {
        $closingBalance = DB::table("savings")
            ->leftJoin("savings_transactions", "savings.id", "savings_transactions.savings_id")
            ->selectRaw("COALESCE(sum(savings_transactions.debit),0) saving_debit, COALESCE(sum(savings_transactions.credit),0) saving_credit,
        savings_transactions.savings_id, (COALESCE(sum(savings_transactions.credit),0) - COALESCE(sum(savings_transactions.debit),0)) saving_balance,savings.client_id")
            ->where("savings.client_id", $client_id)
            ->when($end_date, function ($query) use ($end_date) {
                $query->whereDate('savings_transactions.submitted_on', '<=', $end_date);
            })
            ->groupBy('savings.client_id')->first();

        return $closingBalance != null ? $closingBalance->saving_balance : 0.00;
    }

    public function getClientSavingSummery(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $client_id = $request->client_id;
        $openingBalance = $this->getSavingOpeningBalance($client_id, $start_date);
        $closingBalance = $this->getSavingClosingBalance($client_id, $end_date);

        $transactions = DB::table("savings")
            ->leftJoin("savings_transactions", "savings.id", "savings_transactions.savings_id")
            ->selectRaw("COALESCE(sum(savings_transactions.debit),0) saving_debit, COALESCE(sum(savings_transactions.credit),0) saving_credit,
        savings_transactions.savings_id, (COALESCE(sum(savings_transactions.credit),0) - COALESCE(sum(savings_transactions.debit),0)) saving_balance,savings.client_id,savings_transactions.submitted_on")
            ->where("savings.client_id", $client_id)
            ->when($start_date, function ($query) use ($start_date) {
                $query->whereDate('savings_transactions.submitted_on', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                $query->whereDate('savings_transactions.submitted_on', '<=', $end_date);
            })
            ->groupBy('savings.client_id')->first();

        $totalDebit = $transactions != null ? $transactions->saving_debit : 0;
        $totalCredit = $transactions != null ? $transactions->saving_credit : 0;
        $summery["openingBalance"] = number_format($openingBalance, 2);
        $summery["closingBalance"] = number_format($closingBalance, 2);
        $summery["totalDebit"] = number_format($totalDebit, 2);
        $summery["totalCredit"] = number_format($totalCredit, 2);
        return json_encode($summery);
    }

    public function getClientSavingStatement(Request $request)
    {
        if ($request->ajax()) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $client_id = $request->client_id;
            $branch_id = $request->branch_id;
            $openingBalance = $this->getSavingOpeningBalance($client_id, $start_date);
            $closingBalance = $this->getSavingClosingBalance($client_id, $end_date);


            $openingTrx = (object)[
                "id" => "",
                "saving_debit" => "",
                "saving_credit" => "",
                "transaction_date" => $start_date,
                "savings_id" => "",
                "client_id" => "",
                "savings_product_name" => "",
                "savings_transaction_type_name" => "Opening Balance",
                "balance" => $openingBalance
            ];

            $closingTrx = (object)[
                "id" => "",
                "saving_debit" => "",
                "saving_credit" => "",
                "transaction_date" => $end_date,
                "savings_id" => "",
                "client_id" => "",
                "savings_product_name" => "",
                "savings_transaction_type_name" => "Closing Balance",
                "balance" => $closingBalance
            ];

            $statements = array();

            $savingTransactions = DB::table("savings")
                ->leftJoin("savings_products", "savings.savings_product_id", "savings_products.id")
                ->leftJoin("savings_transactions", "savings.id", "savings_transactions.savings_id")
                ->leftJoin("savings_transaction_types", "savings_transactions.savings_transaction_type_id", "savings_transaction_types.id")
                ->selectRaw("savings_transactions.id,COALESCE(savings_transactions.debit,0) saving_debit, COALESCE(savings_transactions.credit,0) saving_credit,
                    savings_transactions.submitted_on transaction_date,savings_transactions.savings_id,savings.client_id,
                    savings_products.name savings_product_name, savings_transaction_types.name savings_transaction_type_name")
                ->where("savings.client_id", $client_id)
                ->when($start_date, function ($query) use ($start_date) {
                    $query->whereDate('savings_transactions.submitted_on', '>=', $start_date);
                })
                ->when($end_date, function ($query) use ($end_date) {
                    $query->whereDate('savings_transactions.submitted_on', '<=', $end_date);
                })->get();

            array_push($statements, $openingTrx);
            foreach ($savingTransactions as $key => $trx) {
                if ($trx->saving_debit != 0) {
                    $openingBalance = $openingBalance - $trx->saving_debit;
                }
                if ($trx->saving_credit != 0) {
                    $openingBalance = $openingBalance + $trx->saving_credit;
                }
                $savingTransactions[$key]->balance = $openingBalance;
                array_push($statements, $savingTransactions[$key]);
            }
            array_push($statements, $closingTrx);

            return DataTables::of($statements)->editColumn('savings_id', function ($data) {
                return '<a href="' . url('saving/' . $data->savings_id . '/show') . '">' . $data->savings_id . '</a>';
            })->editColumn('saving_debit', function ($data) {
                return $data->saving_debit != "" ? number_format($data->saving_debit, 2) : $data->saving_debit;
            })->editColumn('saving_credit', function ($data) {
                return $data->saving_credit != "" ? number_format($data->saving_credit, 2) : $data->saving_credit;
            })->editColumn('balance', function ($data) {
                return number_format($data->balance, 2);
            })
                ->editColumn('action', function ($data) {
                    if ($data->saving_debit == "") {
                        return "";
                    }
                    $action = '<a href="' . url('savings/transaction/' . $data->id . '/show') . '" class="btn btn-info"><i class="ri-eye-fill"></i>' . trans_choice('general.detail', 2) . '</a>';
                    return $action;
                })->rawColumns(['savings_id', 'savings_transaction_type_name', 'savings_product_name', 'action'])->make(true);
        }
    }

    public function getLoanOpeningBalance($client_id, $end_date)
    {
        $openingBalance = DB::table("loans")
            ->leftJoin("loan_transactions", "loans.id", "loan_transactions.loan_id")
            ->selectRaw("COALESCE(sum(loan_transactions.debit),0) loan_debit, COALESCE(sum(loan_transactions.credit),0) loan_credit,
        loan_transactions.loan_id, (COALESCE(sum(loan_transactions.debit),0) - COALESCE(sum(loan_transactions.credit),0)) loan_balance,loans.client_id")
            ->where("loans.client_id", $client_id)
            ->whereIn("loan_transactions.loan_transaction_type_id", [1, 2, 4, 6, 8, 9, 10, 11])
            ->when($end_date, function ($query) use ($end_date) {
                $query->whereDate('loan_transactions.submitted_on', '<', $end_date);
            })
            ->groupBy('loans.client_id')->first();

        return $openingBalance != null ? $openingBalance->loan_balance : 0.00;
    }

    public function getLoanClosingBalance($client_id, $end_date)
    {
        $closingBalance = DB::table("loans")
            ->leftJoin("loan_transactions", "loans.id", "loan_transactions.loan_id")
            ->selectRaw("COALESCE(sum(loan_transactions.debit),0) loan_debit, COALESCE(sum(loan_transactions.credit),0) loan_credit,
        loan_transactions.loan_id, (COALESCE(sum(loan_transactions.debit),0) - COALESCE(sum(loan_transactions.credit),0)) loan_balance,loans.client_id")
            ->where("loans.client_id", $client_id)
            ->whereIn("loan_transactions.loan_transaction_type_id", [1, 2, 4, 6, 8, 9, 10, 11])
            ->when($end_date, function ($query) use ($end_date) {
                $query->whereDate('loan_transactions.submitted_on', '<=', $end_date);
            })
            ->groupBy('loans.client_id')->first();

        return $closingBalance != null ? $closingBalance->loan_balance : 0.00;
    }

    public function getClientLoanSummery(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $client_id = $request->client_id;
        $openingBalance = $this->getLoanOpeningBalance($client_id, $start_date);
        $closingBalance = $this->getLoanClosingBalance($client_id, $end_date);

        $transactions = DB::table("loans")
            ->leftJoin("loan_transactions", "loans.id", "loan_transactions.loan_id")
            ->selectRaw("COALESCE(sum(loan_transactions.debit),0) loan_debit, COALESCE(sum(loan_transactions.credit),0) loan_credit,
        loan_transactions.loan_id, (COALESCE(sum(loan_transactions.credit),0) - COALESCE(sum(loan_transactions.debit),0)) loan_balance,loans.client_id")
            ->where("loans.client_id", $client_id)
            ->whereIn("loan_transactions.loan_transaction_type_id", [1, 2, 4, 6, 8, 9, 10, 11])
            ->when($start_date, function ($query) use ($start_date) {
                $query->whereDate('loan_transactions.submitted_on', '>=', $start_date);
            })
            ->when($end_date, function ($query) use ($end_date) {
                $query->whereDate('loan_transactions.submitted_on', '<=', $end_date);
            })
            ->groupBy('loans.client_id')->first();

        $totalDebit = $transactions != null ? $transactions->loan_debit : 0;
        $totalCredit = $transactions != null ? $transactions->loan_credit : 0;
        $summery["openingBalance"] = number_format($openingBalance, 2);
        $summery["closingBalance"] = number_format($closingBalance, 2);
        $summery["totalDebit"] = number_format($totalDebit, 2);
        $summery["totalCredit"] = number_format($totalCredit, 2);
        return json_encode($summery);
    }

    public function getClientLoanStatement(Request $request)
    {
        if ($request->ajax()) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $client_id = $request->client_id;
            $branch_id = $request->branch_id;
            $openingBalance = $this->getLoanOpeningBalance($client_id, $start_date);
            $closingBalance = $this->getLoanClosingBalance($client_id, $end_date);


            $openingTrx = (object)[
                "id" => "",
                "loan_debit" => "",
                "loan_credit" => "",
                "transaction_date" => $start_date,
                "loan_id" => "",
                "client_id" => "",
                "loan_product_name" => "",
                "loan_transaction_type_name" => "Opening Balance",
                "balance" => $openingBalance
            ];

            $closingTrx = (object)[
                "id" => "",
                "loan_debit" => "",
                "loan_credit" => "",
                "transaction_date" => $end_date,
                "loan_id" => "",
                "client_id" => "",
                "loan_product_name" => "",
                "loan_transaction_type_name" => "Closing Balance",
                "balance" => $closingBalance
            ];

            $statements = array();

            $loanTransactions = DB::table("loans")
                ->leftJoin("loan_products", "loans.loan_product_id", "loan_products.id")
                ->leftJoin("loan_transactions", "loans.id", "loan_transactions.loan_id")
                ->leftJoin("loan_transaction_types", "loan_transactions.loan_transaction_type_id", "loan_transaction_types.id")
                ->selectRaw("loan_transactions.id,COALESCE(loan_transactions.debit,0) loan_debit, COALESCE(loan_transactions.credit,0) loan_credit,
                    loan_transactions.submitted_on transaction_date,loan_transactions.loan_id,loans.client_id,
                    loan_products.name loan_product_name, loan_transaction_types.name loan_transaction_type_name")
                ->where("loans.client_id", $client_id)
                ->whereIn("loan_transactions.loan_transaction_type_id", [1, 2, 4, 6, 8, 9, 10, 11])
                ->when($start_date, function ($query) use ($start_date) {
                    $query->whereDate('loan_transactions.submitted_on', '>=', $start_date);
                })
                ->when($end_date, function ($query) use ($end_date) {
                    $query->whereDate('loan_transactions.submitted_on', '<=', $end_date);
                })->get();

            array_push($statements, $openingTrx);
            foreach ($loanTransactions as $key => $trx) {
                if ($trx->loan_debit != 0) {
                    $openingBalance = $openingBalance + $trx->loan_debit;
                }
                if ($trx->loan_credit != 0) {
                    $openingBalance = $openingBalance - $trx->loan_credit;
                }
                $loanTransactions[$key]->balance = $openingBalance;
                array_push($statements, $loanTransactions[$key]);
            }
            array_push($statements, $closingTrx);

            return DataTables::of($statements)->editColumn('loan_id', function ($data) {
                return '<a href="' . url('loan/' . $data->loan_id . '/show') . '">' . $data->loan_id . '</a>';
            })->editColumn('loan_debit', function ($data) {
                return $data->loan_debit != "" ? number_format($data->loan_debit, 2) : $data->loan_debit;
            })->editColumn('loan_credit', function ($data) {
                return $data->loan_credit != "" ? number_format($data->loan_credit, 2) : $data->loan_credit;
            })->editColumn('balance', function ($data) {
                return number_format($data->balance, 2);
            })
                ->editColumn('action', function ($data) {
                    if ($data->loan_debit == "") {
                        return "";
                    }
                    $action = '<a href="' . url('loan/transaction/' . $data->id . '/show') . '" class="btn btn-info"><i class="ri-eye-fill"></i>' . trans_choice('general.detail', 2) . '</a>';
                    return $action;
                })->rawColumns(['loan_id', 'loan_transaction_type_name', 'loan_product_name', 'action'])->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $titles = Title::all();
        $professions = Profession::all();
        $client_types = ClientType::all();
        $client_groups = ClientGroup::all();
        // $users = User::whereHas('roles', function ($query) {
        //     $query->where('name', '!=', 'client')
        //         ->orWhere('name', 'admin');
        // })->get();
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['client', 'admin']);
        })->get();

        $branches = Branch::all();
        $countries = Country::all();
        $randnum = rand(1111111111, 9999999999);
        $custom_fields = CustomField::where('category', 'add_client')->where('active', 1)->get();
        return theme_view('client::client.create', compact('titles', 'professions', 'client_types', 'client_groups', 'users', 'branches', 'countries', 'custom_fields', 'randnum'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        info($request->all());
        $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
            'branch_id' => ['required'],
            'email' => ['nullable', 'email', 'max:255'],
            'dob' => ['required', 'date'],
            'created_date' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ]);
        $client = new Client();
        $client->first_name = $request->first_name;
        $client->last_name = $request->last_name;
        $client->external_id = $request->external_id;
        $client->created_by_id = Auth::id();
        $client->gender = $request->gender;
        $client->country_id = $request->country_id;
        $client->loan_officer_id = $request->loan_officer_id;
        $client->title_id = $request->title_id;
        $client->branch_id = $request->branch_id;
        $client->client_type_id = $request->client_type_id;
        $client->profession_id = $request->profession_id;
        $client->mobile = $request->mobile;
        // $client->church = $request->church;
        // $client->pastor = $request->pastor;
        // $client->church_location = $request->church_location;
        $client->notes = $request->notes;
        $client->email = $request->email;
        $client->address = $request->address;
        $client->marital_status = $request->marital_status;
        $client->created_date = $request->created_date;

        $request->dob ? $client->dob = $request->dob : '';

        $client->place_of_workship      = $request->place_of_workship;
        $client->address_type           = $request->address_type;
        $client->employment_status      = $request->employment_status;
        $client->business_activity      = $request->business_activity;
        $client->business_name          = $request->business_name;
        $client->business_location      = $request->business_location;
        $client->business_address       = $request->business_address;

        // $client->mobile = $request->mobile;
        // $client->church = $request->church;
        // $client->pastor = $request->pastor;

        $client->state = $request->state;
        $client->city = $request->city;
        $client->zip = $request->zip;
        $client->client_group_id = $request->client_group_id;
        if ($request->hasFile('signature_pad')) {
            $signature_file_name = $request->file('signature_pad')->store('public/uploads/clients');
            //check if we had a file before
            $client->signature_pad = basename($signature_file_name);
        }
        if ($request->hasFile('photo')) {
            $file_name = $request->file('photo')->store('public/uploads/clients');
            $client->photo = basename($file_name);
        } elseif ($request->filled('client_photo')) {
            // Handle webcam base64 image
            $data = $request->input('client_photo');
            if (preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $data)) {
                $data = preg_replace('/^data:image\/(png|jpg|jpeg);base64,/', '', $data);
                $data = str_replace(' ', '+', $data);
                $imageData = base64_decode($data);
                $extension = 'png'; // Default to png
                if (strpos($request->input('client_photo'), 'jpeg') !== false) $extension = 'jpg';
                $filename = 'webcam_' . uniqid() . '.' . $extension;
                $path = storage_path('app/public/uploads/clients/' . $filename);
                file_put_contents($path, $imageData);
                $client->photo = $filename;
            }
        }
        $client->save();
        if ($client->status == "active") {
            $this->processSmsClientOpenAccount(1, $client->id);
        }
        custom_fields_save_form('add_client', $request, $client->id);
        activity()->on($client)
            ->withProperties(['id' => $client->id])
            ->log('Create Client');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $client = Client::with('loan_officer')->find($id);
        $custom_fields = CustomField::where('category', 'add_client')->where('active', 1)->get();
        return theme_view('client::client.show', compact('client', 'custom_fields'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $client = Client::find($id);
        $titles = Title::all();
        $professions = Profession::all();
        $client_types = ClientType::all();
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $branches = Branch::all();
        $countries = Country::all();
        $client_groups = ClientGroup::all();
        $custom_fields = CustomField::where('category', 'add_client')->where('active', 1)->get();
        return theme_view('client::client.edit', compact('client', 'titles', 'professions', 'client_types', 'client_groups', 'users', 'branches', 'countries', 'custom_fields'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
            'email' => ['nullable', 'email', 'max:255'],
            'dob' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ]);
        $client = Client::find($id);
        $previous_status = $client->status;
        $client->first_name = $request->first_name;
        $client->last_name = $request->last_name;
        $client->external_id = $request->external_id;
        $client->gender = $request->gender;
        $client->country_id = $request->country_id;
        $client->loan_officer_id = $request->loan_officer_id;
        $client->title_id = $request->title_id;
        $client->client_type_id = $request->client_type_id;
        $client->profession_id = $request->profession_id;
        $client->mobile = $request->mobile;
        $client->notes = $request->notes;
        $client->email = $request->email;
        $client->address = $request->address;
        $client->marital_status = $request->marital_status;
        $request->dob ? $client->dob = $request->dob : '';

        $client->place_of_workship  = $request->place_of_workship;
        $client->address_type       = $request->address_type;
        $client->employment_status  = $request->employment_status;
        $client->business_activity  = $request->business_activity;
        $client->business_name      = $request->business_name;
        $client->business_location  = $request->business_location;
        $client->business_address   = $request->business_address;

        $client->state = $request->state;
        $client->city = $request->city;
        $client->zip = $request->zip;
        $client->client_group_id = $request->client_group_id;

        if ($request->hasFile('signature_pad')) {
            $signature_file_name = $request->file('signature_pad')->store('public/uploads/clients');
            //check if we had a file before
            if ($client->signature_pad) {
                Storage::delete('public/uploads/clients/' . $client->signature_pad);
            }
            $client->signature_pad = basename($signature_file_name);
        }
        if ($request->hasFile('photo')) {
            $file_name = $request->file('photo')->store('public/uploads/clients');
            //check if we had a file before
            if ($client->photo) {
                Storage::delete('public/uploads/clients/' . $client->photo);
            }
            $client->photo = basename($file_name);
        } elseif ($request->filled('client_photo')) {
            // Handle webcam base64 image
            $data = $request->input('client_photo');
            if (preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $data)) {
                $data = preg_replace('/^data:image\/(png|jpg|jpeg);base64,/', '', $data);
                $data = str_replace(' ', '+', $data);
                $imageData = base64_decode($data);
                $extension = 'png'; // Default to png
                if (strpos($request->input('client_photo'), 'jpeg') !== false) $extension = 'jpg';
                $filename = 'webcam_' . uniqid() . '.' . $extension;
                $path = storage_path('app/public/uploads/clients/' . $filename);
                file_put_contents($path, $imageData);
                // Delete previous photo if exists
                if ($client->photo) {
                    Storage::delete('public/uploads/clients/' . $client->photo);
                }
                $client->photo = $filename;
            }
        }
        $client->save();
        if ($client->status == "active" && $previous_status != "active") {
            $this->processSmsClientOpenAccount(1, $client->id);
        }
        custom_fields_save_form('add_client', $request, $client->id);
        activity()->on($client)
            ->withProperties(['id' => $client->id])
            ->log('Update Client');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $client = Client::find($id);
        $client->delete();
        activity()->on($client)
            ->withProperties(['id' => $client->id])
            ->log('Delete Client');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }

    public function create_user($id)
    {
        $users = User::role('client')->get();
        $client = Client::find($id);
        return theme_view('client::client.create_user', compact('users', 'client'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store_user(Request $request, $id)
    {
        if ($request->existing == 1) {
            $request->validate([
                'user_id' => ['required'],
            ]);
            if (ClientUser::where('client_id', $id)->where('user_id', $request->user_id)->get()->count() > 0) {
                \flash(trans_choice("client::general.user_already_added", 1))->error()->important();
                return redirect()->back();
            }
            $client_user = new ClientUser();
            $client_user->client_id = $id;
            $client_user->created_by_id = Auth::id();
            $client_user->user_id = $request->user_id;
            $client_user->save();
        } else {
            $request->validate([
                'first_name' => ['required'],
                'last_name' => ['required'],
                'gender' => ['required'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'email' => $request->email,
                'notes' => $request->notes,
                'address' => $request->address,
                'password' => Hash::make($request->password),
                'email_verified_at' => date("Y-m-d H:i:s")
            ]);
            //attach client role
            $role = Role::findByName('client');
            $user->assignRole($role);
            $client_user = new ClientUser();
            $client_user->client_id = $id;
            $client_user->created_by_id = Auth::id();
            $client_user->user_id = $user->id;
            $client_user->save();
        }
        activity()->log('Create Client User');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/' . $id . '/show');
    }

    public function destroy_user($id)
    {
        ClientUser::destroy($id);
        activity()->log('Delete Client User');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }

    public function change_status(Request $request, $id)
    {
        $request->validate([
            'status' => ['required'],
            'date' => ['required', 'date'],
        ]);
        $client = Client::find($id);
        $client->status = $request->status;
        $client->save();
        activity()->on($client)
            ->withProperties(['id' => $client->id])
            ->log('Update Client Status');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }

    public function clientFileExportImport()
    {
        //return view('file-import');
        return theme_view('client::client.client_import');
    }

    public function clientExcelImport(Request $request)
    {
        if (!$request->hasFile('file')) {
            \flash("File Not Selected")->success()->important();
            return redirect()->back();
        }
        Excel::import(new ClientImport, $request->file('file')->store('temp'));
        //return back();
        \flash("Successfully Imported")->success()->important();
        return redirect('client');
    }


    public function processSmsClientOpenAccount($sms_gateway_id, $client_id)
    {
        $client = Client::where('id', $client_id)->first();
        $mobile = Client::where('id', $client_id)->first()->mobile;
        $message = $client->first_name . " " . $client->last_name . ", Your Account is Activated";
        $sms = [
            'sms_gateway_id' => $sms_gateway_id,
            'client_id' => $client_id,
            'text_body' => $message,
            'send_to' => $mobile
        ];
        $sms = CommunicationLog::create($sms);
        if ($sms_gateway_id == 1) {
            $sms_body = $message;
            //$response = $this->send_sms_old($sms_gateway_id,$mobile, $sms_body);
            // if(strpos($response, '1701') !== false){
            //     $sms->status = 'delivered';
            // } else{
            //     $sms->status = 'failed';
            // }
            //handle send_sms here
            try {
                $smsGateway = $sms_gateway_id
                    ? \Modules\Communication\Entities\SmsGateway::find($sms_gateway_id)
                    : null;
                $arkesel = $smsGateway
                    ? new \Modules\Client\Drivers\Arkesel($smsGateway->key, $smsGateway->sender)
                    : new \Modules\Client\Drivers\Arkesel();
                $formattedMobile = '233' . ltrim($mobile, '0');
                $response = $arkesel->send($sms_body, [$formattedMobile]);
            } catch (\Exception $e) {
                $response = $e->getMessage();
                \Log::error('Arkesel SMS error: ' . $response);
            }
            $sms->response = $response;
            $sms->save();
        }
        activity()->on($sms)
            ->withProperties(['id' => $sms->id])
            ->log('Process and Send Sms');
        return;
    }

    public function send_sms_old($sms_gateway_id, $to, $msg)
    {
        if ($sms_gateway_id == 1) {
            $sms_gateway_id = 1;
            $active_sms = SmsGateway::find($sms_gateway_id);
            $append = "&";
            $append .= $active_sms->to_name . "=" . $to;
            $append .= "&" . $active_sms->msg_name . "=" . urlencode($msg);
            $url = $active_sms->url . $append;
            $endpoint = $active_sms->url;
            $params = array(
                $active_sms->key_one => $active_sms->key_one_description,
                $active_sms->key_two => $active_sms->key_two_description,
                'type' => 0,
                'dlr' => 1,
                $active_sms->key_three => $active_sms->key_three_description,
                $active_sms->to_name => $to,
                $active_sms->msg_name => $msg
            );
            $url = $endpoint . '?' . http_build_query($params);
            //return $url;
            //send sms here
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            $curl_scraped_page = curl_exec($ch);
            curl_close($ch);
            return $curl_scraped_page;
        }
    }

    public function blacklist(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);
        //dd($request->all());
        $client = Client::findOrFail($id);
        $blacklist = Blacklist::updateOrCreate(
            ['client_id' => $client->id],
            [
                'reason' => $request->reason,
                'status' => 'active',
                'created_by' => Auth::id(),
            ]
        );
        activity()->on($client)
            ->withProperties(['id' => $client->id])
            ->log('Client Blacklisted');
        \flash('Client successfully blacklisted')->success()->important();
        return redirect()->back();
    }

    public function unblacklist($id)
    {
        $client = Client::findOrFail($id);
        $blacklist = $client->blacklist;
        if ($blacklist) {
            $blacklist->status = 'inactive';
            $blacklist->save();
        }
        activity()->on($client)
            ->withProperties(['id' => $client->id])
            ->log('Client Unblacklisted');
        \flash('Client successfully unblacklisted')->success()->important();
        return redirect()->back();
    }
}
