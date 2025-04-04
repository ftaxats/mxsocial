<?php
namespace App\Http\Services;

use App\Enums\DepositStatus;
use App\Enums\PlanDuration;
use App\Enums\StatusEnum;
use App\Enums\WithdrawStatus;
use App\Http\Utility\SendNotification;
use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;
use App\Models\Admin;
use App\Models\Core\File;

use Illuminate\Http\Request;
use DOMDocument;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use App\Models\Admin\PaymentMethod;
use App\Models\Admin\Withdraw;
use App\Models\Package;
use App\Models\PaymentLog;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawLog;
use App\Rules\General\FileExtentionCheckRule;
use Twilio\Rest\Events\V1\SubscriptionOptions;
use Illuminate\Support\Facades\DB;
use App\Traits\Notifyable;
class PaymentService
{



    use Notifyable;




    /**
     * create a new deposit Log
     *
     * @param User $user
     * @param PaymentMethod $method
     * @param mixed $charge
     * @param mixed $finalAmount
     * @return PaymentLog
     */
    public function paymentLog(User $user, PaymentMethod $method ,array $params): PaymentLog{




        $log                       = PaymentLog::firstOrNew(
                                        [
                                            'method_id'   => $method->id,
                                            'user_id'     => $user->id,
                                            'status'      => (string) DepositStatus::INITIATE->value,
                                        ]);

        $log->currency_id          = Arr::get($params,"currency_id",null);
        $log->amount               = Arr::get($params,"amount",0.00);
        $log->base_amount          = Arr::get($params,"base_amount",0.00);
        $log->charge               = Arr::get($params,"charge",0.00);
        $log->final_amount         = Arr::get($params,"final_amount",0.00);
        $log->base_final_amount    = Arr::get($params,"base_final_amount",0.00);
        $log->rate                 = Arr::get($params,"rate",0.00);
        $log->custom_data          = Arr::get($params,"custom_data",[]);
        $log->trx_code             = Arr::get($params,"trx_code",trx_number());
        $log->status               = Arr::get($params,"status",null);
        $log->feedback             = Arr::get($params,"notes",null);
        $log->created_at           = Carbon::now();
        $log->save();


        return $log;
    }



    /**
     * create a new withdraw Log
     *
     */
    public function withdrawLog(User $user,Withdraw $method , array $params): WithdrawLog{


        $log                       = new WithdrawLog();
        $log->method_id            = $method->id;
        $log->user_id              = $user->id;
        $log->currency_id          = Arr::get($params,"currency_id",null);
        $log->amount               = Arr::get($params,"amount",0.00);
        $log->base_amount          = Arr::get($params,"base_amount",0.00);
        $log->charge               = Arr::get($params,"charge",0.00);
        $log->final_amount         = Arr::get($params,"final_amount",0.00);
        $log->base_final_amount    = Arr::get($params,"base_final_amount",0.00);
        $log->custom_data          = Arr::get($params,"custom_data",[]);
        $log->status               = Arr::get($params,"status",null);
        $log->notes                = Arr::get($params,"notes",null);
        $log->trx_code             = Arr::get($params,"trx_code",trx_number());
        $log->save();
        return $log;
  
    }


     /**
     * create a new transaction
     *
     * @param User $user
     * @param array $params
     * @return Transaction
     */
    public static function makeTransaction(User $user,array $params): Transaction{

        $transaction                     = new Transaction();
        $transaction->user_id            = $user->id;
        $transaction->post_balance       = $user->balance;
        $transaction->currency_id        = Arr::get($params,"currency_id" ,null);
        $transaction->amount             = Arr::get($params,"amount" ,0);
        $transaction->charge             = Arr::get($params,"charge" ,0);
        $transaction->final_amount       = Arr::get($params,"final_amount" ,0);
        $transaction->trx_code           = Arr::get($params,"trx_code" ,trx_number());
        $transaction->trx_type           = Arr::get($params,"trx_type" ,null);
        $transaction->remarks            = Arr::get($params,"remarks" ,null);
        $transaction->details            = Arr::get($params,"details" ,null);
        $transaction->save();
        return $transaction;
    }






   /**
    * Handle payment / deposit request
    *
    * @param PaymentLog $log
    * @param array $request
    * @return array
    */
    public function handleDepositRequest(PaymentLog $log, array $request): array{

        $status    = true;
        $message   = translate("Your deposit status has been successfully updated");

        try {

            DB::transaction(function() use($log ,$request){
          
                $log->update([
                    'status'   => Arr::get($request , 'status' ,DepositStatus::value('PENDING')),
                    'feedback' => Arr::get($request , 'feedback' ,translate("Failed to update")),
                ]);

                if($log->status  == DepositStatus::value("PAID",true)){

                    $params ['trx_type']        = Transaction::$PLUS;
                    $params ['currency_id']     = $log->currency->id;
                    $params ['trx_code']        = $log->trx_code;
                    $params ['remarks']         = 'deposit';
                    $params ['amount']          = $log->amount;
                    $params ['charge']          = $log->charge;
                    $params ['final_amount']    = $log->amount + $log->charge;
                    $params ['details']         = $log->amount." ".$log->currency->code.' Deposited Via ' .$log->method->name;
                    $transaction                = PaymentService::makeTransaction($log->user,$params);
                    $log->user->balance +=$log->base_amount;
                    $log->user->save();

                }


                $code = [
                    "name"            => $log->user->name,
                    "trx_code"        => $log->trx_code,
                    "amount"          => num_format($log->amount,$log->currency),
                    "time"            => Carbon::now(),
                    "payment_method"  => $log->method->name,
                    "reason"          => Arr::get($request , 'feedback' ,translate("Failed to update"))
                ];
                
    
                $route      =  route("user.deposit.report.list");

                $notifications = [
                    'database_notifications' => [
                        'action' => [SendNotification::class, 'database_notifications'],
                        'params' => [
    
                            $log->status  == DepositStatus::value("PAID",true) ?  [ $log->user, 'DEPOSIT_REQUEST_ACCEPTED', $code, $route ] : [ $log->user, 'DEPOSIT_REQUEST_REJECTED', $code, $route ],
                        ],
                    ],
                  
                    'email_notifications' => [
                        'action' => [SendMailJob::class, 'dispatch'],
                        'params' => [
                          
                            $log->status  == DepositStatus::value("PAID",true) ?   [$log->user, 'DEPOSIT_REQUEST_ACCEPTED', $code] : [$log->user, 'DEPOSIT_REQUEST_REJECTED', $code],
                        ],
                    ],
                    'sms_notifications' => [
                        'action' => [SendSmsJob::class, 'dispatch'],
                        'params' => [
    
                            $log->status  == DepositStatus::value("PAID",true) ? [$log->user, 'DEPOSIT_REQUEST_ACCEPTED', $code] : [$log->user, 'DEPOSIT_REQUEST_REJECTED', $code],
                        ],
                    ],
                ];
    
                $this->notify($notifications);

            });
    
        } catch (\Exception $ex) {

            $status     = false;
            $message    = strip_tags($ex->getMessage());
        }
       
        return [
            "status"   =>  $status ,
            "message"  =>  $message ,
        ];
    }






    /**
    * Handle payment / deposit request
    *
    * @param WithdrawLog $log
    * @param array $request
    * @return array
    */
    public function handleWithdrawRequest(WithdrawLog $log, array $request): array{

        $status    = true;
        $message   = translate("Your withdraw status has been successfully updated");

        try {

            DB::transaction(function() use($log ,$request){
          
                $log->update([
                    'status'   => Arr::get($request , 'status' ,WithdrawStatus::value('PENDING')),
                    'notes'    => Arr::get($request , 'feedback' ,translate("Failed to update")),
                ]);

                if($log->status  == WithdrawStatus::value("APPROVED",true)){

                    $params ['trx_type']        = Transaction::$MINUS;
                    $params ['currency_id']     = $log->currency->id;
                    $params ['trx_code']        = $log->trx_code;
                    $params ['remarks']         = 'withdraw';
                    $params ['amount']          = $log->amount;
                    $params ['charge']          = $log->charge;
                    $params ['final_amount']    = $log->amount + $log->charge;
                    $params ['details']         = $log->amount." ".$log->currency->code.' Withdraw Via ' .$log->method->name;
                    $transaction                = PaymentService::makeTransaction($log->user,$params);
                    $log->user->balance -=$log->base_final_amount;
                    $log->user->save();

                }


                $code = [
                    "name"            => $log->user->name,
                    "trx_code"        => $log->trx_code,
                    "amount"          => num_format($log->amount,$log->currency),
                    "time"            => Carbon::now(),
                    "method"          => $log->method->name,
                    "reason"          => Arr::get($request , 'feedback' ,translate("Unknown Error"))
                ];

                $route      =  route("user.withdraw.report.list");
                $notifications = [
                    'database_notifications' => [
                        'action' => [SendNotification::class, 'database_notifications'],
                        'params' => [
    
                            $log->status  == WithdrawStatus::value("APPROVED",true) ?  [ $log->user, 'WITHDRAWAL_REQUEST_ACCEPTED', $code, $route ] : [ $log->user, 'WITHDRAWAL_REQUEST_REJECTED', $code, $route ],
                        ],
                    ],
                  
                    'email_notifications' => [
                        'action' => [SendMailJob::class, 'dispatch'],
                        'params' => [
                          
                            $log->status  == WithdrawStatus::value("APPROVED",true) ?   [$log->user, 'WITHDRAWAL_REQUEST_ACCEPTED', $code] : [$log->user, 'WITHDRAWAL_REQUEST_REJECTED', $code],
                        ],
                    ],
                    'sms_notifications' => [
                        'action' => [SendSmsJob::class, 'dispatch'],
                        'params' => [
    
                            $log->status  == WithdrawStatus::value("APPROVED",true) ? [$log->user, 'WITHDRAWAL_REQUEST_ACCEPTED', $code] : [$log->user, 'WITHDRAWAL_REQUEST_REJECTED', $code],
                        ],
                    ],
                ];
    
                $this->notify($notifications);

            });
    
        } catch (\Exception $ex) {

            $status     = false;
            $message    = strip_tags($ex->getMessage());
        }
       
        return [
            "status"   =>  $status ,
            "message"  =>  $message ,
        ];
    }
 
    




}
