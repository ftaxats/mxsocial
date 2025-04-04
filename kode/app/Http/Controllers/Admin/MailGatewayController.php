<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GatewayRequest;
use App\Http\Utility\SendMail;
use App\Models\Admin\MailGateway;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MailGatewayController extends Controller
{
    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['permissions:view_gateway'])->only('list');
        $this->middleware(['permissions:update_gateway'])->only(['edit','update',"test",'updateStatus']);
    }


    /**
     * gateway list
     *
     * @return View
     */
    public function list(): View
    {

        return view('admin.mail_gateway.list',[
            'breadcrumbs' =>  ['Home'=>'admin.home','Gateways'=> null],
            'title'       => 'Manage Gateway',
            'gateways'    => MailGateway::with(['updatedBy'])->search(['name'])->latest()->get()
        ]);
    }


    /**
     * @param int|string $uid
     * @return View
     */
    public function edit(int | string $uid) :View{

        $gateway = MailGateway::where('uid',$uid)->where('code',"!=","104PHP")->firstOrFail();
        return view('admin.mail_gateway.edit',[
            'breadcrumbs' =>  ['Home'=>'admin.home','Gateways'=> "admin.mailGateway.list","Edit" => null],
            'title'       => 'Update '.strtoupper($gateway->name),
            'gateway'     =>  $gateway
        ]);
    }

    /**
     * update gateway
     *
     * @param GatewayRequest $request
     * @return RedirectResponse
     */
    public function update(GatewayRequest $request) :RedirectResponse{

        $gateway             = MailGateway::where('id',$request->id)->first();
        $gateway->credential = $request->input("credential");
        $gateway->save();
        return  back()->with(response_status('Updated successfully'));
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function test(Request $request) :RedirectResponse{

        $request->validate([
            "id"    => ["required",'exists:mail_gateways,id'],
            "email" => ["required",'email']
        ]);
        $gateway = MailGateway::where('id',$request->id)->firstOrFail();
        $code = [
            'time' =>  get_date_time(Carbon::now(),"Y-m-d H:i:s")
        ];

        $response = SendMail::mailNotifications("TEST_MAIL",$code , (object) ["name" =>"dear", 'email'=>$request->get('email')] , $gateway);
        return  back()->with(response_status((preg_replace('/[^a-zA-Z0-9@._\- ]/', '', $response['message'])), $response['status'] ? "success" :"error"));
    }

    /**
     * Update a specific gateway status
     *
     * @param Request $request
     * @return string
     */
    public function updateStatus(Request $request) :string{

        $request->validate([
            'id'       => ['required','exists:mail_gateways,uid'],
            'status'   => ['required',Rule::in(StatusEnum::toArray())]
        ]);

        $response['status']  = false;
        $response['message'] = translate('Default status cannot be updated');
        if($request->input('status') == StatusEnum::true->status()){
            MailGateway::where('uid',$request->input("id"))->update([
                'default'    => $request->input('status')
            ]);

            MailGateway::where('uid',"!=",$request->input('id'))->update([
                'default'    =>  StatusEnum::false->status()  ,
            ]);

            $response['status']  = true;
            $response['message'] = translate('Updated successfully');
        }

        $response['reload'] = true;

        return json_encode($response);
    }
}
