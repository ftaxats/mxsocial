<?php

namespace App\Http\Services\Gateway\payeer;

use App\Enums\DepositStatus;
use App\Http\Services\UserService;
use App\Models\PaymentLog;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Payment
{
    public static function paymentData(PaymentLog $log) :string
    {
        $siteName = site_settings('site_name');
        $gateway  = ($log->method->parameters);
        $m_amount = number_format($log->amount, 2, '.', "");
        $arHash = [
            trim($gateway->merchant_id),
            $log->trx_code,
            $m_amount,
            $log->method->currency->code,
            base64_encode("Deposit To $siteName"),
            trim($gateway->secret_key)
        ];

        $val['m_shop']     = trim($gateway->merchant_id);
        $val['m_orderid']  = $log->trx_code;
        $val['m_amount']   = $m_amount;
        $val['m_curr']     = $log->method->currency->code;
        $val['m_desc']     = base64_encode("Deposit To $siteName");
        $val['m_sign']     = strtoupper(hash('sha256', implode(":", $arHash)));

        $send['val']       = $val;
        $send['view']      = 'user.payment.redirect';
        $send['method']    = 'get';
        $send['url']       = 'https://payeer.com/merchant';
        
    
        return json_encode($send);
    }


    public static function ipn(Request $request, PaymentLog $log ) :array
    {

        $data['status']       = 'error';
        $data['message']      = translate('Transaction failed.');
        $data['redirect']     = route('user.home');
        $data['gw_response']  = $request->all();
        $status               = DepositStatus::value('FAILED',true);

        $params               = ($log->method->parameters);

        if (isset($request->m_operation_id) && isset($request->m_sign)) {

            $sign_hash = strtoupper(hash('sha256', implode(":", array(
                $request->m_operation_id,
                $request->m_operation_ps,
                $request->m_operation_date,
                $request->m_operation_pay_date,
                $request->m_shop,
                $request->m_orderid,
                $request->m_amount,
                $request->m_curr,
                $request->m_desc,
                $request->m_status,
                $params->secret_key
            ))));

            if ($request->m_sign != $sign_hash) {
                $data['message'] = translate('digital signature not matched');
            } else {

                if ($request->m_amount == round($log->final_amount,2) && $request->m_curr == $log->method->currency->code && $request->m_status == 'success') {
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
