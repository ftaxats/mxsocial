<?php

namespace App\Http\Services\Gateway\bkash;

use App\Enums\DepositStatus;
use App\Enums\StatusEnum;

use App\Http\Services\UserService;
use App\Models\PaymentLog;
use Config;
use Illuminate\Support\Arr;
use Karim007\LaravelBkashTokenize\Facade\BkashPaymentTokenize;
use Illuminate\Http\Request;

class Payment
{

    public static function paymentData(PaymentLog $log) :string  {

        $gateway = ($log->method->parameters);
        self::setConfig($gateway,$log->trx_code);

        $request                           = [];
        $inv                               = uniqid();
        $request['intent']                 = 'sale';
        $request['mode']                   = '0011'; 
        $request['payerReference']         = $inv;
        $request['currency']               = 'BDT';
        $request['amount']                 = round($log->final_amount);
        $request['merchantInvoiceNumber']  = $log->trx_code;
        $request['callbackURL']            = config("bkash.callbackURL");

        $response =  BkashPaymentTokenize::cPayment(json_encode($request));

        
        $send['error']   = true;
        $send['message'] = translate("Invalid Request");

        if (isset($response['bkashURL'])) {

            $send['redirect']     = true;
            $send['redirect_url'] = $response['bkashURL'];
        }
      
        return json_encode($send);
    }


    public static function setConfig(mixed $gateway ,string $trx_code) :void{


        $sandbox = $gateway->sandbox == StatusEnum::false->status()
        ? false
        : true ;

        $config = [
            'sandbox'          =>  $sandbox ,
            'bkash_app_key'    =>  $gateway->api_key ,
            'bkash_app_secret' =>  $gateway->api_secret ,
            'bkash_username'   =>  $gateway->username ,
            'bkash_password'   =>  $gateway->password ,
            'callbackURL'      =>  route('ipn', [$trx_code]) ,
            "timezone"         => "Asia/Dhaka"
        ];
        
        Config::set('bkash',  $config );

    }


    public static function ipn(Request $request, PaymentLog $depositLog) :array {

        $gateway = ($depositLog->method->parameters);

        self::setConfig($gateway,$depositLog->trx_code);

        $data['status']      = 'error';
        $data['message']     = translate('Invalid amount.');
        $data['redirect']    = route('user.home');
        $data['gw_response'] = $request->all();

        $status              = DepositStatus::value('FAILED',true);

        if ($request->status == 'success'){
            $response = BkashPaymentTokenize::executePayment($request->paymentID); 
        
            if (isset($response['statusCode']) && $response['statusCode'] == "0000" && $response['transactionStatus'] == "Completed") {
                
                $status           = DepositStatus::value('PAID',true);
                $data['status']   = 'success';
                $data['message']  = trans('default.deposit_success');

            }
  
        }else if ($request->status == 'cancel'){
            $status           = DepositStatus::value('CANCEL',true);
            $data['message']  = translate('Payment Cancel');
        }


        $data['redirect'] = UserService::updateDepositLog($depositLog,$status,$data);

        return $data;
    }

}