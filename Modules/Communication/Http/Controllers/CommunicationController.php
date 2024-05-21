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
                ->selectRaw("communication_logs.type,communication_logs.id,communication_logs.client_id,
                            concat(clients.first_name,' ',clients.last_name) client_name,communication_logs.text_body,
                            communication_logs.send_to,communication_logs.status,
                            sms_gateways.name sms_gateway, communication_logs.created_at")
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
                    $query->whereDate('communication_logs.created_at', '<=',$end_date);
                });

            return DataTables::of($query)->editColumn('client_name', function ($data) {
                return '<a href="' . url('client/' . $data->client_id . '/show') . '">' . $data->client_name . '</a>';

            })->editColumn('status', function ($data) {
                if ($data->status == "pending") {
                    return trans_choice('core::general.pending', 1);
                }
                if ($data->status == "failed") {
                    return trans_choice('core::general.failed', 1);
                }
                if ($data->status == "delivered") {
                    return trans_choice('core::general.delivered', 1);
                }
                if ($data->gender == "sent") {
                    return trans_choice('client::general.sent', 1);
                }
            })->rawColumns(['id', 'client_name', 'action'])->make(true);
        }

        $clients = Client::all();
        return theme_view('communication::communication.all_sms_report', compact('clients'));
    }

    public function sendSmsForm(){
        $clients = Client::all();
        $smsGateways = SmsGateway::all();
        return theme_view('communication::communication.send_sms_form', compact('clients','smsGateways'));
    }

    public function processSmsSend(Request $request){
        $client_id = $request->client_id;
        $sms_gateway_id = $request->sms_gateway_id;
        $mobile = Client::where('id',$client_id)->first()->mobile;
        $sms = [
            'sms_gateway_id' => $sms_gateway_id,
            'client_id' => $request->client_id,
            'text_body' => $request->sms,
            'send_to' => $mobile
        ];
        $sms = CommunicationLog::create($sms);
        if($sms_gateway_id == 1){
            $sms_body= $request->sms;
            $response = $this->send_sms_old($sms_gateway_id,$mobile, $sms_body);

            //new not working
            //$obj = new RouteMobileHttpSms("rslr.connectbind.com","8080","msgg-test","AS#23ghn","wish",$request->sms,$mobile,"0","0");
            //$response = $obj->Submit();
            if(strpos($response, '1701') !== false){
                $sms->status = 'delivered';
            } else{
                $sms->status = 'failed';
            }
            $sms->response = $response;
            $sms->save();
        }
        activity()->on($sms)
            ->withProperties(['id' => $sms->id])
            ->log('Process and Send Sms');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('communication/all_sms');

    }

    public function sendSms(Request $request){
        if($gateway_id==1){
            $obj = new RouteMobileHttpSms("rslr.connectbind.com","8080","msgg-test","AS#23ghn","wish","message","233246068512","0","0");
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

    public function send_sms_old($sms_gateway_id,$to, $msg)
    {
        if($sms_gateway_id == 1){
                /*$active_sms = SmsGateway::find($sms_gateway_id);
                $append = "&";
                $append .= $active_sms->to_name . "=" . $to;
                $append .= "&" . $active_sms->msg_name . "=" . urlencode($msg);
                $url = $active_sms->url . $append;
                $endpoint = 'http://rslr.connectbind.com:8080/bulksms/bulksms';
                $params = array('username' => 'msgh-test',
                                'password' => 'As#23ghn',
                                'type'=> 0,
                                'dlr'=> 1,
                                'source'=>'wish',
                                'destination'=>$to,
                                'message'=>$msg);
                return $url = $endpoint . '?' . http_build_query($params);

                
                //send sms here
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_VERBOSE, true);
                $curl_scraped_page = curl_exec($ch);
                curl_close($ch);
                return $curl_scraped_page;*/
                $sms_gateway_id = 1;
                $active_sms = SmsGateway::find($sms_gateway_id);
                $append = "&";
                $append .= $active_sms->to_name . "=" . $to;
                $append .= "&" . $active_sms->msg_name . "=" . urlencode($msg);
                $url = $active_sms->url . $append;
                $endpoint = $active_sms->url;
                $params = array($active_sms->key_one => $active_sms->key_one_description,
                                $active_sms->key_two => $active_sms->key_two_description,
                                'type'=> 0,
                                'dlr'=> 1,
                                $active_sms->key_three => $active_sms->key_three_description,
                                $active_sms->to_name => $to,
                                $active_sms->msg_name => $msg);
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
