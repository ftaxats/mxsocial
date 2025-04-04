<?php

namespace App\Http\Controllers\User;

use App\Enums\FileKey;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Core\File;
use App\Models\CreditLog;
use App\Models\MediaPlatform;
use App\Models\Notification;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;
use App\Traits\Fileable;
use App\Traits\ModelAction;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class HomeController extends Controller
{



    protected $user ,$subscription,$accessPlatforms,$webhookAccess;

    use Fileable , ModelAction;
    public function __construct(){

        $this->middleware(function ($request, $next) {
            $this->user = auth_user('web');
            $this->subscription           = $this->user->runningSubscription;
            $this->accessPlatforms        = (array) ($this->subscription ? @$this->subscription->package->social_access->platform_access : []);
            $this->webhookAccess          = @optional($this->subscription->package->social_access)
                                                 ->webhook_access;

            return $next($request);
        });
    }


    /**
     * User Dashboard
     *
     * @param Request $request
     * @return View
     */
    public function home(Request $request): View
    {

        return view('user.home',[
            'meta_data' => $this->metaData(["title" => trans('default.user_dashboard')]),
            'data'      => $this->dashboardCounter()
        ]);
    }


    /**
     * counter dashboard data
     */

     public function dashboardCounter() :array{

        $data['account_report']            = [

            "total_account"         => SocialAccount::where('user_id', $this->user->id)->count(),
            "active_account"        => SocialAccount::where('user_id', $this->user->id)->active()->count(),
            "inactive_account"      => SocialAccount::where('user_id', $this->user->id)->inactive()->count(),
            "accounts_by_platform"  => MediaPlatform::withCount(['accounts' => function($q){
                                            $q->where('user_id', $this->user->id);
                                        }])
                                        ->integrated()
                                        ->get()
        ];


        $data['latest_post']  = SocialPost::with(['file','account','account.platform'])
                                            ->where('user_id', $this->user->id)
                                            ->latest()
                                            ->take(10)
                                            ->get();



        $data['latest_activities']           =  CreditLog::with(['user'])
                                                            ->where('user_id',$this->user->id)
                                                            ->search(['remark','trx_code'])
                                                            ->filter(['type'])
                                                            ->date()
                                                            ->latest()
                                                            ->take(10)
                                                            ->get();


        $data['latest_transactiions']           =  Transaction::with(['user','admin','currency'])
                                                        ->search(['remarks','trx_code'])
                                                        ->filter(["user:username",'trx_type'])
                                                        ->where('user_id', $this->user->id)
                                                        ->date()
                                                        ->latest()
                                                        ->take(5)
                                                        ->get();

        $data['total_post']               = SocialPost::where('user_id', $this->user->id)
                                                       ->date()
                                                       ->count();

        $data['pending_post']             = SocialPost::where('user_id', $this->user->id)
                                                       ->pending()
                                                       ->date()
                                                       ->count();
        $data['schedule_post']            = SocialPost::where('user_id', $this->user->id)
                                                       ->schedule()
                                                       ->date()
                                                       ->count();
        $data['success_post']             = SocialPost::where('user_id', $this->user->id)
                                                       ->success()
                                                       ->date()
                                                       ->count();
        $data['failed_post']              = SocialPost::where('user_id', $this->user->id)->failed()->date()->count();

        $data['affiliate_earnings']       =  $this->user->affiliates->sum("commission_amount");




        $data['monthly_post_graph']          = sortByMonth(SocialPost::filter(["platform:slug"])
                                                            ->date()
                                                            ->selectRaw("MONTHNAME(created_at) as months, COUNT(*) as total")
                                                            ->whereYear('created_at', '=',date("Y"))
                                                            ->groupBy('months')
                                                            ->where('user_id', $this->user->id)
                                                            ->pluck('total', 'months')
                                                            ->toArray(),true);

        $data['monthly_pending_post']      = sortByMonth(SocialPost::filter(["platform:slug"])
                                                            ->date()
                                                            ->selectRaw("MONTHNAME(created_at) as months, COUNT(*) as total")
                                                            ->whereYear('created_at', '=',date("Y"))
                                                            ->pending()
                                                            ->where('user_id', $this->user->id)
                                                            ->groupBy('months')
                                                            ->pluck('total', 'months')
                                                            ->toArray(),true);

        $data['monthly_schedule_post']     = sortByMonth(SocialPost::filter(["platform:slug"])
                                                            ->date()
                                                            ->selectRaw("MONTHNAME(created_at) as months, COUNT(*) as total")
                                                            ->whereYear('created_at', '=',date("Y"))
                                                            ->schedule()
                                                            ->where('user_id', $this->user->id)
                                                            ->groupBy('months')
                                                            ->pluck('total', 'months')
                                                            ->toArray(),true);

        $data['monthly_success_post']      = sortByMonth(SocialPost::filter(["platform:slug"])
                                                            ->date()
                                                            ->selectRaw("MONTHNAME(created_at) as months, COUNT(*) as total")
                                                            ->whereYear('created_at', '=',date("Y"))
                                                            ->success()
                                                            ->where('user_id', $this->user->id)
                                                            ->groupBy('months')
                                                            ->pluck('total', 'months')
                                                            ->toArray(),true);

        $data['monthly_failed_post']      = sortByMonth(SocialPost::filter(["platform:slug"])
                                                            ->date()
                                                            ->selectRaw("MONTHNAME(created_at) as months, COUNT(*) as total")
                                                            ->whereYear('created_at', '=',date("Y"))
                                                            ->failed()
                                                            ->where('user_id', $this->user->id)
                                                            ->groupBy('months')
                                                            ->pluck('total', 'months')
                                                            ->toArray(),true);

        $data['subscription_log']          = Subscription::with(['user','package','oldPackage'])
                                                            ->where('user_id', $this->user->id)
                                                            ->date()
                                                            ->latest()
                                                            ->take(8)
                                                            ->get();







        return $data;

     }


    /**
     * profile Update view
     * @param Request $request
     * @return View
     */
    public function profile(Request $request ) :View{

        return view('user.profile',[
            'meta_data'=> $this->metaData(['title'=> translate("Profile")])
        ]);
    }


    /**
     * profile Update
     * @param Request $request
     * @return RedirectResponse
     */
    public function profileUpdate(Request $request ) :RedirectResponse{

        $request->validate([
            'name'               => ["required","max:100",'string'],
            'username'           => ['required',"string","max:155","alpha_dash",'unique:users,username,'.$this->user->id],
            "country_id"         => ['nullable',"exists:countries,id"],
            'phone'              => ['unique:users,phone,'.$this->user->id ,'max:191'],
            'email'              => ['email','required','unique:users,email,'.$this->user->id],
            'auto_subscription'  => ['nullable', Rule::in(StatusEnum::toArray())],
            'address'            => ['nullable','array'],
            'address.*'          => ['nullable','max:191'],
            "image"              => ['nullable','image', new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true)) ]
        ]);

        $user                       =  $this->user;
        $user->name                 =  $request->input('name');
        $user->username             =  $request->input('username');
        $user->phone                =  $request->input('phone');
        $user->email                =  $request->input('email');
        $user->address              =  $request->input('address',[]);
        $user->country_id           =  $request->input('country_id');

        $user->auto_subscription    =  $request->input('auto_subscription')?? StatusEnum::false->status();

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


        return back()->with(response_status('Profile Updated'));
    }


    /**
     * update password
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function passwordUpdate(Request $request ): RedirectResponse
    {
        $rules   = [
            'current_password' => 'required|max:100',
            'password'         => 'required|confirmed|min:6',
        ];

        if(site_settings('strong_password') == StatusEnum::true->status()){
            $rules['password']    =  ["required","confirmed",Password::min(8)
                                        ->mixedCase()
                                        ->letters()
                                        ->numbers()
                                        ->symbols()
                                        ->uncompromised()
                                    ];
        }

        $request->validate($rules);
        $user = $this->user;
        if( $this->user->password && !Hash::check($request->input('current_password'), $this->user->password)) {
            return back()->with('error', translate("Your Current Password does not match !!"));
        }

        $user->password = $request->input('password');
        $user->save();
        return back()->with(response_status('Password Updated'));
    }



    /**
     * read a notifications
     *
     */

    public function readNotification(Request $request) :string{

        $notification = Notification::where('notificationable_type','App\Models\User')
                                ->where("id", $request->input("id"))
                                ->where("notificationable_id",$this->user->id)
                                ->first();
        $status  = false;
        $message = translate('Notification Not Found');
        if( $notification ){
            $notification->is_read =  (StatusEnum::true)->status();
            $notification->save();
            $status = true;
            $message = translate('Notification Readed');
        }
        return json_encode([
            "status"  => $status,
            "message" => $message
        ]);

    }


    /**
     * view  all notifications
     *
     */

    public function notification(Request $request) :View{

        Notification::where('notificationable_type','App\Models\User')
                ->where("notificationable_id",$this->user->id)
                ->update([
                    "is_read" =>  (StatusEnum::true)->status()
                ]);

        return view('user.notifications',[
            'meta_data'=> $this->metaData(['title'=>translate("Notifications")]),
            'notifications' => Notification::where('notificationable_type','App\Models\User')
                                    ->where("notificationable_id",$this->user->id)
                                    ->latest()
                                    ->paginate(paginateNumber())
        ]);


    }


    /**
     * Affiliate Config Update
     * @param Request $request
     * @return RedirectResponse
     */
    public function affiliateUpdate(Request $request ) :RedirectResponse{

        $response = response_status('Affiliate System Is Currently Disabled');
        if(site_settings("affiliate_system") == StatusEnum::true->status()){
            $response = response_status('Referral Code Updated');
            $request->validate([
                'referral_code'      => ['required','unique:users,referral_code,'.$this->user->id,'max:155'],
            ]);

            $user                       =  $this->user;
            $user->referral_code        =  $request->input('referral_code');
            $user->save();

        }

        return back()->with( $response);
    }


    /**
     * Webhook Config Update
     * @param Request $request
     * @return RedirectResponse
     */
    public function webhookUpdate(Request $request ) :RedirectResponse{

        $response = response_status('You current plan doesnot have webhook access');
        if($this->webhookAccess == StatusEnum::true->status()){
            $response = response_status('Webhook Api Key Updated');
            $request->validate([
                'webhook_api_key'      => ['required','unique:users,webhook_api_key,'.$this->user->id],
            ]);

            $user                       =  $this->user;
            $user->webhook_api_key      =  $request->input('webhook_api_key');
            $user->save();

        }

        return back()->with( $response);
    }

}
