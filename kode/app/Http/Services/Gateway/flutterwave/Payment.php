<?php


namespace App\Http\Services\Gateway\flutterwave;

use App\Enums\DepositStatus;
use App\Models\PaymentLog;
use App\Http\Services\CurlService;
use App\Http\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Payment
{
    public static function paymentData(PaymentLog $log) :string
    {
        $gateway                  = ($log->method->parameters);
        $send['API_publicKey']    = $gateway->public_key ?? '';
        $send['customer_email']   = optional($log->user)->email;
        $send['amount']           = round($log->final_amount);
        $send['customer_phone']   = optional($log->user)->phone ?? '';
        $send['currency']         = $log->method->currency->code;
        $send['txref']            = $log->trx_code;
        $send['view']             = 'user.payment.flutterwave';
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

        if ($type != 'error') {

            $url         = 'https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify';
            $headers     = ['Content-Type:application/json'];
            $postParam   = array(
                "SECKEY" => $params->secret_key ?? '',
                "txref"  => $log->trx_code
            );

            $response = CurlService::curlPostRequestWithHeaders($url, $headers, $postParam);

            $response = json_decode($response);
            $data['gw_response']  = $response ;
            if ($response->data->status == "successful" && $response->data->chargecode == "00" && round($log->final_amount) == $response->data->amount && $log->method->currency->code == $response->data->currency) {

                $data['status']   = 'success';
                $data['message']  = trans('default.deposit_success');
                $status           = DepositStatus::value('PAID',true);
            } 
        } 

        $data['redirect'] = UserService::updateDepositLog($log,$status,$data);

        return $data;
    }
}
