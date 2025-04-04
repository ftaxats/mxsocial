<?php

namespace App\Http\Services\Gateway\paystack;

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
        $gateway          = ($log->method->parameters);
        $send['key']      = $gateway->public_key ?? '';
        $send['email']    = optional($log->user)->email;
        $send['amount']   = round(($log->final_amount * 100),2);
        $send['currency'] = $log->method->currency->code;
        $send['ref']      = $log->trx_code;
        $send['view']     = 'user.payment.paystack';
        return json_encode($send);
    }

    public static function ipn(Request $request, PaymentLog $log) :array {

        $data['status']      = 'error';
        $data['message']     = translate('Invalid amount.');
        $data['redirect']    = route('user.home');
        $data['gw_response'] = $request->all();
        $status              = DepositStatus::value('FAILED',true);

        $params      = ($log->method->parameters);
        $secret_key  = $params->secret_key ?? '';
        $url         = 'https://api.paystack.co/transaction/verify/' . $log->trx_code;
        $headers = [
            "Authorization: Bearer {$secret_key}"
        ];
        $response = CurlService::curlGetRequestWithHeaders($url, $headers);
        $response = json_decode($response, true);

        if ($response && isset($response['data'])) {
            $data['message'] = Arr::get($response['data'],"gateway_response", translate('Invalid amount'));
    
            if ($response['data']['status'] == 'success') {
                $payable = round(($log->final_amount * 100),2);
                if (round($response['data']['amount']) == $payable && $response['data']['currency'] == $log->method->currency->code) {
                    $data['status']   = 'success';
                    $data['message']  = trans('default.deposit_success');
                    $status           = DepositStatus::value('PAID',true);
                } 
            }
        } 

        $data['redirect'] = UserService::updateDepositLog($log,$status,$data);
        return $data;
    }
}
