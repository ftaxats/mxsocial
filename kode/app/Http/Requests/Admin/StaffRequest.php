<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\StatusEnum;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Validation\Rule;
class StaffRequest extends FormRequest
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
        $rules = [
            'username' => 'required|alpha_dash|unique:admins,username,'.request()->id,
            'role_id'  => "nullable|exists:roles,id",
            'phone'    => 'unique:admins,phone,'.request()->id,
            'email'    => 'email:rfc,dns|required|unique:admins,email,'.request()->id,
            'password' => 'required|min:5',
            'status'   => ['required',  Rule::in(StatusEnum::toArray())],
            "image"    => ['nullable','image', new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true)) ]
        ];

        if(request()->routeIs('admin.staff.update')){
            $rules['id'] = 'required|exists:admins,id';
            $rules       = array_diff_key($rules, array_flip( ['password', 'status']));
        }

        return $rules;
        
    }

   
}
