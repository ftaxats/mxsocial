<?php

namespace App\Http\Services\Gateway\authorizedotnet;

use App\Enums\DepositStatus;
use App\Http\Services\CurlService;
use App\Http\Services\UserService;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\CreditCardType;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use net\authorize\api\contract\v1\PaymentType;
use net\authorize\api\contract\v1\TransactionRequestType;
use net\authorize\api\controller\CreateTransactionController;
class Payment
{
    public static function paymentData(PaymentLog $log) :string 
    {
       
        $gateway         = $log->method->parameters;
        $send['track']   = $log->trx_code;
        $send['view']    = 'user.payment.authorizenet';
        $send['method']  = 'post';
        $send['url']     = route('ipn',[$log->trx_code]);
        return json_encode($send);

    }



    public static function ipn(Request $request , PaymentLog $log ) :array
    {


        $request->validate([
            'cardNumber' => 'required',
            'cardExpiry' => 'required',
            'cardCVC' => 'required',
        ]);

        $data['status']      = 'error';
        $data['message']     = translate('Unable to Process.');
        $data['redirect']    = route('user.home');
        $data['gw_response'] = $request->all();
        $status              = DepositStatus::value('FAILED',true);

        $cardNumber      = str_replace(' ','',$request->cardNumber);
        $exp             = str_replace(' ','',$request->cardExpiry);

        $gateway         = $log->method->parameters;

        $merchantAuthentication = new MerchantAuthenticationType();
        $merchantAuthentication->setName($gateway->login_id);
        $merchantAuthentication->setTransactionKey($gateway->current_transaction_key);

        $creditCard = new CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($exp);

        $paymentOne = new PaymentType();
        $paymentOne->setCreditCard($creditCard);

  
        $transactionRequestType = new TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount(round($log->final_amount,2));
        $transactionRequestType->setPayment($paymentOne);

        $transactionRequest = new CreateTransactionRequest();
        $transactionRequest->setMerchantAuthentication($merchantAuthentication);
        $transactionRequest->setRefId($log->trx_code);
        $transactionRequest->setTransactionRequest($transactionRequestType);

        $controller         = new CreateTransactionController($transactionRequest);
        $response           = $controller->executeWithApiResponse(ANetEnvironment::PRODUCTION); 

        
        $response = @$response->getTransactionResponse();

        $data['gw_response'] = $response ;
        if (($response != null) && ($response->getResponseCode() == "1")) {
            $data['status']   = 'success';
            $data['message']  = trans('default.deposit_success');
            $status           = DepositStatus::value('PAID',true);
        }

        $data['redirect'] = UserService::updateDepositLog($log,$status,$data);
        return $data;

       

    }




   
}
