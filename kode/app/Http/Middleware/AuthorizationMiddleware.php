<?php

namespace App\Http\Middleware;

use App\Enums\StatusEnum;
use App\Http\Controllers\User\Auth\LoginController;
use App\Http\Services\User\AuthService;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


        try {


            $user              = auth_user('web') ;

            if($user){

                $authControl       = new LoginController(); 
                $authService       = new AuthService(); 
                $emailVerification = site_settings('email_verification');
        
    
                if($user->status == StatusEnum::false->status())   return  $authControl->logout()->with(response_status('Your account has been suspended indefinitely due to a violation of our terms and conditions. For further assistance, please contact our support team.','error'));

                if($emailVerification  == StatusEnum::true->status() 
                  && !$user->email_verified_at ){

                    if(session()->get("otp_expire_at",Carbon::now()) <= Carbon::now()) 
                    $authService->otpConfiguration($user,'email','REGISTRATION_VERIFY');
                    return redirect()->route("auth.email.verification")->with('success',translate("An email verification code has been dispatched to your registered email address. Kindly check your inbox for further instructions."));
                }


            }
           

            
        } catch (\Throwable $th) {
           
        }



        return $next($request);
    }
}
