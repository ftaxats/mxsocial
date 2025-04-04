<?php

namespace App\Http\Requests\User;

use App\Enums\StatusEnum;
use App\Http\Services\User\AuthService;
use Illuminate\Foundation\Http\FormRequest;

class AuthenticateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {

        $googleCaptcha = (object) json_decode(site_settings("google_recaptcha"));


        $rules =  [
            'login_data' => ['required','max:155'],
            'password' => ['required','max:155']
        ];

        if((new AuthService())->loginWithOtp()){
            unset($rules['password']);
        }

        if(site_settings("captcha_with_login") == StatusEnum::true->status()){
            
            if(site_settings("default_recaptcha") == StatusEnum::true->status()){
                $rules['default_captcha_code'] = (new AuthService())->captchaValidationRules();                
            }
            elseif($googleCaptcha->status == StatusEnum::true->status()){
                $rules['g-recaptcha-response'] = (new AuthService())->captchaValidationRules("google");      
            }

        }

        return $rules;
    }
}
