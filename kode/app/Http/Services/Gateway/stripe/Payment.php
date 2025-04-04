<?php

namespace App\Http\Services\Gateway\stripe;

use App\Enums\DepositStatus;
use App\Http\Services\UserService;
use App\Models\PaymentLog;
use Illuminate\Http\Request;



require_once('stripe-php/init.php');

class Payment
{
    public static function paymentData(PaymentLog $log) :string 
    {

  
            $params            = ($log->method->parameters);
           \Stripe\Stripe::setApiKey($params->secret_key);

            $siteName           = site_settings('site_name');
            $user               = $log->user;
            $address = array(
            
                'email' => @$user->email,
                'name'  =>  @$user->name,
                'address' => [
                    'line1'       => @$user->address->address,
                    'postal_code' =>  @$user->address->postal_code,
                    'city'        => @$user->address->city,
                    'state'       => @$user->address->state,
                    'country'     => @$user->country->code??'US',
                ],
            
           );

        $customer = \Stripe\Customer::create($address);

        $response = \Stripe\Checkout\Session::create([
            'customer'   => $customer->id,
            'payment_method_types' =>  ['card'],
            'line_items' => [
                [
            
                'price_data' => [
                'currency' => $log->method->currency->code,
                'unit_amount' => round($log->final_amount,2) * 100,
                    'product_data' => [
                        'name' =>  $siteName,
                ],
                ],

                'quantity' => 1,
            ]
            
            ],
            'mode' => 'payment',
            'cancel_url'   => route('ipn',["trx_code" => $log->trx_code,"type"=>'failed']),
            'success_url'  => route('ipn',["trx_code" => $log->trx_code,"type"=>'success']),

        ]);

        $send['error']   = true;
        $send['message'] = translate("Invalid Payment Request");
 
        if(isset( $response->id)){
            session()->put('payment_id',$response->id);
            $send['redirect']     = true;
            $send['redirect_url'] = $response->url;      
        }
      
        return json_encode($send);
    }



    public static function ipn(Request $request , PaymentLog $log , string  $type = null) :array
    {

        $data['status']      = 'error';
        $data['message']     = translate('Invalid amount.');
        $data['gw_response'] = $request->all();
        $status              = DepositStatus::value('FAILED',true);
        $params            = ($log->method->parameters);

        if ($type == 'success') {
            $paymentID =       $request->input('payment_id',session()->get('payment_id'))  ;
            $stripe = new \Stripe\StripeClient($params->secret_key);
            $session =  $stripe->checkout->sessions->retrieve(
                              $paymentID,
                            []);
            
            if($session->object == 'checkout.session' &&  $session->payment_status == 'paid'){
                    $data['status']   = 'success';
                    $data['message']  = trans('default.deposit_success');
                    $status           = DepositStatus::value('PAID',true);
            }

            session()->forget('payment_id');

        }

        $data['redirect'] = UserService::updateDepositLog($log,$status,$data);
        return $data;

    }
}
