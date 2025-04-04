<?php

namespace App\Http\Services\Gateway\cashmaal;

use App\Enums\DepositStatus;
use App\Http\Services\UserService;
use App\Models\PaymentLog;
use Illuminate\Http\Request;

class Payment
{
    public static function paymentData(PaymentLog $log) :string {

        $gateway = ($log->method->parameters);

        $val['pay_method']    = " ";
        $val['amount']        = round($log->final_amount, 2);
        $val['currency']      = $log->method->currency->code;
        $val['succes_url']    = route('payment.success',['payment_intent' => base64_encode(
                                            json_encode([
                                                    "trx_number" => $log->trx_code,
                                                    "type"       =>"SUCCESS",
                                            ]))
                                        ]);
        $val['cancel_url']    = route('payment.failed',['payment_intent' => base64_encode(
                                            json_encode([
                                                    "trx_number" => $log->trx_code,
                                                    "type"       =>"FAILED",
                                            ])
                                            )
                                        ]);
        $val['client_email']  = optional($log->user)->email;
        $val['web_id']        = $gateway->web_id;
        $val['order_id']      = $log->trx_code;
        $val['addi_info']     = "Payment";

        $send['url']          = 'https://www.cashmaal.com/Pay/';
        $send['method']       = 'post';
        $send['view']         = 'user.payment.redirect';
        $send['val']          = $val;

        return json_encode($send);
    }

    public static function ipn(Request $request, PaymentLog $depositLog) :array {
    

        $data['status']      = 'error';
        $data['message']     = translate('Unable to Process.');
        $data['redirect']    = route('user.home');
        $data['gw_response'] = $request->all();
        $status              = DepositStatus::value('FAILED',true);

        if ($request->currency == $depositLog->method->currency->code && ($request->amount == round($depositLog->final_amount, 2))) {
            $data['status']   = 'success';
            $data['message']  = trans('default.deposit_success');
            $status           = DepositStatus::value('PAID',true);
        }

        $data['redirect'] = UserService::updateDepositLog($depositLog,$status,$data);

        return $data;

    }
}
