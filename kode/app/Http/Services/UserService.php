<?php
namespace App\Http\Services;

use App\Enums\BalanceTransferType;
use App\Enums\DepositStatus;
use App\Enums\FileKey;
use App\Enums\PlanDuration;
use App\Enums\StatusEnum;
use App\Enums\SubscriptionStatus;
use App\Enums\WithdrawStatus;
use App\Http\Requests\Admin\BalanceUpdateRequest;
use App\Http\Utility\SendNotification;
use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;
use App\Models\Admin\PaymentMethod;
use App\Models\Admin\Withdraw;
use App\Models\AffiliateLog;
use App\Models\User;
use App\Models\Core\File;
use App\Models\Country;
use App\Models\CreditLog;
use App\Models\KycLog;
use App\Models\Package;
use App\Models\PaymentLog;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\Subscription;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\Fileable;
use App\Traits\ModelAction;
use App\Traits\Notifyable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;


class UserService
{


    use Fileable,ModelAction,Notifyable;
    protected PaymentService $paymentService;
    public function __construct(){
        $this->paymentService = new PaymentService();

    }



    /**
     * Get user list view informations
     *
     * @return array
     */
    public function getList(): array{

        return [

                'breadcrumbs'  =>  ['Home'=>'admin.home','Users'=> null],
                'title'        => 'Manage Users',

                'users'        =>  User::with([
                                                'file',
                                                'createdBy',
                                                'country',
                                                "runningSubscription",
                                                "runningSubscription.package"                                       
                                            ])
                                        ->routefilter()
                                        ->search(['name','email',"phone"])
                                        ->filter(['country:name'])
                                        ->latest()
                                        ->paginate(paginateNumber())
                                        ->appends(request()->all()),

                "countries"    => get_countries(),
    
            ];
    }


   
    /**
     * Save a specific user 
     *
     * @param Request $request
     * @return User
     */
    public function save(Request $request): User{

        return  DB::transaction(function() use ($request): User | null{
 
                $user                       =  User::with('file')->firstOrNew(['id' => $request->input("id")]);
                $user->name                 =  $request->input('name');
                $user->username             =  $request->input('username');
                $user->phone                =  $request->input('phone');
                $user->email                =  $request->input('email');
                $user->address              =  $request->input('address',[]);

                if($request->input('password')){
                    $user->password             =  $request->input('password');
                }

                $user->country_id           =  $request->input('country_id');
                $user->email_verified_at    =  $request->input('email_verified')?Carbon::now() : null ;
                $user->auto_subscription    =  $request->input('auto_subscription',StatusEnum::false->status());
                $user->is_kyc_verified      =  $request->input('is_kyc_verified',StatusEnum::false->status());
                $user->save();

                if($request->hasFile('image')){

                    $oldFile = $user->file()->where('type',FileKey::AVATAR->value)->first();
                    $this->saveFile($user ,$this->storeFile(
                                                   file       : $request->file('image'), 
                                                   location   : config("settings")['file_path']['profile']['user']['path'],
                                                   removeFile : @$oldFile
                                                )
                                                ,FileKey::AVATAR->value);
                }
                return $user;
        
            });
    }




    /**
     * Get user report & statistics 
     *
     * @return array
     */
    public function getReport(): array{

        $currentYear   = date("Y");
        $currentMonth  = request()->input('month', date("m"));

        $usersByCountries    =   User::with(['country'])
                                        ->select(DB::raw("count(id) as total, country_id"))
                                        ->groupBy('country_id')
                                        ->orderBy('total')
                                        ->lazyById(paginateNumber(),'country_id')->mapWithKeys(fn(User $user) =>
                                            [$user->country->name => $user->total]
                                        )->toJson();

        $topCountries        =   Country::withCount('users')
                                        ->orderBy('users_count', 'desc')
                                        ->take(30)
                                        ->get();  
                                           
      
        $currentYearUsers    =  sortByMonth(User::selectRaw("MONTHNAME(created_at) as months,  count(*) as total")
                                                ->whereYear('created_at', '=',date("Y"))
                                                ->groupBy('months')
                                                ->pluck('total', 'months')
                                                ->toArray());


        $daysInMonth      = Carbon::createFromDate($currentYear, $currentMonth, 1)->daysInMonth;
        $days             = array_fill(1, $daysInMonth, 0);
        $currentMonthData = DB::table('users')
                                ->selectRaw("DAY(created_at) as day, count(*) as total")
                                ->whereYear('created_at', '=', $currentYear)
                                ->whereMonth('created_at', '=', $currentMonth)
                                ->groupBy('day')
                                ->pluck('total', 'day')
                                ->toArray();

        $currentMonthUsers = array_replace($days, $currentMonthData);

        return [

            'title'                    =>  'User statistics',
            'user_by_countries'        =>  $usersByCountries ,
            'top_countries'            =>  $topCountries ,
            'subscribed_users'         =>  User::whereHas('subscriptions')->count(),
            'unsubscribed_users'       =>  User::whereDoesntHave('subscriptions')->count(),
            'active_users'             =>  User::active()->count(),
            'banned_users'             =>  User::banned()->count(),
            'user_by_year'             =>  $currentYearUsers,
            'user_by_month'            =>  $currentMonthUsers,
        ];

    }






    /**
     * Get a specific user details
     *
     * @param string $uid
     * @return array
     */
    public function getUserDetails(string $uid): array{


        $user  = User::with([   
                            'file',
                            'kycLogs',
                            'posts',
                            'accounts',
                            'templates',
                            'paymentLogs',
                            'creditLogs',
                            'transactions',
                            'subscriptions',
                            'runningSubscription',
                            'runningSubscription.package',
                            'tickets',
                            'withdraws',
                            'affiliates',
                            "referral"
                        ])->where('uid',$uid)
                          ->firstOrFail();


        $graphData = new Collection();

        SocialPost::whereYear('created_at',  date('Y'))
            ->where('user_id',$user->id)
            ->selectRaw("MONTH(created_at) as month, 
                            MONTHNAME(created_at) as months,
                            count(*) as total,
                            SUM(CASE WHEN status =  '0'  THEN id END) AS pending,
                            SUM(CASE WHEN status =  '1'  THEN id END) AS success,
                            SUM(CASE WHEN status =  '2'  THEN id END) AS failed,
                            SUM(CASE WHEN status =  '3'  THEN id END) AS schedule")

            ->groupBy('month', 'months')
            ->orderBy('month')
            ->chunk(1000, function (Collection $logs) use (&$graphData) : void {
                $graphData  = $logs->map(fn(SocialPost $log): array =>
                        [$log->months =>  [
                            'total'    => $log->total ?? 0,
                            'pending'  => $log->pending ?? 0,
                            'success'  => $log->success ?? 0,
                            'failed'   => $log->failed ?? 0,
                            'schedule' => $log->schedule ?? 0
                        ]]
                );
            });

        

        return [
            'breadcrumbs'          => ['Home'=>'admin.home','Users'=> 'admin.user.list' ,'Show' => null],
            'title'                => 'Show Users',
            'user'                 => $user,
            'packages'             => Package::active()->get(),
            'withdraw_methods'     => Withdraw::active()->get(),
            'methods'              => PaymentMethod::active()->get(),
            "countries"            => get_countries(),
            "graph_data"           => sortByMonth(@$graphData->collapse()->all() ?? [],true,
                                                    [
                                                        'total'       => 0,
                                                        'pending'     => 0,
                                                        'success'     => 0,
                                                        'failed'      => 0,
                                                        'schedule'    => 0
                                                    ])
        
        ];

    }




    /**
     * Delete a specific users
     *
     * @param integer|string $uid
     * @return array
     */
    public function delete(int|string $uid): array{

        try {
            DB::transaction(function() use ($uid): void{

                $user      = User::with(
                                     [
                                        'file',"otp",'notifications','tickets','tickets.messages','tickets.file','subscriptions','transactions','paymentLogs','paymentLogs.file','withdraws','withdraws.file','templates','templateUsages','kycLogs','kycLogs.file','creditLogs','affiliates','accounts','posts','posts.file','webhookLogs'
                                     ])->where('uid',$uid)
                                       ->firstOrfail();
    
    
                #DELETE SUBSCRIPTIONS
                $user->subscriptions()->delete();
    
                #DELETE AFFLIATES
                $user->affiliates()->delete();
    
                #DELETE ACCOUNTS
                $user->accounts()->delete();
    
                #DELETE OTP
                $user->otp()->delete();
    
                #DELETE TRANSACTIONS
                $user->transactions()->delete();
    
                #DELETE NOTIFICATIONS
                $user->notifications()->delete();
    
                #DELETE CREDIT LOG
                $user->creditLogs()->delete();
    
                #DELERE TEMPLATE REPORTS
                $user->templates()->delete();
    
                #DELETE TEMPLATE REPORT
                $user->templateUsages()->delete();
    
                #DELETE WEBHOOK
                $user->webhookLogs()->delete();
    
                #DELETE SOCIAL POST WITH FILES
                $user->post?->map(fn(SocialPost $post):bool => $this->unlinkLogFile($post ,config("settings")['file_path']['post']['path']));
                $user->posts()->delete();
    
    
                #DELETE PAYMENT LOGS
                $user->paymentLogs?->map(fn(PaymentLog $paymentLog):bool => $this->unlinkLogFile($paymentLog ,config("settings")['file_path']['payment']['path']));
                $user->paymentLogs()->delete();
    
    
                #DELETE WITHDRAW LOGS
                $user->withdraws?->map(fn(Withdraw $withdraw):bool => $this->unlinkLogFile($withdraw ,config("settings")['file_path']['withdraw']['path']));
                $user->withdraws()->delete();
    
                #DELETE TICKET LOGS
                $user->tickets?->map(function(Ticket $ticket): bool{ 
                    $ticket->messages()->delete();
                    return $this->unlinkLogFile($ticket ,config("settings")['file_path']['ticket']['path']);
                });
                $user->tickets()->delete();
    
    
                #DELETE KYC LOGS
                $user->kycLogs?->map(fn(KycLog $kycLog):bool => $this->unlinkLogFile($kycLog ,config("settings")['file_path']['kyc']['path']));
                $user->kycLogs()->delete();
    
    
                #UNLINK USER IMAGE
                $this->unlink(
                    location    : config("settings")['file_path']['profile']['user']['path'],
                    file        : $user->file()->where('type',FileKey::AVATAR->value)->first()
                );
              
                $user->delete();
            });
        } catch (\Exception $ex) {
         
            return [ 'status' => false , 'message' => strip_tags($ex->getMessage()) ];
        }
                 
        return ['status' => true , 'message' => translate("Deleted Successfully") ];

    }


    /**
     * Unlink log files
     *
     * @param mixed $log
     * @param string $path
     * @return bool
     */
    public function unlinkLogFile(Model $model ,string $path): bool{

        try {
            $model->file->map(fn(File $file):bool =>  $this->unlink(
                location    : $path,
                file        : $file
            ));
    
            return true;
        } catch (\Throwable $th) {
            return false;
        }


    }



    /**
     * Transfer balance for a specific user
     *
     * @param BalanceUpdateRequest $request
     * @return array
     */
    public function transferBalance(BalanceUpdateRequest $request): array{

        $user          =   User::findOrfail($request->input('id'));
        switch ($request->input('type')) {
            case BalanceTransferType::DEPOSIT->value:
                      $method    = PaymentMethod::with(['currency'])
                                                   ->findOrfail($request->input('payment_id'));
                      $response  = Arr::get($this->createDepositLog($request ,$user ,$method),"response",[]);
                break;
            case BalanceTransferType::WITHDRAW->value:
                        $method    = Withdraw::findOrfail($request->input("method_id"));
                        $response  = $this->createWithdrawLog($request ,$user ,$method);
                break;
        }

        return $response;

    }







    /**
     * Withdraw request handle
     *
     * @param Request $request
     * @param User $user
     * @param Withdraw $method
     * @param mixed $status
     * @return array
     */
    public function createWithdrawLog(Request $request , User $user , Withdraw $method ,mixed $status = null) :array{


        $params['currency_id']     = session()->get("currency") ? session()->get("currency")->id : base_currency()->id;
        $amount                    = (float)$request->input("amount");
        $charge                    = round_amount((float)$method->fixed_charge + ($amount  * (float)$method->percent_charge / 100));
        $total                     = $amount + $charge;
        $baseAmount                = convert_to_base($total);
        $response                  = response_status("Insufficient funds in user account. Withdrawal request cannot be processed due to insufficient balance. ",'error');

        if($baseAmount  < $user->balance){

            $status              =  $status ?  $status : WithdrawStatus::value("APPROVED",true);

            $params  = [

                'currency_id'         =>  session()->get("currency") ? session()->get("currency")->id : base_currency()->id ,
                "amount"              =>  $amount,
                "base_amount"         =>  convert_to_base($amount),
                "charge"              =>  $charge,
                "final_amount"        =>  $amount + $charge,
                "base_final_amount"   =>  convert_to_base($amount + $charge),
                "status"              =>  $status,
                "notes"               =>  $request->input("remarks"),
                "trx_code"            =>  trx_number()
            ];


            DB::transaction(function() use ($request,$params,$user,$method ,$baseAmount ) {
                
                $log = $this->paymentService->withdrawLog($user , $method ,$params);

                    if(request()->routeIs('user.*')){
                        $this->saveCustomInfo($request ,$log, $method->parameters ,"custom_data","withdraw");
                    }

                    if($log->status == WithdrawStatus::value("APPROVED")){

                        $params ['trx_type']   = Transaction::$MINUS;
                        $params ['trx_code']   = $log->trx_code;
                        $params ['remarks']    = 'withdraw';
                        $params ['details']    = $params['amount']." ".session("currency")?->code.' Withdraw Via ' .$method->name;
                    
                        $transaction           =  PaymentService::makeTransaction($user,$params);

                        $user->balance -= $baseAmount;
                        $user->save();

                    }
                    
                    $code = [
                        "name"      => $user->name,
                        "trx_code"  => $log->trx_code,
                        "amount"    => num_format($log->amount,$log->currency),
                        "method"    => $method->name,
                        "time"      => Carbon::now(),
                    ];
    
                    $route          =  route("admin.withdraw.report.list");
                    $userRoute      =  route("user.withdraw.report.list");
                    $admin          = get_superadmin();

                    $notifications = [
                        'database_notifications' => [
                            'action' => [SendNotification::class, 'database_notifications'],
                            'params' => [
                                [ $admin, 'WITHDRAWAL_REQUEST_SUBMIT', $code, $route ],
                                ($log->status == WithdrawStatus::value("APPROVED")) ? [$user, 'WITHDRAWAL_REQUEST_ACCEPTED', $code ,$userRoute] : null,
                            ],
                        ],
                     
                        'email_notifications' => [
                            'action' => [SendMailJob::class, 'dispatch'],
                            'params' => [
                                [$admin, 'WITHDRAWAL_REQUEST_SUBMIT', $code],
                                [$user, 'WITHDRAWAL_REQUEST_RECEIVED', $code],
                                ($log->status == WithdrawStatus::value("APPROVED")) ? [$user, 'WITHDRAWAL_REQUEST_ACCEPTED', $code] : null,
                            ],
                        ],
                        'sms_notifications' => [
                            'action' => [SendSmsJob::class, 'dispatch'],
                            'params' => [
                                [$admin, 'WITHDRAWAL_REQUEST_SUBMIT', $code],
                                [$user, 'WITHDRAWAL_REQUEST_RECEIVED', $code],
                                ($log->status == WithdrawStatus::value("APPROVED")) ? [$user, 'WITHDRAWAL_REQUEST_ACCEPTED', $code] : null,
                            ],
                        ],
                    ];
                    
                    $this->notify($notifications);


            
            });


            $message = translate("Your withdrawal request has been processed successfully");
            if(request()->routeIs('user.*')){
                $message = translate("Your withdrawal Request is submitted !! Please wait for confirmation");
            }
            return $response                  = response_status($message);

        }

        return $response;


    }


    /**
     * create  a new subscription for user
     *
     * @param User $user
     * @param Package $package
     * @return array
     */
    public function createSubscription(User $user , Package $package ,string | null $remarks =  null) :array {

        try {

            $price           = round($package->discount_price) > 0 ?  $package->discount_price :  $package->price;
        
            $oldSubscription = $user->runningSubscription;

            if($user->balance <   $price){
    
                return [
                    "status"    => false,
                    "message"   => translate("User doesnot have enough balance to purchase this package !!")
                ];
            }
    
            if($package->is_free == StatusEnum::true->status() && Subscription::where("user_id",$user->id)->where('package_id',$package->id)->count() > 0){
                
                return [
                    "status"    => false,
                    "message"   => translate("User cannot be  subscribed in  free package twice !!")
                ];
                
            }
    
            $params =  [
                "user_id"         =>  $user->id,
                "package_id"      =>  $package->id,
                "old_package_id"  =>  $oldSubscription?->package_id,
                "payment_amount"  =>  $price,
                "payment_status"  =>  DepositStatus::value('PAID',true) ,
                "status"          =>  SubscriptionStatus::value('RUNNING',true),
            ];
    
    
            $expireDate = null;
            if($package->duration != PlanDuration::value('UNLIMITED',true)){
                $expireDate = date('Y-m-d', strtotime(date('Y-m-d') . ($package->duration == PlanDuration::value('YEARLY', true) ? ' + 1 years' : ' + 1 months')));

            }


            $params['expired_at']                 = $expireDate ;
    
            $params['remarks']                    = $remarks ? $remarks :  $package->title . " Plan Purchased" ;
    
            $wordLimit                            = (int) @$package->ai_configuration->word_limit;
            $postLimit                            = (int) @$package->social_access->post;
            $profileLimit                         = (int) @$package->social_access->profile;
    
            $params['word_balance']               = $wordLimit;
            $params['remaining_word_balance']     = $wordLimit;
            $params['total_profile']              = $profileLimit ;
            $params['post_balance']               = $postLimit;
            $params['remaining_post_balance']     = $postLimit;
           
    
            if(site_settings('subscription_carry_forword') == StatusEnum::true->status() && $oldSubscription && $oldSubscription->package_id == $package->id){
    
                if($wordLimit   !=  PlanDuration::value('UNLIMITED')){
    
                    $carriedWords                          = (int)$oldSubscription->remaining_word_balance;
                    $params['word_balance']               += $carriedWords;
                    $params['remaining_word_balance']     += $carriedWords;
                    $params['carried_word_balance']        = $carriedWords;
                }
    
                $carriedProfile                            = (int)$oldSubscription->total_profile;
                $params['total_profile']                  += $carriedProfile;
                $params['carried_profile']                 = $carriedProfile;
    
                if($wordLimit   != PlanDuration::value('UNLIMITED')){
    
                    $carriedPost                           = (int)$oldSubscription->remaining_post_balance; 
                    $params['post_balance']               += $carriedPost ;
                    $params['remaining_post_balance']     += $carriedPost ;
                    $params['carried_post_balance']        = $carriedPost ;
    
                }
    
            }
    

            DB::transaction(function() use ($params,$oldSubscription,$user,$package) {
    


                $params['trx_code']               = trx_number() ; 
    
              
                $this->invalidatePreviousSubscriptions($user);
         
    
                $subscription   = Subscription::create($params);
    
                $user->balance -= $subscription->payment_amount;
                $user->save();
                
    
                $package->total_subscription_income +=$subscription->payment_amount;
                $package->save();
    
                $transactionParams = [

                    "currency_id"    => base_currency()->id,
                    "amount"         => $subscription->payment_amount,
                    "final_amount"   => $subscription->payment_amount,
                    "trx_type"       => Transaction::$MINUS,
                    "remarks"        => "subscription",
                    "details"        => $package->title . " Plan Purchased",
                    "trx_code"       => $subscription->trx_code
                ];
    
                $transaction         =  PaymentService::makeTransaction($user,$transactionParams);
    
          
                $balance             = PlanDuration::value('UNLIMITED');
                $socialBalance       = PlanDuration::value('UNLIMITED');
    
                if((int)$subscription->word_balance != PlanDuration::value('UNLIMITED')){
                    $balance         = (int) $subscription->word_balance;
                }
                if((int)$subscription->word_balance != PlanDuration::value('UNLIMITED')){
                    $socialBalance   = (int) $subscription->post_balance;
                }
    


                $crditLogs =  [
    
                    "word_credit"        => [
                        'balance'        =>  $balance,
                        'post_balance'   => (int) @$oldSubscription->remaining_word_balance
                    ],
    
                    "profile_credit"     => [
                        'balance'        => (int) $subscription->total_profile,
                        'post_balance'   => (int) @$oldSubscription->total_profile
                    ],
    
                    "social_post_credit" => [
                        'balance'        => $socialBalance,
                        'post_balance'   => (int) @$oldSubscription->remaining_post_balance
                    ],
    
                ];
    
                foreach( $crditLogs as $key => $log){

                    $log['user_id']          = $user->id;
                    $log['subscription_id']  = $subscription->id;
                    $log['trx_code']         = trx_number();
                    $log['remarks']           = k2t($key);
                    $log['details']          = $transaction->details;
                    $log['type']             = Transaction::$PLUS;

                    CreditLog::create($log);
                }


                $continuousCommission  = site_settings("continuous_commission");
                $signUpPackage         = Package::active()->where('id',site_settings('signup_bonus',-1))->first();

                $affiliateBonus        =  $continuousCommission == StatusEnum::true->status() || Subscription::where('user_id', $user->id)
                                                                                                    ->when($signUpPackage ,function($query) use($signUpPackage) {
                                                                                                        return $query->where('package_id','!=',@$signUpPackage->id);
                                                                                                    })->count() < 2;

                if(site_settings("affiliate_system") == StatusEnum::true->status() && $user->referral && $affiliateBonus && $subscription->package->affiliate_commission > 0  ){

                    $this->affiliateBonus($user, $subscription);
                }

                $route             =  route("admin.subscription.report.list");
                $userRoute         =  route("user.subscription.report.list");
                $admin             = get_superadmin();
                $code =  [
                    'name'         => $user->name,
                    'start_date'   => date('Y-m-d'),
                    'end_date'     => $subscription->expired_at,
                    'package_name' => $package->title,
                ];

                $notifications = [

                    'database_notifications' => [
                        'action' => [SendNotification::class, 'database_notifications'],
                        'params' => [
                            [ $admin, 'SUBSCRIPTION_CREATED', $code, $route ],
                            [ $user, 'SUBSCRIPTION_CREATED', $code, $userRoute ],
                        ],
                    ],
              
                    'email_notifications' => [
                        'action' => [SendMailJob::class, 'dispatch'],
                        'params' => [
                            [$admin,'SUBSCRIPTION_CREATED',$code],
                            [$user, 'SUBSCRIPTION_CREATED', $code],
                        ],
                    ],
                    'sms_notifications' => [
                        'action' => [SendSmsJob::class, 'dispatch'],
                        'params' => [
                            [$admin,'SUBSCRIPTION_CREATED',$code],
                            [$user, 'SUBSCRIPTION_CREATED', $code],
                        ],
                    ],
                ];

                $this->notify($notifications);
              
          
            });
    

            return [
                "status"    => true,
                "message"   => translate("New subscription created")
            ];
    
    
        } catch (\Exception $ex) {
            
            return [
                "status"    => false,
                "message"   => strip_tags($ex->getMessage())
            ];
        }

 
    }



    /**
     * Affiliate Bonus calculations
     *
     * @param User $user
     * @param Subscription $subscription
     * @return void
     */
    public function affiliateBonus(User $user , Subscription $subscription) :void {

        DB::transaction(function() use ($user,$subscription) {

            $commission  =  ((float) $subscription->package->affiliate_commission / 100 ) * (float) $subscription->payment_amount;
            $params ['commission_rate']             = $subscription->package->affiliate_commission ; 
            $params ['subscription_id']             = $subscription->id; 
            $params ['user_id']                     = $user->referral->id;
            $params ['referred_to']                 = $user->id;
            $params ['commission_amount']           = $commission;
            $params ['trx_code']                    = trx_number();
            $params ['note']                        = $user->name . " Purchased ".$subscription->package->title . " Plan";

            $log = AffiliateLog::create($params);

            $user->referral->balance += $log->commission_amount;
            $user->referral->save();

            $transactionParams = [
                "currency_id"    => base_currency()->id,
                "amount"         => $log->commission_amount,
                "final_amount"   => $log->commission_amount,
                "trx_type"       => Transaction::$PLUS,
                "remarks"        => "affiliate",
                "details"        => $log->commission_amount." ".base_currency()?->code.' Added Via Affiliate Bonus ',
                "trx_code"       => $log->trx_code
            ];

            $transaction         =  PaymentService::makeTransaction($user,$transactionParams);
        });
        
    }


    /**
     * Create deposit request
     *
     * @param Request $request
     * @param User $user
     * @param PaymentMethod $method
     * @param mixed $status
     * @return array
     */
    public function createDepositLog(Request $request , User $user ,PaymentMethod $method , mixed $status = null) :array{

        $params['currency_id']     = session()->get("currency") ? session()->get("currency")->id : base_currency()->id;
        $amount                    = (float)$request->input("amount");
        $charge                    = round_amount( (float)$method->fixed_charge + ($amount  * (float)$method->percentage_charge / 100),4);
        $total                     = $amount + $charge;

        $status                    = $status ?  $status : (string)DepositStatus::PAID->value;
        $finalBase                 = convert_to_base($total);

        $finalAmount               = round_amount($finalBase*$method->currency->exchange_rate,2);

        $params                    = [
            'currency_id'          =>  session()->get("currency") ? session()->get("currency")->id : base_currency()->id ,
            "amount"               =>  $amount,
            "base_amount"          =>  convert_to_base($amount),
            "charge"               =>  $charge,
            "final_base"           =>  $finalBase,
            "final_amount"         =>  $finalAmount,
            "base_final_amount"    =>  convert_to_base($amount + $charge),
            "status"               =>  $status,
            "notes"                =>  $request->input("remarks"),
            "trx_code"             =>  trx_number(),
            "rate"                 =>  exchange_rate($method->currency,5)
        ];


        $log = DB::transaction(function() use ($request,$params,$user,$method) {

            $log = $this->paymentService->paymentLog($user,$method ,$params);

         
            $params ['trx_type']        = Transaction::$PLUS;
            $params ['trx_code']        = $log->trx_code;
            $params ['remarks']         = 'deposit';
            $params ['final_amount']    = $log->amount + $log->charge;
            $params ['details']         = $log->amount." ".session("currency")?->code.' Deposited Via ' .$method->name;

            $code = [
                "name"            => $user->name,
                "trx_code"        => $log->trx_code,
                "amount"          => num_format($log->amount,$log->currency),
                "time"            => Carbon::now(),
                "payment_method"  => $log->method->name
            ];

            $route          =  route("admin.deposit.report.list");
            $userRoute      =  route("user.deposit.report.list");
            $admin          =  get_superadmin();


            if($log->status  == (string)DepositStatus::PAID->value){

                $params ['trx_code']       =  $log->trx_code;
                $transaction               =  PaymentService::makeTransaction($user,$params);
                $user->balance +=$log->base_amount;
                $user->save();
            }

            $notifications = [

                'database_notifications' => [
                    'action' => [SendNotification::class, 'database_notifications'],
                    'params' => [
                        [ $admin, 'NEW_DEPOSIT', $code, $route ],
                        $log->status  == (string)DepositStatus::PAID->value ?  [ $user, 'DEPOSIT_REQUEST_ACCEPTED', $code, $userRoute ] : [ $user, 'DEPOSIT_REQUEST', $code, $userRoute ],
                    ],
                ],
               
                'email_notifications' => [
                    'action' => [SendMailJob::class, 'dispatch'],
                    'params' => [
                        [$admin,'NEW_DEPOSIT',$code],
                        $log->status  == (string)DepositStatus::PAID->value  ?   [$user, 'DEPOSIT_REQUEST_ACCEPTED', $code] : [$user, 'DEPOSIT_REQUEST', $code],
                    ],
                ],
                'sms_notifications' => [
                    'action' => [SendSmsJob::class, 'dispatch'],
                    'params' => [
                        [$admin,'NEW_DEPOSIT',$code],
                        $log->status  == (string)DepositStatus::PAID->value  ? [$user, 'DEPOSIT_REQUEST_ACCEPTED', $code] : [$user, 'DEPOSIT_REQUEST', $code],
                    ],
                ],
            ];

            $this->notify($notifications);


            return $log;

        });

        $message = translate("Your deposit request has been processed successfully");
     
        $response    = response_status($message);

        return [
            "response" => $response,
            "log"      => $log->load(['user','user.country']),
        ];

        
    }


    /**
     *Save custom field
     *
     * @param Request $request
     * @param mixed $log
     * @param object $params
     * @param string $key
     * @return void
     */
    public function saveCustomInfo(Request $request , mixed $log , object $params , string $key ,string $fileLocation) :void{

          
        $collection    = collect($request);
        $customData    = [];
        if ($params != null) {

            foreach ($collection as $k => $v) {

                foreach ($params as $inKey => $inVal) {

                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {

                                try {
             
                                    $response = $this->storeFile(
                                        file        : $request->file($inKey), 
                                        location    :  config("settings")['file_path'][$fileLocation]['path'],
                                    );
                                    
                                    if(isset($response['status'])){

                                        $file = new File([

                                            'name'      => Arr::get($response, 'name', '#'),
                                            'disk'      => Arr::get($response, 'disk', 'local'),
                                            'type'      => $inKey ,
                                            'size'      => Arr::get($response, 'size', ''),
                                            'extension' => Arr::get($response, 'extension', ''),
                                        ]);
                
                                        $log->file()->save($file);
                                    }

                                    $customData[$inKey] = [

                                        'field_name' => Arr::get( $response ,'name',"#"),
                                        'type'       => $inVal->type,
                                    ];

                                } catch (\Exception $exp) {

                                }
                            }
                        } else {
                            $customData[$inKey] = $v;
                            $customData[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }

            $log->{$key} = $customData;
            $log->save();
        }
    }



    /**
     * manual  input validation rules
     *
     * @param mixed $params
     * @return array
     */
    public function paramValidationRules(mixed $params) :array {

        $rules           = [];
        $verifyImages    = [];
        if ($params != null) {
            foreach ($params as $key => $cus) {
                $rules[$key] = [$cus->validation];

                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true)));
                    array_push($verifyImages, $key);
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
            }
        }

        return $rules;

    }


    /**
     * Update deposit log
     *
     * @param PaymentLog $log
     * @param mixed $status
     * @param array $responseData
     * @return void
     */
    public static function updateDepositLog(PaymentLog $log , mixed $status ,array $responseData) :string{

        $log->status            =   $status;
        $log->gateway_response  =   $responseData;
        $log->save();
        
        $redirectRoute = 'payment.failed';

        if($log->status  == (string) DepositStatus::value("PAID",true)){
            $params  = [
                'trx_code'     => $log->trx_code,
                'currency_id'  => $log->currency_id,
                'amount'       => $log->amount,
                'charge'       => $log->charge,
                'final_amount' => $log->amount + $log->charge,
                'trx_type'     => Transaction::$PLUS,
                'remarks'      => "Deposit",
                'details'      => $log->amount." ".$log->currency?->code.' Deposited Via ' .$log->method->name
            ];

            $transaction  =  PaymentService::makeTransaction($log->user,$params);
            $log->user->balance +=$log->base_amount;
            $log->user->save();

            $redirectRoute = 'payment.success';
        }

        return route($redirectRoute,['payment_intent' => base64_encode(json_encode([
            "trx_number" => $log->trx_code,
            "type"       => $log->status  == (string)DepositStatus::value("PAID",true) ? "SUCCESS" : "FAILED",
        ]))]);

    }
    
    


    /**
     * Inactive social account
     *
     * @param Subscription $subscription
     * @param string $details
     * @return void
     */
    public function inactiveSocialAccounts(Subscription $subscription, string $details = "Subscription Expired") :void{

        SocialAccount::where('user_id',$subscription->user->id)->where('subscription_id',$subscription->id)->update([
            'status'  => StatusEnum::false->status(),
            'details' => $details,
        ]);
    }



    /**
     * Invalid subscriptions
     *
     * @param User $user
     * @return void
     */
    public function invalidatePreviousSubscriptions(User $user) :void{

        $subscriptions  = Subscription::with('user')
                            ->running()
                            ->where('user_id',$user->id)
                            ->get();

        foreach($subscriptions as $subscription){   
            
            $subscription->expired_at =  date('Y-m-d');
            $subscription->status     =  SubscriptionStatus::value('EXPIRED',true);
            $subscription->save();
            $this->inactiveSocialAccounts($subscription);
        }

    }
    

    public function deductSubscriptionCredit(Subscription $subscription , string $key , int $value = 1) :Subscription{
        $subscription->decrement($key,$value);
        return $subscription;
    }
  
}
