<?php

namespace App\Http\Services\Gateway\mercadopago;

use App\Enums\DepositStatus;
use App\Http\Services\CurlService;
use App\Http\Services\UserService;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Payment
{
    const SANDBOX = false;

    public static function paymentData(PaymentLog $log)
    {
        $siteName    = site_settings('site_name');
        $gateway     = ($log->method->parameters);
        $url         = "https://api.mercadopago.com/checkout/preferences?access_token=" . $gateway->access_token;

        $headers = [
            "Content-Type: application/json",
        ];
        $postParam = [
            'items' => [
                [
                    'id' => $log->trx_code,
                    'title' => "Deposit",
                    'description' => 'Deposit from '.$log->user->username,
                    'quantity' => 1,
                    'currency_id' => $log->method->currency->code,
                    'unit_price' => round($log->final_amount, 2)
                ]
            ],
            'payer' => [
                'email' => optional($log->user)->email ?? '',
            ],
            'back_urls' => [
                'success' => route('payment.success',['payment_intent' => base64_encode(
                    json_encode([
                            "trx_number" => $log->trx_code,
                            "type"       =>"SUCCESS",
                    ]))
                ]),
                'pending' => '',
                'failure' => route('payment.failed',['payment_intent' => base64_encode(
                    json_encode([
                            "trx_number" => $log->trx_code,
                            "type"       =>"FAILED",
                    ])
                    )
                ]),
            ],
            'notification_url' => route('ipn', [$log->trx_code]),
            'auto_return' => 'approved',
        ];
        $response = CurlService::curlPostRequestWithHeaders($url, $headers, $postParam);
        $response = json_decode($response);


        $send['preference']  =  $log->trx_code;
        $send['view']        = 'user.payment.mercado';
        
                
        $send['error']   = true;
        $send['message'] = @$response->message ?? translate("Invalid Request");

        if(isset($response->auto_return) && $response->auto_return == 'approved') {

            $send['redirect']     = true;
            $send['redirect_url'] = $response->init_point;

            if (self::SANDBOX)  $send['redirect_url'] = $response->sandbox_init_point;
        }
        return json_encode($send);
    }

    public static function ipn(Request $request, PaymentLog $log ) :array
    {

        $gateway       = ($log->method->parameters);
        $paymentId     = json_decode(json_encode($request->all()))->data->id;
        $url           = "https://api.mercadopago.com/v1/payments/" . $paymentId. "?access_token=" . $gateway->access_token;
        $response      = CurlService::curlGetRequest($url);
        $paymentData   = json_decode($response);

        $data['status']       = 'error';
        $data['message']      = translate('Transaction failed.');
        $data['redirect']     = route('user.home');
        $data['gw_response']  = $paymentData;
        $status               = DepositStatus::value('FAILED',true);

        if (isset($paymentData->status) && $paymentData->status == 'approved') {

            $data['status']   = 'success';
            $data['message']  = trans('default.deposit_success');
            $status           = DepositStatus::value('PAID',true);

        }
      
        $data['redirect'] = UserService::updateDepositLog($log,$status,$data);


        return $data;
    }
}
