<?php
namespace App\Http\Services\Gateway\paypal;

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
        $siteName             = site_settings('site_name');
        $gateway              = ($log->method->parameters);

        $param['cleint_id']   = $gateway->cleint_id ?? '';
        $param['description'] = "Deposit To {$siteName} Account";
        $param['custom_id']   = $log->trx_code;
        $param['amount']      = round($log->final_amount);
        $param['currency']    = $log->method->currency->code;
        $param['view']        = 'user.payment.paypal';

        return json_encode($param);
    }

    public static function ipn(Request $request,PaymentLog $log, mixed $type = null) :array
    {
        $data['status']      = 'error';
        $data['message']     = translate('Invalid amount.');
        $data['redirect']    = route('user.home');

        $status              = DepositStatus::value('FAILED',true);

        $url         = "https://api.paypal.com/v2/checkout/orders/{$type}";
        $params      = ($log->method->parameters);
        $client_id   = $params->cleint_id ?? '';
        $secret      = $params->secret ?? '';
        $headers = [
            'Content-Type:application/json',
            'Authorization:Basic ' . base64_encode("{$client_id}:{$secret}")
        ];
        $response     = CurlService::curlGetRequestWithHeaders($url, $headers);
        $paymentData  = json_decode($response, true);
        $data['gw_response'] = $paymentData;
        
        if (isset($paymentData['status']) && $paymentData['status'] == 'COMPLETED') {

            if ($paymentData['purchase_units'][0]['amount']['currency_code'] == $log->method->currency->code && $paymentData['purchase_units'][0]['amount']['value'] == round($log->final_amount)) {
                
                $data['status']   = 'success';
                $data['message']  = trans('default.deposit_success');
                $status           = DepositStatus::value('PAID',true);

            } 
        } ;

        $data['redirect'] = UserService::updateDepositLog($log,$status,$data);
        return $data;
    }
}
