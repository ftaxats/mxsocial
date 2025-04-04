<?php

namespace App\Http\Services\Gateway\mollie;

use App\Enums\DepositStatus;
use Mollie\Laravel\Facades\Mollie;
use App\Http\Services\UserService;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Payment
{
    public static function paymentData(PaymentLog $log) :string
    {
        $siteName   = site_settings('site_name');
        $gateway    = ($log->method->parameters);

        config(['mollie.key' => trim($gateway->api_key)]);
        $currency = $log->method->currency->code;

        $payment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => "$currency",
                'value' => '' . sprintf('%0.2f', round($log->final_amount, 2)) . '',
            ],
            'description' => "Deposit To   $siteName Account",
            'redirectUrl' => route('ipn', [$log->trx_code]),
            'metadata' => [
                "order_id" => $log->trx_code,
            ],
        ]);
        $payment = Mollie::api()->payments()->get($payment->id);

        session()->put('payment_id',$payment->id);
        session()->put('deposit_id',$log->id);

        $send['redirect']      = true;
        $send['redirect_url']  = $payment->getCheckoutUrl();
        return json_encode($send);
    }

    public static function ipn(Request $request, PaymentLog $log = null) :array
    {

        $gateway = ($log->method->parameters);
        config(['mollie.key' => trim($gateway->api_key)]);
        $payment = Mollie::api()->payments()->get(session()->get('payment_id'));
        $data['status']       = 'error';
        $data['message']      = translate('Transaction failed.');
        $data['redirect']     = route('user.home');
        $data['gw_response']  = $payment;
        $status               = DepositStatus::value('FAILED',true);

        if ($payment->status == "paid") {

            $data['status']   = 'success';
            $data['message']  = trans('default.deposit_success');
            $status           = DepositStatus::value('PAID',true);
        } 

        $data['redirect'] = UserService::updateDepositLog($log,$status,$data);

        return $data;
    }
}
