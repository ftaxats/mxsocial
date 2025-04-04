<?php

namespace App\Traits;

use App\Enums\StatusEnum;
use App\Http\Services\UserService;
use App\Models\CreditLog;
use App\Models\MediaPlatform;
use App\Models\SocialAccount;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;


trait AccountManager
{


    protected $userService;

    public function __construct(){

        $this->userService = new UserService();
    }


    /**
     * Save social account
     *
     * @param string $guard
     * @param MediaPlatform $platform
     * @param array $accountInfo
     * @param string $account_type
     * @return array
     */
    protected function saveAccount(string $guard , MediaPlatform $platform , array $accountInfo, string  $account_type , string $is_official , int | string  $dbId = null ) :array{

        $socialAccount = DB::transaction(function() use ($guard,$platform,$accountInfo,$account_type ,$is_official ,$dbId ) {

                        $user      = auth_user($guard);
                        $accountId = Arr::get($accountInfo,"account_id",null);

                        $findBy = ['account_id' => $accountId ,'platform_id' => $platform->id ];

             
                        switch ($guard) {
                            case 'web':
                                $findBy ['user_id'] = $user->id;
                                break;
                            case 'admin':
                                $findBy ['admin_id'] = $user->id;
                                break;
                        }

                

                        $account = $dbId ? SocialAccount::find($dbId) : SocialAccount::firstOrNew($findBy);


                        $account->platform_id                 = $platform->id;
                        $account->name                        = Arr::get($accountInfo,'name');
                        $account->account_information         = $accountInfo;
                        $account->status                      = StatusEnum::true->status();
                        $account->is_connected                = StatusEnum::true->status();
                        $account->account_type                = $account_type;
                        $account->is_official                 = $is_official;

                        $account->token                                  = Arr::get($accountInfo,"token",null);
                        $account->access_token_expire_at                 = Arr::get($accountInfo,"access_token_expire_at",null);
                        $account->refresh_token                           = Arr::get($accountInfo,"refresh_token",null);
                        $account->refresh_token_expire_at                 = Arr::get($accountInfo,"refresh_token_expire_at",null);




                        switch ($guard) {
                            case 'web':
                                $account->subscription_id = $user->runningSubscription?->id;
                                $account->user_id         = $user->id;
                                break;
                            case 'admin':
                                $account->admin_id        = $user->id;
                                break;
                        }
                        
                        $account->save();

                        if($account->user_id && !$dbId){

                            $this->generateCreditLog(
                                user        : $user,
                                trxType     : Transaction::$MINUS,
                                balance     : 1,
                                postBalance : (int)$user->runningSubscription->total_profile,
                                details     :  'A new '. $platform->name .' Account Created',
                                remark      : t2k("profile_credit"),
                            );

                            $user->runningSubscription->decrement('total_profile',1);

                        }

                        return $account;

                    });

        return [
            'status'  => true,
            'account' => $socialAccount
        ];




    }



    /**
     * Generate credit log
     *
     * @param User $user
     * @param string $trxType
     * @param integer $balance
     * @param integer $postBalance
     * @param string $details
     * @param string $remark
     * @return CreditLog
     */
    public function generateCreditLog(User $user, string  $trxType , int $balance = 1 , int $postBalance ,string $details ,string $remark) : CreditLog {

        $creditLog                   = new CreditLog();
        $creditLog->user_id          = $user->id;
        $creditLog->subscription_id  = $user->runningSubscription->id;
        $creditLog->trx_code         = trx_number();
        $creditLog->type             = $trxType;
        $creditLog->balance          = $balance;
        $creditLog->post_balance     = $postBalance;
        $creditLog->details          = $details;
        $creditLog->remarks          = $remark;
        $creditLog->save();
        return $creditLog;

    }


    public function disConnectAccount(SocialAccount $account) :void{
        
        $account->is_connected = StatusEnum::false->status();
        $account->update();

    }

}