<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\General\FileExtentionCheckRule;
use App\Rules\General\FileLengthCheckRule;
class FrontendSectionRequest extends FormRequest
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

        $appearances = @get_appearance(true);

        $rules = [
            "type" => ['required',Rule::in(['content','element'])],
            "key"  => ['required',Rule::in(array_keys($appearances))],
            "id"   => ['exists:frontends,id']
        ];

        foreach(request()->except('_token') as $k => $v){
            
            if($k == "image_input" ||  $k == "parent_id" ) continue;
            elseif($k == "slect_input"){
                foreach($v as $selecKey => $selectInput){
    
                    $ruleIn = [];
                    if(isset($appearances[request()->input('key')][request()->input('type')]['select'][$selecKey])){
                        $ruleIn = explode(",",$appearances[request()->input('key')][request()->input('type')]['select'][$selecKey]);
                    }
                    $rules[$k.".".$selecKey] =  ['required',Rule::in($ruleIn)];
                }
            }
            else{
                $rules[$k] =  ['required'];
            }
        }

        if(isset($appearances[request()->input('key')][request()->input('type')]['images'])){
          
           foreach($appearances[request()->input('key')][request()->input('type')]['images'] as $k => $v){
              $rules["image_input.".$k] = ['image', new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true))];
           }
        }

        return $rules;
    }
}
