<?php

namespace App\Http\Services\Gateway\btcpay;

use App\Enums\DepositStatus;
use App\Http\Services\UserService;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use BTCPayServer\Client\Invoice;
use BTCPayServer\Client\Webhook;
use BTCPayServer\Util\PreciseNumber;
use Exception;

class Payment
{
    public static function paymentData(PaymentLog $log) :string {

        
        $gateway = ($log->method->parameters);

        $client  = new Invoice($gateway->server_name, $gateway->api_key);

        try {
            $amount  = PreciseNumber::parseFloat($log->final_amount);
            $invoice = $client->createInvoice(
                $gateway->store_id,
                $log->method->currency->code,
                $amount,
                $log->trx_code
            );

            session()->put("trx_code",$log->trx_code);

            $send['redirect']     = true;
            $send['redirect_url'] = $invoice['checkoutLink'];



        } catch (Exception $e) {
            $send['error']     = true;
            $send['message']   = $e->getMessage();;
        }

        return json_encode($send);
    }

    public static function ipn(mixed $request, ? PaymentLog $depositLog = null ) :array {
    


        $data['status']      = 'error';
        $data['message']     = translate('Unable to Process.');
        $data['redirect']    = route('user.home');
        $data['gw_response'] = $request->all();
        $status              = DepositStatus::value('FAILED',true);

        $rawPostData = file_get_contents("php://input");

        if ($rawPostData) {
            $headers = getallheaders();
            foreach ($headers as $key => $value) {
                if (strtolower($key) === 'btcpay-sig') {
                    $signature = $value;
                }
            }

            $gateway = ($depositLog->method->parameters);

            if (isset($signature) || self::validWebhookRequest($signature, $rawPostData, $gateway->secret_code)) {
                    try {
                        $postData = json_decode($rawPostData, false, 512, JSON_THROW_ON_ERROR);
                        if( isset($postData->invoiceId) 
                            && $postData->type == 'InvoicePaymentSettled' 
                            && $postData->payment->status == 'Settled' 
                            && !$postData->afterExpiration){
                            $data['status']   = 'success';
                            $data['message']  = trans('default.deposit_success');
                            $status           = DepositStatus::value('PAID',true);
                        }

                    } catch (\Exception $ex) {
                        $data['message']  = strip_tags($ex->getMessage());
                    }

            }

        }



        $data['redirect'] = UserService::updateDepositLog($depositLog,$status,$data);

        session()->forget("trx_code");

        return $data;

    }



    private static function validWebhookRequest(string $signature, string $requestData, $secretCode): bool
    {
        return Webhook::isIncomingWebhookRequestValid($requestData, $signature, $secretCode);
    }

}
