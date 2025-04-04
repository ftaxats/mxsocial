<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\General\FileExtentionCheckRule;

class ProfileRequest extends FormRequest
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
       
        return [
            'username' => 'required|unique:admins,username,'.auth_user()->id,
            'phone'    => 'unique:admins,phone,'.auth_user()->id,
            'email'    => 'required|unique:admins,email,'.auth_user()->id,
            "name"     => "nullable|string",
            "image"    => ['nullable','image', new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true))]
        ];
    }
}
