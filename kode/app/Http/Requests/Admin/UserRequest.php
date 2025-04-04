<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\StatusEnum;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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

        $password =  request()->routeIs('admin.user.update') ? 'nullable' :"required";
  
        $rules = [
            'name'               => ["required","max:100",'string'],
            'username'           => ['required',"string","max:155","alpha_dash",'unique:users,username,'.request()->id , 'max:191'],
            "country_id"         => ['nullable',"exists:countries,id"],
            'phone'              => ['unique:users,phone,'.request()->id , 'max:191'],
            'email'              => ['email','required','unique:users,email,'.request()->id , 'max:191'],
            'password'           => [$password ,Password::min(6),"confirmed"],
            'status'             => ['required', Rule::in(StatusEnum::toArray())],
            'email_verified'     => ['nullable', Rule::in(StatusEnum::toArray())],
            'auto_subscription'  => ['nullable', Rule::in(StatusEnum::toArray())],
            'is_kyc_verified'    => ['nullable', Rule::in(StatusEnum::toArray())],
            "image"              => ['nullable','image', new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true)) ]
        ];

        if(site_settings('strong_password') == StatusEnum::true->status()){

            $rules['password']    =  [ $password,"confirmed",Password::min(8)
                                                                        ->mixedCase()
                                                                        ->letters()
                                                                        ->numbers()
                                                                        ->symbols()
                                                                        ->uncompromised()
                                    ];
        }

        if(request()->routeIs('admin.user.update')){
            $rules['id']  = 'required|exists:users,id';
            $remove       = ['status'];
            $rules        = array_diff_key($rules, array_flip($remove));
        }

        return $rules;
    }



    /**
     * Return validation error message
     *
     * @return array
     */
    public function messages() : array {

        return [ 
            'country_id.exists'      => translate('Invalid country'),
            'id.exists'              => translate('Invalid user'),
            'id.required'            => translate('Invalid user'),
        
        ];


    }
}
