<?php

namespace App\Http\Services\Gateway\voguepay;

use App\Enums\DepositStatus;
use App\Http\Services\CurlService;
use App\Http\Services\UserService;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Payment
{
    public static function paymentData(PaymentLog $log) :string 
    {
       

        $gateway                = ($log->method->parameters);
        $send['v_merchant_id']  = $gateway->merchant_id;
        $send['notify_url']     = route('ipn',[$log->trx_code]);
        $send['cur']            = $log->method->currency->code;
        $send['merchant_ref']   = $log->trx_code;
        $send['memo']           = 'Payment';
        $send['store_id']       = $log->user_id;
        $send['custom']         = $log->trx_code;
        $send['Buy']            = round($log->final_amount,2);
        $send['view']           = 'user.payment.voguepay';
        
        return json_encode($send);
    }



    public static function ipn(Request $request , PaymentLog $log ) :array
    {

        $data['status']      = 'error';
        $data['message']     = translate('Invalid amount.');
        $data['redirect']    = route('user.home');
        $data['gw_response'] = $request->all();
        $status              = DepositStatus::value('FAILED',true);

        $trx        = $log->trx_code;
        $url        = "https://voguepay.com/?v_transaction_id=$trx&type=json";
        $response   = CurlService::curlGetRequest($url);
        $vougueData = json_decode($response);

        $gateway                = ($log->method->parameters);

        if ($vougueData->status == "Approved" && $vougueData->merchant_id == $gateway->merchant_id && $vougueData->total == round($log->final_amount,2) && $vougueData->cur_iso == $log->method->currency->code) {

            $data['status']   = 'success';
            $data['message']  = trans('default.deposit_success');
            $status           = DepositStatus::value('PAID',true);
        }

        $data['redirect'] = UserService::updateDepositLog($log,$status,$data);
        return $data;

    }
}
