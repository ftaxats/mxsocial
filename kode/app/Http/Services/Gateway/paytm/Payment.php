<?php

namespace App\Http\Services\Gateway\paytm;

use App\Enums\DepositStatus;
use App\Http\Services\CurlService;
use App\Http\Services\UserService;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Services\Gateway\paytm\Paytm;

class Payment
{
    public static function paymentData(PaymentLog $log) :string
    {

        $siteName                     = site_settings('site_name');
        $gateway                      = ($log->method->parameters);

        $gateway_currency             = $log->method->currency->code;
       

        $val['MID']                   = trim($gateway->mid);
        $val['WEBSITE']               = trim($gateway->website);
        $val['CHANNEL_ID']            = trim($gateway->channel_id);
        $val['INDUSTRY_TYPE_ID']      = trim($gateway->industry_type_id);

        try {
            $checkSumHash = (new PayTM())->getChecksumFromArray($val, $gateway->merchant_key);

        } catch (\Exception $e) {

            $send['error']    = true;
            $send['message']  = $e->getMessage();
            return json_encode($send);
        }

        $val['ORDER_ID']     = $log->trx_code;
        $val['TXN_AMOUNT']   = round($log->final_amount,2);
        $val['CUST_ID']      = $log->user_id;
        $val['CALLBACK_URL'] = route('ipn',$log->trx_code);
        $val['CHECKSUMHASH'] = $checkSumHash;

        $send['val']    = $val;
        $send['view']   = 'user.payment.redirect';
        $send['method'] = 'post';
        $send['url']    = $gateway->transaction_url . "?order=". $log->trx_code;


        return json_encode($send);
    }



    public static function ipn(Request $request, PaymentLog $log) :array {

        $data['status']      = 'error';
        $data['message']     = translate('Invalid amount.');
        $data['redirect']    = route('user.home');
        $data['gw_response'] = $request->all();
        $status              = DepositStatus::value('FAILED',true);

        $gateway                      = ($log->method->parameters);
        $ptm = new PayTM();

        if ($ptm->verifychecksum_e($_POST, $gateway->merchant_key, $_POST['CHECKSUMHASH']) === "TRUE") {

            if ($_POST['RESPCODE'] == "01") {
                $requestParamList  = array("MID" => $gateway->MID, "ORDERID" => $_POST['ORDERID']);
                $StatusCheckSum    = $ptm->getChecksumFromArray($requestParamList, $gateway->merchant_key);
                $requestParamList['CHECKSUMHASH'] = $StatusCheckSum;
                $responseParamList = $ptm->callNewAPI($gateway->transaction_status_url, $requestParamList);
                if ($responseParamList['STATUS'] == 'TXN_SUCCESS' && $responseParamList['TXNAMOUNT'] == $_POST['TXNAMOUNT'] ) {
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
