<?php

namespace App\Http\Services\Gateway\razorpay;

use App\Enums\DepositStatus;
use App\Http\Services\UserService;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Razorpay\Api\Api;

require_once('razorpay-php/Razorpay.php');

class Payment
{
    public static function paymentData(PaymentLog $log) :string
    {
   
        $gateway          = ($log->method->parameters);
        $api_key          = $gateway->key_id ?? '';
        $api_secret       = $gateway->key_secret ?? '';
        $razorPayApi      = new Api($api_key, $api_secret);

        $finalAmount      = round($log->final_amount * 100, 2);
        $gatewayCurrency  =  $log->method->currency->code;

        $trx = $log->trx_code;
        $razorOrder = $razorPayApi->order->create(
            array(
                'receipt'         => $trx,
                'amount'          => $finalAmount,
                'currency'        => $gatewayCurrency,
                'payment_capture' => '0'
            )
        );


        $val['key']             = $api_key;
        $val['amount']          = $finalAmount;
        $val['currency']        = $gatewayCurrency;
        $val['order_id']        = $razorOrder['id'];
        $val['buttontext']      = "Deposit via Razorpay";
        $val['name']            = optional($log->user)->username;
        $val['description']     = "Deposit By Razorpay";
        $val['image']           = imageURL(@site_logo('site_logo')->file,"site_logo",true);
        $val['prefill.name']    = optional($log->user)->username;
        $val['prefill.email']   = optional($log->user)->email;
        $val['prefill.contact'] = optional($log->user)->phone;
        $val['theme.color']     = "#2ecc71";
        $send['val']            = $val;

        $send['method']       = 'POST';
        $send['url']          = route('ipn',[$log->trx_code]);
        $send['custom']       = $trx;
        $send['checkout_js']  = "https://checkout.razorpay.com/v1/checkout.js";
        $send['view']         = 'user.payment.razorpay';

        
        return json_encode($send);
    }

    public static function ipn(Request $request, PaymentLog $log) :array
    {

        $data['status']      = 'error';
        $data['message']     = translate('Invalid amount.');
        $data['redirect']    = route('user.home');
        $data['gw_response'] = $request->all();
        $status              = DepositStatus::value('FAILED',true);
        $params              = ($log->method->parameters);
        $api_secret          = $params->key_secret ?? '';
        $signature           = hash_hmac('sha256', $request->razorpay_order_id . "|" . $request->razorpay_payment_id, $api_secret);

        if ($signature == $request->razorpay_signature) {
            $data['status']   = 'success';
            $data['message']  = trans('default.deposit_success');
            $status           = DepositStatus::value('PAID',true);
        }

    
        $data['redirect'] = UserService::updateDepositLog($log,$status,$data);
        
        return $data;
    }
}
