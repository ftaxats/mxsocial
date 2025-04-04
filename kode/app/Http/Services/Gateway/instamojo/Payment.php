<?php

namespace App\Http\Services\Gateway\instamojo;

use App\Enums\DepositStatus;
use App\Http\Services\CurlService;
use App\Http\Services\UserService;
use App\Models\PaymentLog;
use Illuminate\Http\Request;

class Payment
{
    public static function paymentData(PaymentLog $log) :string
    {
        $siteName   = site_settings('site_name');
        $gateway    = ($log->method->parameters);
        $api_key    = ($gateway->api_key ?? '');
        $auth_token = ($gateway->auth_token ?? '');
        $url        = 'https://instamojo.com/api/1.1/payment-requests/';
        $headers = [
            "X-Api-Key:$api_key",
            "X-Auth-Token:$auth_token"
        ];
  
        $postParam = [
            'purpose'                 => 'Payment to ' . $siteName,
            'amount'                  => round($log->final_amount),
            'buyer_name'              => optional($log->user)->name ?? 'User Name',
            'redirect_url'            => route('payment.success',
                                            ['payment_intent' => base64_encode(
                                                json_encode([
                                                        "trx_number" => $log->trx_code,
                                                        "type"       =>"SUCCESS",
                                                 ])
                                                )
                                            ]),
            'webhook'                 => route('ipn', [$log->trx_code]),
            'email'                   => optional($log->user)->email ?? 'example@example.com',
            'send_email'              => true,
            'allow_repeated_payments' => false
        ];

        $response = CurlService::curlPostRequestWithHeaders($url, $headers, $postParam);
        $response = json_decode($response);



        $send['error']   = true;
        $send['message'] = @$response->message?? translate("Invalid Request");

        if ($response->success) {
            $send['redirect']     = true;
            $send['redirect_url'] = $response->payment_request[0]->longurl;
        } 
        return json_encode($send);
    }

    public static function ipn(Request $request,PaymentLog $log = null,string $type = null) :array
    {

        $params = ($log->method->parameters);

        $data['status']       = 'error';
        $data['message']      = translate('Transaction failed.');
        $data['redirect']     = route('user.home');
        $data['gw_response']  = $request->all();
        $status               = DepositStatus::value('FAILED',true);

        $salt     = trim($params->salt);
        $imData   = $_POST;
        $macSent  = $imData['mac'];
        unset($imData['mac']);
        ksort($imData, SORT_STRING | SORT_FLAG_CASE);
        $mac = hash_hmac("sha1", implode("|", $imData), $salt);

        if ($macSent == $mac && $imData['status'] == "Credit") {

            $data['status']   = 'success';
            $data['message']  = trans('default.deposit_success');
            $status           = DepositStatus::value('PAID',true);
        }



        $data['redirect'] = UserService::updateDepositLog($log,$status,$data);

        return $data;
    }
}
