<?php

namespace App\Http\Services\Gateway\skrill;

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
       
        $siteName    = site_settings('site_name');
        $gateway     = $log->method->parameters;
        $currency    = $log->method->currency->code;
        $val['pay_to_email']        = trim($gateway->skrill_email);
        $val['transaction_id']      = "$log->trx_code";

        $val['return_url']          = route('payment.success',['payment_intent' => base64_encode(
                                                json_encode([
                                                        "trx_number" => $log->trx_code,
                                                        "type"       =>"SUCCESS",
                                                ]))
                                            ]);
        $val['return_url_text']     = "Return   $siteName ";
        $val['cancel_url']          =  route('payment.failed',['payment_intent' => base64_encode(
                                                    json_encode([
                                                            "trx_number" => $log->trx_code,
                                                            "type"       =>"FAILED",
                                                    ])
                                                    )
                                                ]);
        $val['status_url']          = route('ipn',[$log->trx_code]);
        $val['language']            = 'EN';
        $val['amount']              = round($log->final_amount,2);
        $val['currency']            = "$currency";
        $val['detail1_description'] = "$siteName";
        $val['detail1_text']        = "Deposit To  $siteName";
        $val['logo_url']            = imageURL(@site_logo('user_site_logo')->file,'user_site_logo',true);


        $send['val']                = $val;
        $send['view']               = 'user.payment.redirect';
        $send['method']             = 'post';
        $send['url']                = 'https://www.moneybookers.com/app/payment.pl';


        return json_encode($send);


    }



    public static function ipn(Request $request , PaymentLog $log ) :array
    {

        $data['status']      = 'error';
        $data['message']     = translate('Unable to Process.');
        $data['redirect']    = route('user.home');
        $data['gw_response'] = $request->all();
        $status              = DepositStatus::value('FAILED',true);

        $gateway             = $log->method->parameters;
        $currency            = $gateway->method->currency->code;

        $concatFields = $_POST['merchant_id']
            . $_POST['transaction_id']
            . strtoupper(md5($gateway->secret_key))
            . $_POST['mb_amount']
            . $_POST['mb_currency']
            . $_POST['status'];

        if (strtoupper(md5($concatFields)) == $_POST['md5sig'] && $_POST['status'] == 2 && $_POST['pay_to_email'] == trim($gateway->skrill_email)) {
            
            $data['status']   = 'success';
            $data['message']  = trans('default.deposit_success');
            $status           = DepositStatus::value('PAID',true);
        }

        $data['redirect'] = UserService::updateDepositLog($log,$status,$data);
        
        return $data;

    }




   
}
