<?php

namespace App\Http\Services\Gateway\nmi;

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
       
        $gateway                   = ($log->method->parameters);
        $xmlRequest                = new \DOMDocument('1.0', 'UTF-8');
        $xmlRequest->formatOutput  = true;
        $xmlSale                   = $xmlRequest->createElement('sale');


        self::appendXmlNode($xmlRequest, $xmlSale, 'api-key', $gateway->api_key);
        self::appendXmlNode($xmlRequest, $xmlSale, 'redirect-url', route('ipn',[$log->trx_code]));
        self::appendXmlNode($xmlRequest, $xmlSale, 'amount', round($log->final_amount));

        self::appendXmlNode($xmlRequest, $xmlSale, 'currency', $log->method->currency->code);

        self::appendXmlNode($xmlRequest, $xmlSale, 'order-id', $log->trx_code);
        $xmlRequest->appendChild($xmlSale);
        


        $data = CurlService::curlPostContent('https://secure.nmi.com/api/v2/three-step',$xmlRequest->saveXML(),["Content-type: text/xml"]);

        

        $gwResponse = new \SimpleXMLElement($data);

        if ((string)$gwResponse->result == 1) {
            $formURL = $gwResponse->{'form-url'};
        } else {
            $send['error']   = true;
            $send['message'] = 'Something went wrong';
            return json_encode($send);
        }
        
        $formURL          = (array)$formURL;
        $formURL          = $formURL[0];
        $send['url']      = $formURL;
        $send['view']     = 'user.payment.nmi';
        $send['method']   = 'POST';

        return json_encode($send);


    }



    public static function ipn(Request $request , PaymentLog $log ) :array {

        $data['status']      = 'error';
        $data['message']     = translate('Invalid Credit Card');
        $data['redirect']    = route('user.home');
        $data['gw_response'] = $request->all();
        $status              = DepositStatus::value('FAILED',true);


        $tokenId             = $request->input('token-id');

        $gateway             = ($log->method->parameters);


        $xmlRequest = new \DOMDocument('1.0', 'UTF-8');
        $xmlRequest->formatOutput = true;
        $xmlCompleteTransaction = $xmlRequest->createElement('complete-action');
        self::appendXmlNode($xmlRequest, $xmlCompleteTransaction, 'api-key', $gateway->api_key);
        self::appendXmlNode($xmlRequest, $xmlCompleteTransaction, 'token-id', $tokenId);
        $xmlRequest->appendChild($xmlCompleteTransaction);
        $response = CurlService::curlPostContent('https://secure.nmi.com/api/v2/three-step',$xmlRequest->saveXML(),["Content-type: text/xml"]);
        $gwResponse = @new \SimpleXMLElement((string)$response);



        $data['gw_response'] =        $gwResponse;
        if (@$gwResponse->result == 1) {

            $data['status']   = 'success';
            $data['message']  = trans('default.deposit_success');
            $status           = DepositStatus::value('PAID',true);
          
        }

        $data['redirect'] = UserService::updateDepositLog($log,$status,$data);

        return $data;


    }




    
    public static function appendXmlNode($domDocument, $parentNode, $name, $value) :void
    {
        $childNode      = $domDocument->createElement($name);
        $childNodeValue = $domDocument->createTextNode($value);
        $childNode->appendChild($childNodeValue);
        $parentNode->appendChild($childNode);
    }
}
