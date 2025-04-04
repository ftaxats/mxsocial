<?php

namespace App\Http\Controllers\User\Auth;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\AuthenticateRequest;
use App\Http\Services\User\AuthService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View ;
use Illuminate\Http\RedirectResponse;


class LoginController extends Controller
{

    private $maxLoginAttempts,$lockoutTime, $authService;
    public function __construct()
    {

        $this->authService = new AuthService();
        $this->maxLoginAttempts = site_settings("max_login_attemtps");
        $this->lockoutTime = 2*60;
    }

    /**
     * Undocumented function
     *
     * @return View
     */
    public function login():View{

        return view('user.auth.login',[
            'meta_data'=> $this->metaData(
               [
                  "title" => trans("default.login"),
               ]
            )
        ]);
    }


    /**
     * Authenticate a user
     *
     * @param AuthenticateRequest $request
     * @return RedirectResponse
     */
    public function authenticate(AuthenticateRequest $request) :RedirectResponse
    {
        $field             = $this->getLoginField($request->input('login_data'));
        $remember_me       = $request->has('remember_me');
        $credentials       = [$field => request()->input('login_data'), 'password' => request()->input('password')];
        $attemptValidtion  = site_settings("login_attempt_validation");

        if($attemptValidtion == StatusEnum::true->status() && $this->hasTooManyLoginAttempts($request, $field)) return $this->sendLockoutResponse($request, $field);

        if($this->authService->loginWithOtp()){
            $user =  User::where("phone",request()->input('login_data'))->first();

            if($user && $this->authService->otpConfiguration($user))  return redirect(route('auth.otp.verification'))
            ->with(response_status('Check your phone! An OTP has been sent successfully.'));

            return redirect()->back()->with(response_status("Invalid credential","error"));
        }


        if (Auth::guard('web')->attempt( $credentials ,$remember_me)){
            $this->onSuccessfulLogin($request, $field);
            return redirect()->intended('user/dashboard')->with(response_status('Login success'));
        }


        if($attemptValidtion  == StatusEnum::true->status()) $this->incrementLoginAttempts($request, $field);

        return redirect()->back()->with(response_status("Invalid credential","error"));
    }



    private function onSuccessfulLogin(AuthenticateRequest $request, string $field): void {
        $user = auth_user('web');
        $user->last_login = Carbon::now();
        $user->save();
        $this->clearLoginAttempts($request, $field);
    }



    /**
     * get login filed
     * @param string $login
     * @return string
     */
    public function getLoginField(string $login):string{

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        } elseif (preg_match('/^[0-9]+$/', $login)) {
            return 'phone';
        }
        return 'username';
    }


    protected function hasTooManyLoginAttempts(Request $request,string $field)
    {
        return RateLimiter::tooManyAttempts($this->throttleKey($request, $field), $this->maxLoginAttempts);
    }

    /**
     *
     * @param Request $request
     * @param string $field
     * @return string
     */
    protected function throttleKey(Request $request, string $field) :string
    {
        return $field . '|' . $request->ip();
    }


    /**
     * send lockout response
     *
     * @param Request $request
     * @param string $field
     * @return RedirectResponse
     */
    protected function sendLockoutResponse(Request $request,string $field) :RedirectResponse
    {
        $seconds = RateLimiter::availableIn($this->throttleKey($request,$field));
        $minutes = ceil($seconds / 60);
        return redirect()->back()->with(
            'error', translate("Too many login attempts!! Please try again after ").$minutes.' minute '
        );
    }

    protected function incrementLoginAttempts(Request $request, string $field)
    {
        RateLimiter::hit($this->throttleKey($request, $field),$this->lockoutTime);
    }

    /**
     * clear login attempts
     * @param Request $request
     * @param string $field
     * @return void
     */
    protected function clearLoginAttempts(Request $request,string $field)
    {
        RateLimiter::clear($this->throttleKey($request, $field));
    }

    /**
     * logout method
     *
     * @return RedirectResponse
     */
    public function logout() :RedirectResponse{

        Auth::guard('web')->logout();
        return redirect('/');
    }
}
