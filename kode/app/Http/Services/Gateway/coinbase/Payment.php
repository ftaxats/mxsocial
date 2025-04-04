<?php

namespace App\Http\Services\Gateway\coinbase;

use App\Enums\DepositStatus;
use App\Http\Services\UserService;
use App\Models\PaymentLog;
use Illuminate\Http\Request;

class Payment
{
    public static function paymentData(PaymentLog $log) :string {


        $siteName   = site_settings('site_name');

        $gateway    = ($log->method->parameters);
        
        session()->put('trx_code',$log->trx_code);

        $url = 'https://api.commerce.coinbase.com/charges';
        $array = [
            'name' => $log->user->username,
            'description'  => "Deposit to " . $siteName,
            'local_price'  => [
                'amount'   => round($log->final_amount),
                'currency' => $log->method->currency->code
            ],
            'metadata' => [
                'trx' => $log->trx_code
            ],
            'pricing_type' => "fixed_price",
            'redirect_url' => route('payment.success',['payment_intent' => base64_encode(
                json_encode([
                        "trx_number" => $log->trx_code,
                        "type"       =>"SUCCESS",
                ]))
            ]),
            'cancel_url' => route('payment.failed',['payment_intent' => base64_encode(
                                        json_encode([
                                                "trx_number" => $log->trx_code,
                                                "type"       =>"FAILED",
                                        ])
                                        )
                                    ])
        ];

        $jsonData = json_encode($array);
        $ch       = curl_init();
        $apiKey   = $gateway->api_key;
        $header   = array();
        $header[] = 'Content-Type: application/json';
        $header[] = 'X-CC-Api-Key: ' . "$apiKey";
        $header[] = 'X-CC-Version: 2018-03-22';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);


        $result = json_decode($result);

        if (@$result->error == '') {
            $send['redirect']     = true;
            $send['redirect_url'] = $result->data->hosted_url;
        } else {
            $send['error']        = true;
            $send['message']      = @$result->error->message??'Some problem ocurred with api.';
        }

        return json_encode($send);
    }

    public static function ipn(Request $request, PaymentLog $depositLog) :array {

        $data['status']      = 'error';
        $data['message']     = translate('Unable to Process.');
        $data['redirect']    = route('user.home');

        $status              = DepositStatus::value('FAILED',true);

        $postdata    = file_get_contents("php://input");
        $res         = json_decode($postdata);
        $data['gw_response'] =       $res  ;
        $gateway     = ($depositLog->method->parameters);
        $headers     = apache_request_headers();
        $headers     = json_decode(json_encode($headers),true);
        $sentSign    = $headers['X-Cc-Webhook-Signature'];
        
        $sig         = hash_hmac('sha256', $postdata, $gateway->webhook_secret);
        if ($sentSign == $sig && $res->event->type == 'charge:confirmed') {

            $data['status']   = 'success';
            $data['message']  = trans('default.deposit_success');
            $status           = DepositStatus::value('PAID',true);
            
        }
        $data['redirect'] = UserService::updateDepositLog($depositLog,$status,$data);
        
        session()->forget("trx_code");
        return $data;
    
    }
}
