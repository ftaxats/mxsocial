<?php

namespace App\Http\Requests\Admin;

use App\Enums\EnumTrait;
use App\Enums\StatusEnum;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WithdrawRequest extends FormRequest
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
            'name'               => ['required','unique:withdraws,name,'.request()->id],
            
            'duration'           => ['required','gt:-1',"max:20000"],
            'minimum_amount'     => ['required','gt:-1','numeric','min:1','max:99999999','decimal:0,8','lt:maximum_amount'],
            'maximum_amount'     => ['required','gt:-1','numeric','min:1','max:99999999','decimal:0,8'],
            'fixed_charge'       => ['required','gt:-1','numeric','min:0','max:99999999','decimal:0,8'],
            'percent_charge'     => ['required',"gt:-1","max:99999999","decimal:0,8"],
            'description'        => ['max:250',"string"],
            "image"              => ['nullable','image', new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true)) ],


            "field_name"   => ["required", "array"],
            "field_name.*" => ["required", "max:255"],
            "type"         => ["required_with:field_name", "array"],
            "type.*"       => ["required_with:field_name", Rule::in(['text', 'file', 'textarea', "password"])],
            "validation"   => ["required_with:field_name", "array"],
            "validation.*" => ["required_with:field_name"],

        ];

        if(request()->routeIs('admin.withdraw.update')){
            $rules['id']         = ["required","exists:withdraws,id"];
        }
        return $rules;
    }


    public function messages(): array {


        return  [ "field_name.required"         => translate('Please add a new custom field')];

    }
}
