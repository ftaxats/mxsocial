<?php

namespace App\Http\Requests\Admin;

use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentMethodRequest extends FormRequest
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
        
        $rules =  [ 

            'serial_id'          => ["numeric","gt:0","between:0,10000000"],
            'currency_id'        => ["required","exists:currencies,id"],
            'parameter.*'        => [
                                      Rule::requiredIf(function () { 
                                         return request()->route("type") ==  'automatic' ;
                                      })
                                    ],
            'parameter'          => ['array'],

            'minimum_amount'     => ['required','gt:-1','numeric','min:1','max:99999999','decimal:0,8','lt:maximum_amount'],
            'maximum_amount'     => ['required','gt:-1','numeric','min:1','max:99999999','decimal:0,8'],

            'percentage_charge'  => ["numeric","gt:-1","between:0,99999999.99"],
            'fixed_charge'       => ["numeric","gt:-1","between:0,99999999.99"],
            'payment_notes'      => ["max:255"],


            "name"               => [Rule::requiredIf(function () { 
                return request()->route("type") ==  'manual' ;
             }),'max:255','unique:payment_methods,name,'.request()->input('id')],
            "field_name"         => [Rule::requiredIf(function () { 
                return request()->route("type") ==  'manual' ;
             }),'array'],
            "field_name.*"       => [Rule::requiredIf(function () { 
                return request()->route("type") ==  'manual' ;
             }),"max:255"],
            "type"               => [Rule::requiredIf(function () { 
                return request()->route("type") ==  'manual' ;
             }),'array'],
            "type.*"             => [Rule::requiredIf(function () { 
                return request()->route("type") ==  'manual' ;
             }), Rule::in(['text','file','textarea'])],
            "validation"         => [Rule::requiredIf(function () { 
                return request()->route("type") ==  'manual' ;
             }),'array', Rule::in(['required','nullable'])],
            "validation.*"       => [Rule::requiredIf(function () { 
                return request()->route("type") ==  'manual' ;
             })],

            "image"              => ['nullable','image', new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true)) ]
        ];


        if(request()->routeIs('admin.paymentMethod.update')){
            $rules['id'] = ["required",'exists:payment_methods,id'];
        }


        return $rules;
    }
}
