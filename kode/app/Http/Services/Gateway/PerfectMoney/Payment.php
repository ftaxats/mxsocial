<?php

namespace App\Http\Services\Gateway\PerfectMoney;

use App\Enums\DepositStatus;
use App\Http\Services\UserService;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Payment
{
    public static function paymentData(PaymentLog $log) :string
    {
    

        $siteName                     = site_settings('site_name');
        $gateway                      = ($log->method->parameters);

        $gateway_currency             = $log->method->currency->code;


        $val['PAYEE_ACCOUNT']         = trim($gateway->wallet_id);
        $val['PAYEE_NAME']            = $siteName;
        $val['PAYMENT_ID']            = "$log->trx_code";
        $val['PAYMENT_AMOUNT']        = round($log->final_amount,2);
        $val['PAYMENT_UNITS']         = "$gateway_currency";

        $val['STATUS_URL']            = route('ipn',[$log->trx_code]);
        $val['PAYMENT_URL']           = route('payment.success',['payment_intent' => base64_encode(
                                                    json_encode([
                                                            "trx_number" => $log->trx_code,
                                                            "type"       =>"SUCCESS",
                                                    ]))
                                                ]);
        $val['PAYMENT_URL_METHOD']    = 'POST';
        $val['NOPAYMENT_URL']         = route('payment.failed',['payment_intent' => base64_encode(
                                                json_encode([
                                                        "trx_number" => $log->trx_code,
                                                        "type"       =>"FAILED",
                                                ])
                                                )
                                            ]);
        $val['NOPAYMENT_URL_METHOD']  = 'POST';
        $val['SUGGESTED_MEMO']        = $log->user->username;
        $val['BAGGAGE_FIELDS']        = 'IDENT';

        $send['val']                  = $val;
        $send['view']                 = 'user.payment.redirect';
        $send['method']               = 'post';
        $send['url']                  = 'https://perfectmoney.is/api/step1.asp';
        return json_encode($send);

    }

    public static function ipn(Request $request, PaymentLog $log) :array {

        $data['status']      = 'error';
        $data['message']     = translate('Invalid amount.');
        $data['redirect']    = route('user.home');
        $data['gw_response'] = $request->all();
        $status              = DepositStatus::value('FAILED',true);

        $params              = ($log->method->parameters);

        $passphrase          = strtoupper(md5($params->passphrase));

        define('ALTERNATE_PHRASE_HASH', $passphrase);
        define('PATH_TO_LOG', '/somewhere/out/of/document_root/');
        $string =
            $_POST['PAYMENT_ID'] . ':' . $_POST['PAYEE_ACCOUNT'] . ':' .
            $_POST['PAYMENT_AMOUNT'] . ':' . $_POST['PAYMENT_UNITS'] . ':' .
            $_POST['PAYMENT_BATCH_NUM'] . ':' .
            $_POST['PAYER_ACCOUNT'] . ':' . ALTERNATE_PHRASE_HASH . ':' .
            $_POST['TIMESTAMPGMT'];

        $hash = strtoupper(md5($string));
        $hash2 = $_POST['V2_HASH'];

        if ($hash == $hash2) {

            foreach ($_POST as $key => $value) {
                $details[$key] = $value;
            }
            $data['gw_response'] = $details;
            $amo = $_POST['PAYMENT_AMOUNT'];
            $unit = $_POST['PAYMENT_UNITS'];
            $track = $_POST['PAYMENT_ID'];
            if ($_POST['PAYEE_ACCOUNT'] == trim($params->wallet_id) && $unit == $log->method->currency->code && $amo == round($log->final_amount,2)) {
                
                $data['status']   = 'success';
                $data['message']  = trans('default.deposit_success');
                $status           = DepositStatus::value('PAID',true);
            }
        }
        $data['redirect'] = UserService::updateDepositLog($log,$status,$data);

        return $data;


     
    }
}
