<?php

namespace App\Http\Services\Gateway\coingate;

use App\Enums\DepositStatus;
use App\Http\Services\UserService;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use CoinGate\CoinGate;
use CoinGate\Merchant\Order;
use App\Http\Services\CurlService;
class Payment
{
    public static function paymentData(PaymentLog $log) :string {

        $gateway    = ($log->method->parameters);

        try {
            CoinGate::config(array(
                'environment' => 'live', 
                'auth_token'  => $gateway->api_key
            ));
        } catch (\Exception $e) {

            $send['error']   = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }

        $siteName   = site_settings('site_name');

        $post_params = array(
            'order_id'         => $log->trx_code,
            'price_amount'     => round($log->final_amount,2),
            'price_currency'   => $log->method->currency->code,
            'receive_currency' => $log->method->currency->code,
            'callback_url'     => route('ipn',[$log->trx_code]),
            'cancel_url'       => route('payment.failed',['payment_intent' => base64_encode(
                                        json_encode([
                                                "trx_number" => $log->trx_code,
                                                "type"       =>"FAILED",
                                        ])
                                        )
                                    ]),
            'success_url'      => route('payment.success',['payment_intent' => base64_encode(
                                            json_encode([
                                                    "trx_number" => $log->trx_code,
                                                    "type"       =>"SUCCESS",
                                            ]))
                                        ]),
            'title'            => 'Deposit to ' . $siteName,
            'token'            => $log->trx_code
        );


        $send['error']   = true;
        $send['message'] = translate('Unexpected Error! Please Try Again');

        try {
            $order = Order::create($post_params);
        } catch (\Exception $e) {
            $send['error']    = true;
            $send['message']  = $e->getMessage();
            return json_encode($send);
        }
        if (@$order) {
            $send['redirect']     = true;
            $send['redirect_url'] = $order->payment_url;
        } 
        return json_encode($send);
       
    }

    public static function ipn(Request $request, PaymentLog $depositLog) :array {

        $data['status']      = 'error';
        $data['message']     = translate('Unable to Process.');
        $data['redirect']    = route('user.home');
        $data['gw_response'] = $request->all();
        $status              = DepositStatus::value('FAILED',true);


        $ip = $_SERVER['REMOTE_ADDR'];
        $url = 'https://api.coingate.com/v2/ips-v4';

        $response = CurlService::curlContent($url);
        $data['gw_response']  =    $response;
        if (strpos($response, $ip) !== false) {
            if ($_POST['status'] == 'paid' && $_POST['price_amount'] == round($depositLog->final_amount,2) ) {
                $data['status']   = 'success';
                $data['message']  = trans('default.deposit_success');
                $status           = DepositStatus::value('PAID',true);
            }
        }


        $data['redirect'] = UserService::updateDepositLog($depositLog,$status,$data);
        return $data;
    
       
    }
}
