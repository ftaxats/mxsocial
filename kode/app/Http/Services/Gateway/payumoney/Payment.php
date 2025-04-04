<?php

namespace App\Http\Services\Gateway\payumoney;

use App\Enums\DepositStatus;
use App\Http\Services\CurlService;
use App\Http\Services\PaymentService;
use App\Http\Services\UserService;
use App\Models\Admin\PaymentMethod;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Payment
{
    public static function paymentData(PaymentLog $log) :string
    {
        $siteName          = site_settings('site_name');
        $gateway           = ($log->method->parameters);
        $hashSequence      = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";

        $hashVarsSeq       = explode('|', $hashSequence);
        $hash_string       = '';
        $send['val'] = [
            'key'              => $gateway->merchant_key ?? '',
            'txnid'            => $log->trx_code,
            'amount'           => (int)round($log->final_amount,2),
            'firstname'        => optional($log->user)->username ,
            'email'            => optional($log->user)->email ?? '',
            'productinfo'      => $log->trx_code ?? 'Order',
            'surl'             => route('ipn', [$log->trx_code]),
            'furl'             => route('payment.failed',['payment_intent' => base64_encode(
                                            json_encode([
                                                    "trx_number" => $log->trx_code,
                                                    "type"       =>"FAILED",
                                            ])
                                            )
                                        ]),
            'service_provider' => $siteName ,
        ];

        foreach ($hashVarsSeq as $hash_var) {
            $hash_string .= $send['val'][$hash_var] ?? '';
            $hash_string .= '|';
        }
       
        $hash_string .= $gateway->salt;

        $send['val']['hash'] = strtolower(hash('sha512', $hash_string));


        $send['view']        = 'user.payment.redirect';
        $send['method']      = 'post';
        $send['url']         = 'https://secure.payu.in/_payment';
        return json_encode($send);
    }

    public static function ipn(Request $request , PaymentLog $log ) :array
    {

        $data['status']      = 'error';
        $data['message']     = translate('Invalid amount.');
        $data['redirect']    = route('user.home');
        $data['gw_response'] = $request->all();
        $status              = DepositStatus::value('FAILED',true);
        $params              = ($log->method->parameters);

        if (isset($request->status) && $request->status == 'success') {
            if ( ($params->merchant_key ?? '') == $request->key && $log->trx_code == $request->txnid  && round($log->final_amount,2) <= $request->amount ) {
                $data['status']   = 'success';
                $data['message']  = trans('default.deposit_success');
                $status           = DepositStatus::value('PAID',true);
            }
        } 


        $data['redirect'] = UserService::updateDepositLog($log,$status,$data);

        return $data;
    }
}
