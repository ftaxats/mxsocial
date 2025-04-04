<?php

namespace App\Http\Requests\Admin;

use App\Enums\PlanDuration;
use App\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class PackageRequest extends FormRequest
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

          "title"                                    => ['required','unique:packages,title,'.request()->id],
          "duration"                                 => ["required", Rule::in(array_values(PlanDuration::toArray()))],
          "description"                              => ["required",'max:255'],
          "icon"                                     => ["required"],
          "price"                                    => ["required",'numeric','gt:-1',"between:0,99999999.99"],
          "discount_price"                           => ["nullable",'numeric','gt:-1', "between:0,99999999.99", "lte:price"],
          "social_access"                            => ['required','array'],
          "social_access.platform_access"            => ['required','array'],
          "social_access.platform_access.*"          => ['required','exists:media_platforms,id'],
          "social_access.profile"                    => ['required','integer','min:1','max:50000'],
          "social_access.post"                       => ['required','integer','min:-1','max:50000'],
          "social_access.webhook_access"             => ['nullable',Rule::in(StatusEnum::toArray())],
          "social_access.schedule_post"              => ['nullable',Rule::in(StatusEnum::toArray())],
          "ai_configuration.open_ai_model"           => ['nullable',Rule::in(array_keys(Arr::get(config('settings'),'open_ai_model',[])))],
          "ai_configuration.word_limit"              => ['required','integer','min:-1'],
          "ai_configuration.template_access"         => ['nullable','array'],
          "ai_configuration.template_access.*"       => ['exists:ai_templates,id'],
          "affiliate_commission"                     => ['nullable','integer','gt:-1','lte:100'],
          
       ];

       if(request()->routeIs('admin.package.update')){
         $rules ['id'] = "required:exists:packages,id";
       }
       
       return $rules;
    }


     /**
     * Return validation error message
     *
     * @return array
     */
    public function messages() : array {


      $rules = [

        "title"                                    => ['required','unique:packages,title,'.request()->id],
        "duration"                                 => ["required", Rule::in(array_values(PlanDuration::toArray()))],
        "description"                              => ["required",'max:255'],
        "price"                                    => ["required",'numeric','gt:-1',"between:0,99999999.99"],
        "discount_price"                           => ["nullable",'numeric','gt:-1', "between:0,99999999.99", "lte:price"],
        "social_access"                            => ['required','array'],
        "social_access.platform_access"            => ['required','array'],
        "social_access.platform_access.*"          => ['required','exists:media_platforms,id'],
        "social_access.profile"                    => ['required','integer','min:1','max:50000'],
        "social_access.post"                       => ['required','integer','min:-1','max:50000'],
        "social_access.webhook_access"             => ['nullable',Rule::in(StatusEnum::toArray())],
        "social_access.schedule_post"              => ['nullable',Rule::in(StatusEnum::toArray())],
        "ai_configuration.open_ai_model"           => ['nullable',Rule::in(array_keys(Arr::get(config('settings'),'open_ai_model',[])))],
        "ai_configuration.word_limit"              => ['required','integer','min:-1'],
        "ai_configuration.template_access"         => ['nullable','array'],
        "ai_configuration.template_access.*"       => ['exists:ai_templates,id'],
        "affiliate_commission"                     => ['nullable','integer','gt:-1','lte:100'],
        
     ];

      return [ 
          'required.required'                 => translate('Title field is required'),
          'required.unique'                   => translate('Title must be unique'),
          'description.required'              => translate('Description field is required'),
          'price.required'                    => translate('Price field is required'),
          'social_access.required'            => translate('Please configure platform configuration'),
          'social_access.platform_access'     => translate('Please select platform access'),
          'social_access.profile'             => translate('Total profile field is required'),
          'social_access.post'                => translate('Total post field is required'),
          'ai_configuration.word_limit'       => translate('No. of word filed is required'),
      ];


  }
}
