<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\General\FileExtentionCheckRule;
class LogoSettingRequest extends FormRequest
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
            'site_settings.site_logo'       =>  [ 'image', new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true)) ],
            'site_settings.user_site_logo'  =>  [ 'image' ,new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true))],
            'site_settings.site_favicon'    =>  [ 'image', new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true))],
            'site_settings.meta_image'      =>  [ 'image', new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true))],
            'site_settings.loader_icon'     =>  [ 'image', new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true))],
        ];
    }
}
