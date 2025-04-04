<?php

namespace App\Http\Requests;

use App\Enums\AccountType;
use App\Models\MediaPlatform;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class AccountRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $platform = MediaPlatform::where('id',request()->input("platform_id"))
                        ->active()
                        ->integrated()
                        ->firstOrfail();
        $rules = [
            'platform_id'  => ["required","exists:media_platforms,id"],
            'account_type' => ["required", Rule::in(AccountType::toArray())],
            "group_id"     => [Rule::requiredIf(function () use( $platform ) {
                    if(request()->input("account_type") == AccountType::GROUP->value ) {
                        return true;
                    }
                    return false;
                })],
            "page_id"  => [Rule::requiredIf(function ()  use( $platform ) {
                    if(request()->input("account_type") == AccountType::PAGE->value ) {
                        return true;
                    }

                    return false;
                })],
          
        ];

        $inputs = Arr::get(config('settings.platforms_connetion_field'),$platform->slug,[]);


        foreach ($inputs as $key) {
            $rules[$key] = ["required"];
        }
        
        if(request()->routeIs('admin.ai.template.update')){
            $rules['id'] = ["required",'exists:ai_templates,id'];
        }
        return $rules;

   
    }
}
