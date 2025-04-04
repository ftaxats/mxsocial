<?php

namespace App\Http\Requests\User;

use App\Enums\StatusEnum;
use App\Http\Services\User\AuthService;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRegisterRequest extends FormRequest
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

        $rules = [
            'name'               => ["required","max:100",'string'],
            'username'           => ['required',"string","max:155","alpha_dash",'unique:users,username'],
            "country_id"         => ['nullable',"exists:countries,id"],
            'phone'              => ['unique:users,phone'],
            'email'              => ['email','required','unique:users,email'],
            'password'           => ['required',Password::min(6),"confirmed"],
            'terms_condition'    => ["required"],
        ];

        if(site_settings('strong_password') == StatusEnum::true->status()){

            $rules['password']    =  [
                                      "required","confirmed",Password::min(8)
                                                ->mixedCase()
                                                ->letters()
                                                ->numbers()
                                                ->symbols()
                                                ->uncompromised()
                                    ];
        }


        if(site_settings("captcha_with_registration") == StatusEnum::true->status()){

            if(site_settings("default_recaptcha") == StatusEnum::true->status()){
                $rules['default_captcha_code'] = (new AuthService())->captchaValidationRules();                
            }
            elseif($googleCaptcha->status == StatusEnum::true->status()){
                $rules['g-recaptcha-response'] = (new AuthService())->captchaValidationRules("google");      
            }

        }
       
        return  $rules;
    }


   

  
}
