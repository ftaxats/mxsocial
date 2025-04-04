<?php

namespace App\Http\Controllers\User\Auth;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class SocialAuthController extends Controller
{
    public $oauthCreds ;
    public function __construct(){

        $this->oauthCreds = json_decode(site_settings('social_login_with'),true);
    }


    public function redirectToOauth(Request $request, string $service)
    {
        $this->setConfig($service);
        return Socialite::driver($service)->redirect();
    }


    /**
     * Set configuration
     *
     * @param string $service
     * @return void
     */
    public function setConfig(string $service) :void{

        $credential               = Arr::get($this->oauthCreds ,$service."_oauth",[]);
        $credential["redirect"]   = url('login/'.$service.'/callback');
        Arr::forget($credential, 'status');
        Config::set('services.'.$service, $credential);

    }

    /**
     * handle auth call back
     *
     * @param string $service
     * @return RedirectResponse
     */
    public function handleOauthCallback(string $service) : \Illuminate\Http\RedirectResponse
    {

        $this->setConfig($service);


        try {
            $userOauth = Socialite::driver($service)->stateless()->user();

        } catch (\Exception $e) {
            return back()->with('error',translate('Setup Your Social Credentail!! Then Try Agian'));
        }

        $user = User::where('email',$userOauth->email)->first();
        if(!$user){
            $user                    = new User();
            $user->name              = Arr::get($userOauth->user,"name", null) ;
            $user->email             = $userOauth->email ;
            $user->o_auth_id         = Arr::get($userOauth->user,"id", null);
            $user->email_verified_at = Carbon::now();
            $user->save();
        }

        Auth::guard('web')->login($user);
        return redirect()->route('user.home')->with(response_status("Login Success"));
    }
}
