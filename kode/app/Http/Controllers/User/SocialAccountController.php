<?php

namespace App\Http\Controllers\User;

use App\Enums\AccountType;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Http\Services\Account\facebook\Account;
use App\Models\MediaPlatform;
use App\Models\Package;
use App\Models\SocialAccount;
use App\Traits\ModelAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Traits\AccountManager;
class SocialAccountController extends Controller
{


    use ModelAction , AccountManager;

    protected  $user ,$subscription , $accessPlatforms ,$remainingProfile;


    public function __construct(){


        $this->middleware(function ($request, $next) {
            $this->user                   = auth_user('web');
            $this->subscription           = $this->user->runningSubscription;
            $this->accessPlatforms        = (array) ($this->subscription ? @$this->subscription->package->social_access->platform_access : []);

            $this->remainingProfile       = (int) ($this->subscription ? @$this->subscription->total_profile : 0);
            return $next($request);
        });
    }

    /**
     * Social account list
     *
     * @return View
     */
    public function list() :View{


        if(request()->input('platform',null)) $platform = MediaPlatform::whereIn('id',(array)$this->accessPlatforms)
                                                                    ->where('slug', request()->input('platform'))
                                                                    ->firstOrfail();

        return view('user.social.account.list',[

            'meta_data'       => $this->metaData(['title'=> translate("Social Accounts")]),

            'accounts'        => SocialAccount::with(['user','subscription','subscription.package','platform'])
                                                    ->where("user_id",$this->user->id)
                                                    ->filter(["status",'platform:slug','name'])
                                                    ->latest()
                                                    ->paginate(paginateNumber())
                                                    ->appends(request()->all()),

        ]);
    }




    /**
     * Social platform list
     *
     * @return View
     */
    public function platform() :View{

        return view('user.social.account.platform',[
            'meta_data'       => $this->metaData(['title'=> translate("Social Platform")])
        ]);
    }


    /**
     * Create a new account
     * @param string $slug
     * @return View | RedirectResponse
     */
    public function create(string $slug) :View | RedirectResponse{



        if($this->checkSubscriptionProfile()){

            $platform = MediaPlatform::with(['file'])
            ->whereIn('id',(array)$this->accessPlatforms)
            ->active()
            ->integrated()
            ->where('slug', $slug)
            ->first();


            if(!$platform)
                  return redirect()->back()->with("error", translate("You dont have access to this platform!! please upgrade your subscription"));
            return view('user.social.account.create',[

                'meta_data'       => $this->metaData(['title'=>  "Create ".$platform->name. " Account"]),
                'platform'        => $platform,

            ]);
        }

        return redirect()->route("user.social.account.list")->with("error", translate("Unable to create a new account: Insufficient subscription balance. Please subscribe a new plan to proceed with the account creation process. Thank you"));
    }

    public function checkSubscriptionProfile() :bool{
        return $this->remainingProfile > 0 ? true : false;
    }


    /**
     * store a new account
     * @param AccountRequest $request
     * @return RedirectResponse
     */
    public function store(AccountRequest $request) :RedirectResponse{

        $response  = response_status(translate("Unable to create a new account: Insufficient subscription balance. Please subscribe a new plan to proceed with the account creation process. Thank you"),'error');
        if($this->checkSubscriptionProfile()){
                $platform = MediaPlatform::where('id',request()->input("platform_id"))
                                ->whereIn('id',(array)$this->accessPlatforms)
                                ->active()
                                ->integrated()
                                ->firstOrfail();


                $class   = 'App\\Http\\Services\\Account\\'.$platform->slug.'\\Account';

                $service =  new  $class();

                $response = $service->{$platform->slug}($platform,$request->except("_token"),'web');
        }

        return back()->with($response);


    }


    /**
     * store a new account
     * @param Request $request
     * @return RedirectResponse
     */
    public function reconnect(Request $request) :RedirectResponse{

        $request->validate([
            'id'           => "required|exists:social_accounts,id",
            'access_token' => "required",
        ]);

        $account  = SocialAccount::with('platform')
                                ->where('user_id',$this->user->id)
                                ->where("id",request()->input("id"))
                                ->where('subscription_id', @$this->subscription?->id)
                                ->first();

        $response = response_status(translate('This account doesnot belongs to your current subscription'),'error');

        if($account){
            $request->merge([
                'account_id'   => $account->id,
                'account_type' => $account->account_type,
                'page_id'      => $account->account_type == AccountType::PAGE->value ? $account->account_id : null,
                'group_id'     => $account->account_type == AccountType::GROUP->value ? $account->account_id : null,
            ]);

            $class   = 'App\\Http\\Services\\Account\\'.$account->platform->slug.'\\Account';
            $service =  new  $class();
            $response = $service->{$account->platform->slug}($account->platform,$request->except("_token"),'web');
        }


        return back()->with($response);
    }


    /**
     * store a new account
     * @param string $uid
     * @return View|RedirectResponse
     */
    public function show(string $uid) :View | RedirectResponse{

        $account  = SocialAccount::with(['platform'])
                                        ->where('uid',$uid)
                                        ->where('user_id',$this->user->id)
                                        ->firstOrfail();

        $class    = 'App\\Http\\Services\\Account\\'.$account->platform->slug.'\\Account';
        $service  =  new  $class();

        $response = $service->accountDetails($account);

        if(@!$response['status']) return redirect()->route('user.social.account.list',['platform' => $account->platform->slug])->with('error',$response['message']);

        return view('user.social.account.show',[

            'meta_data'       => $this->metaData(['title'=>  "Account Feeds"]),
            'response'        => $response,
            'account'         => $account,

        ]);


    }

    public function destroy(string $id) :RedirectResponse {

        $account  = SocialAccount::withCount(['posts'])
                      ->where('user_id',$this->user->id)
                      ->where('id',$id)->firstOrfail();

        $response =  response_status('Can not be deleted!! item has related data','error');
        if(1  > $account->posts_count){
            $account->delete();
            $response =  response_status('Item deleted succesfully');
        }
        return  back()->with($response);
    }





    /**
     * Update a specific platform status
     *
     * @param Request $request
     * @return string
     */
    public function updateStatus(Request $request) :string{

        $account  = SocialAccount::with('platform')
                        ->where('user_id',$this->user->id)
                        ->where("uid",request()->input("id"))
                        ->where('subscription_id', @$this->subscription?->id)
                        ->first();
        if(!$account){
            return json_encode([
                'status'  => false,
                'message' => translate('This account doesnot belongs to your current subscription')
            ]);
        }

        $request->validate([
            'id'      => 'required|exists:social_accounts,uid',
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['status'])],
        ]);


        return $this->changeStatus($request->except("_token"),[
            "model"    => new SocialAccount(),
        ]);

    }
}
