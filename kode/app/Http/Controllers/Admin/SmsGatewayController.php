<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\StatusEnum;
use App\Http\Requests\Admin\GatewayRequest;
use App\Models\Admin\SmsGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Traits\ModelAction;
class SmsGatewayController extends Controller
{

    use ModelAction;
    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['permissions:view_gateway'])->only('list');
        $this->middleware(['permissions:update_gateway'])->only(['edit','update','updateStatus']);
    }


    /**
     * gateway list
     *
     * @return View
     */
    public function list() :View{

        return view('admin.sms_gateway.list',[
            'breadcrumbs' => ['Home'=>'admin.home','Gateway'=> null],
            'title'       => 'Manage Gateway',
            'gateways'    => SmsGateway::with(['updatedBy'])->search(['name'])->latest()->get()
        ]);
    }


    /**
     * edit gateway
     * @param int|string $uid
     * @return View
     */
    public function edit(int | string $uid) :View{

        $gateway = SmsGateway::where('uid',$uid)->firstOrFail();

        return view('admin.sms_gateway.edit',[
            'breadcrumbs' =>  ['Home'=>'admin.home','Gateways'=> "admin.smsGateway.list", "Edit" => null],
            'title'       => 'Update '.$gateway->name,
            'gateway'     => $gateway
        ]);
    }

    /**
     * update gateway
     *
     * @param GatewayRequest $request
     * @return RedirectResponse
     */
    public function update(GatewayRequest $request) :RedirectResponse{

        $gateway             = SmsGateway::where('id',$request->id)->first();
        $gateway->credential = $request->get("credential");
        $gateway->save();
        return  back()->with(response_status('Updated successfully'));
    }

    /**
     * Update a specific gateway status
     *
     * @param Request $request
     * @return string
     */
    public function updateStatus(Request $request) :string{

        $request->validate([
            'id'       => ['required','exists:sms_gateways,uid'],
            'status'   => ['required',Rule::in(StatusEnum::toArray())]
        ]);

        $response['message']  = translate('Default status cannot be updated');
        $response['status']   = false;

        if($request->input("status") == StatusEnum::true->status()){
            SmsGateway::where('uid',$request->input('id'))->update([
                'default'    => $request->input("status"),
            ]);
            SmsGateway::where('uid',"!=",$request->input('id'))->update([
                'default' =>  StatusEnum::false->status()  ,
            ]);
            $response['status']   = true;
            $response['message']  = translate('Updated Successfully');
        }

        $response['reload']       = true;

        return json_encode($response);
    }
}
