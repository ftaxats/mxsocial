<?php
namespace App\Http\Services\User;

use App\Enums\StatusEnum;
use App\Http\Utility\SendMail;
use App\Http\Utility\SendSMS;
use App\Models\Admin;
use App\Models\Core\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Closure;
use Illuminate\Support\Facades\Http;

use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;
use Illuminate\Database\Eloquent\Model;

class AuthService 
{


    
    /**
     * send otp method
     *
     * @return array
     */
    public function sendOtp(Model $sendTo ,string $template = "PASSWORD_RESET" , string $medium = 'email' ) :array {
        

        $code = generateOTP();
        
        $templateCode = [
            'name'        => $sendTo->name,
            'otp_code'    => $code,
            'time'        => Carbon::now(),
        ];

        $sendTo->otp()->delete();

        $expiredTime = (int) site_settings('otp_expired_in');

        $otp               = new Otp();
        $otp->otp          = $code;
        $otp->type         = strtolower($template);
        $otp->expired_at   = Carbon::now()->addSeconds($expiredTime);
        $sendTo->otp()->save($otp);

        $templateCode['expire_time']  = $otp->expired_at;
    
        $jobs =  [
            "email"     => "App\Jobs\SendMailJob",
            "sms"       => "App\Jobs\SendSmsJob",
        ];

        Arr::get($jobs, $medium)::dispatch($sendTo ,$template,$templateCode);

        return [
            'otp'         => $otp,
            'status'      => true,
            'message'     => "Verification code has been dispatched",
        ];


    
    }


    /**
     * Captcha validation rules
     *
     * @param string $type
     * @return array
     */
    public function captchaValidationRules(string $type = 'default') :array {
        $googleCaptcha = (object) json_decode(site_settings("google_recaptcha"));

        $rules = ['required' , function (string $attribute, mixed $value, Closure $fail) {
            if (strtolower($value) != strtolower(session()->get('gcaptcha_code'))) {
                $fail(translate("Invalid captcha code"));
            }
        }];

        if($type =="google"){
            $rules =  ['required' , function (string $attribute, mixed $value, Closure $fail) use($googleCaptcha) {
                $g_response =  Http::asForm()->post("https://www.google.com/recaptcha/api/siteverify",[
                    "secret"=> $googleCaptcha->secret_key,
                    "response"=> $value,
                    "remoteip"=> request()->ip,
                ]);
                
                if (!$g_response->json("success")) $fail(translate("Recaptcha validation failed"));
            }];
        }

        return $rules;
    }




    /**
     * Otp login check
     *
     * @return boolean
     */
    public function loginWithOtp() :bool {

        $loginAttributes =  json_decode(site_settings('login_with'),true);
        if(is_array($loginAttributes)
            && count($loginAttributes) == 1 
            && in_array('phone',$loginAttributes) 
            && site_settings('sms_otp_verification') == StatusEnum::true->status() )  return true;

        return false;

    }
    


    
    /**
     * Otp login check
     *
     * @return boolean
     */
    public function otpConfiguration(User $user , string $type = 'sms' ,string $template = 'OTP_VERIFY') :bool {

        $status = true;
        try {

            $response = $this->sendOtp($user,$template,$type);
            $otp      = Arr::get($response,"otp",collect());
            session()->put("otp_expire_at",$otp->expired_at);
            $field = $type == 'sms' ? "phone" :"email";
            session()->put("user_identification",[
                'field' => $field,
                'value' =>  $user->{$field},
            ]);
            

        } catch (\Throwable $th) {
            $status = false ;
        }

        return $status;

    }

 

}