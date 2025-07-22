<?php

namespace Modules\Communication\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laracasts\Flash\Flash;
use Modules\Communication\Entities\CommunicationCampaignLog;
use Modules\Communication\Entities\SmsGateway;
use Modules\Communication\Entities\RouteMobileHttpSms;
use Modules\Communication\Entities\CommunicationLog;
use Modules\Client\Entities\Client;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Setting;

class CommunicationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return theme_view('communication::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return theme_view('communication::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return theme_view('communication::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return theme_view('communication::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }


    // public function getAllSms(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $status = $request->status;
    //         $start_date = $request->start_date;
    //         $end_date = $request->end_date;
    //         $client_id = $request->client_id;
    //         $query = DB::table("communication_logs")
    //             ->leftJoin("clients", "clients.id", "communication_logs.client_id")
    //             ->leftJoin("sms_gateways", "sms_gateways.id", "communication_logs.sms_gateway_id")
    //             ->selectRaw("communication_logs.type,communication_logs.id,communication_logs.client_id,
    //                         concat(clients.first_name,' ',clients.last_name) client_name,communication_logs.text_body,
    //                         communication_logs.send_to,communication_logs.status,
    //                         sms_gateways.name sms_gateway, communication_logs.created_at")
    //             ->when($status, function ($query) use ($status) {
    //                 $query->where('status', $status);
    //             })
    //             ->when($client_id, function ($query) use ($client_id) {
    //                 $query->where('clients.id', $client_id);
    //             })
    //             ->when($start_date, function ($query) use ($start_date) {
    //                 $query->whereDate('communication_logs.created_at', '>=', $start_date);
    //             })
    //             ->when($end_date, function ($query) use ($end_date) {
    //                 $query->whereDate('communication_logs.created_at', '<=', $end_date);
    //             });

    //         return DataTables::of($query)->editColumn('client_name', function ($data) {
    //             return '<a href="' . url('client/' . $data->client_id . '/show') . '">' . $data->client_name . '</a>';
    //         })->editColumn('status', function ($data) {
    //             if ($data->status == "pending") {
    //                 return trans_choice('core::general.pending', 1);
    //             }
    //             if ($data->status == "failed") {
    //                 return trans_choice('core::general.failed', 1);
    //             }
    //             if ($data->status == "delivered") {
    //                 return trans_choice('core::general.delivered', 1);
    //             }
    //             if ($data->gender == "sent") {
    //                 return trans_choice('client::general.sent', 1);
    //             }
    //         })->rawColumns(['id', 'client_name', 'action'])->make(true);
    //     }

    //     $clients = Client::all();
    //     return theme_view('communication::communication.all_sms_report', compact('clients'));
    // }

    public function getAllSms(Request $request)
    {
        if ($request->ajax()) {
            $status = $request->status;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $client_id = $request->client_id;
            $query = DB::table("communication_logs")
                ->leftJoin("clients", "clients.id", "communication_logs.client_id")
                ->leftJoin("sms_gateways", "sms_gateways.id", "communication_logs.sms_gateway_id")
                ->selectRaw("
                communication_logs.type,
                communication_logs.id,
                communication_logs.client_id,
                CONCAT(clients.first_name, ' ', clients.last_name) as client_name,
                communication_logs.text_body,
                communication_logs.send_to,
                communication_logs.status,
                sms_gateways.sender as sms_gateway_sender,
                sms_gateways.key as sms_gateway_key,
                sms_gateways.active as sms_gateway_active,
                communication_logs.created_at
            ")
                ->when($status, function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->when($client_id, function ($query) use ($client_id) {
                    $query->where('clients.id', $client_id);
                })
                ->when($start_date, function ($query) use ($start_date) {
                    $query->whereDate('communication_logs.created_at', '>=', $start_date);
                })
                ->when($end_date, function ($query) use ($end_date) {
                    $query->whereDate('communication_logs.created_at', '<=', $end_date);
                });

            return DataTables::of($query)
                ->editColumn('client_name', function ($data) {
                    return '<a href="' . url('client/' . $data->client_id . '/show') . '">' . e($data->client_name) . '</a>';
                })
                ->editColumn('sms_gateway_sender', function ($data) {
                    return '<span>' . e($data->sms_gateway_sender) . '</span>';
                })
                ->editColumn('sms_gateway_key', function ($data) {
                    return '<span data-bs-toggle="tooltip" title="' . e($data->sms_gateway_key) . '">' . \Illuminate\Support\Str::limit($data->sms_gateway_key, 10) . '</span>';
                })
                ->editColumn('sms_gateway_active', function ($data) {
                    if ($data->sms_gateway_active == '0') {
                        return '<span class="label label-warning">' . trans_choice('core::general.no', 1) . '</span>';
                    }
                    if ($data->sms_gateway_active == '1') {
                        return '<span class="label label-success">' . trans_choice('core::general.yes', 1) . '</span>';
                    }
                })
                ->editColumn('status', function ($data) {
                    if ($data->status == "pending") {
                        return trans_choice('core::general.pending', 1);
                    }
                    if ($data->status == "failed") {
                        return trans_choice('core::general.failed', 1);
                    }
                    if ($data->status == "delivered") {
                        return trans_choice('core::general.delivered', 1);
                    }
                    if ($data->status == "sent") {
                        return trans_choice('client::general.sent', 1);
                    }
                })
                ->rawColumns(['client_name', 'sms_gateway_sender', 'sms_gateway_key', 'sms_gateway_active'])
                ->make(true);
        }

        $clients = Client::all();
        return theme_view('communication::communication.all_sms_report', compact('clients'));
    }

    public function sendSmsForm()
    {
        $clients = Client::all();
        $smsGateways = SmsGateway::all();
        //dd($smsGateways);
        return theme_view('communication::communication.send_sms_form', compact('clients', 'smsGateways'));
    }

    public function processSmsSend(Request $request)
    {
       
        $client_id = $request->client_id;
        $sms_gateway_id = $request->sms_gateway_id;
        

        // Use Arkesel gateway (first active one)
        $arkesel = new \Modules\Client\Drivers\Arkesel();

        if ($client_id === "all") {
            $clients = \Modules\Client\Entities\Client::all();
            $mobiles = [];
            foreach ($clients as $client) {
                if ($client->mobile) {
                    // $mobiles[] = $client->mobile;
                    $formattedMobile = '233' . ltrim($client->mobile, '0');
                    $mobiles[] = $formattedMobile;
                    // Log each SMS sent
                    $sms = [
                        'sms_gateway_id' => $sms_gateway_id,
                        'client_id' => $client->id,
                        'text_body' => $request->sms,
                        'send_to' => $client->mobile
                    ];
                    $smsLog = CommunicationLog::create($sms);
                }

                // Log activity for each SMS
                activity()->on($smsLog)
                    ->withProperties(['id' => $smsLog->id])
                    ->log('Process and Send Sms');
            }
            try {
                $response = $arkesel->send($request->sms, $mobiles);
                // Optionally update all logs with status/response
                CommunicationLog::whereIn('client_id', $clients->pluck('id'))->latest()->take(count($mobiles))->update([
                    'status' => 'delivered',
                    'response' => $response,
                ]);
            } catch (\Exception $e) {
                \Modules\Communication\Entities\CommunicationLog::whereIn('client_id', $clients->pluck('id'))->latest()->take(count($mobiles))->update([
                    'status' => 'failed',
                    'response' => $e->getMessage(),
                ]);
            }
        } else {
            $mobile = Client::where('id', $client_id)->first()->mobile;
              $formattedMobile = '233' . ltrim($mobile, '0');
              
            $sms = [
                'sms_gateway_id' => $sms_gateway_id,
                'client_id' => $client_id,
                'text_body' => $request->sms,
                'send_to' => $formattedMobile
            ];
            $smsLog = CommunicationLog::create($sms);

            try {
                $response = $arkesel->send($request->sms, [$formattedMobile]);
                $smsLog->status = 'delivered';
                $smsLog->response = $response;
                $smsLog->save();
            } catch (\Exception $e) {
                $smsLog->status = 'failed';
                $smsLog->response = $e->getMessage();
                $smsLog->save();
            }

            // Log activity for single SMS
            activity()->on($smsLog)
                ->withProperties(['id' => $smsLog->id])
                ->log('Process and Send Sms');
        }

        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('communication/all_sms');
    }


    public function sendSms(Request $request)
    {
        if ($gateway_id == 1) {
            $obj = new RouteMobileHttpSms("rslr.connectbind.com", "8080", "msgg-test", "AS#23ghn", "wish", "message", "233246068512", "0", "0");
            $obj->Submit();

            //save the sms also on database
            $request->validate([
                'text_body' => ['required'],
                'send_to' => ['required']

            ]);
            $sms = [
                'text_body' => $request->text_body,
                'send_to' => $request->send_to
            ];
            $sms = CommunicationLog::create($sms);
            //old sms testing

            return json_encode($sms);
        }
    }



    public function getAllJobs(Request $request)
    {
        if ($request->ajax()) {
            $client_id = $request->client_id;
            $query = DB::table("jobs")
                ->selectRaw("id,queue,payload,attempts,reserved_at,available_at,created_at");

            return DataTables::of($query)->rawColumns(['payload', 'queue'])->make(true);
        }
        return theme_view('communication::communication.all_jobs_report');
    }

    public function getAllFailedJobs(Request $request)
    {
        if ($request->ajax()) {
            $client_id = $request->client_id;
            $query = DB::table("failed_jobs")
                ->selectRaw("id,connection,queue,payload,exception,failed_at");

            return DataTables::of($query)->rawColumns(['payload', 'exception'])->make(true);
        }
        return theme_view('communication::communication.all_failed_jobs_report');
    }
}
