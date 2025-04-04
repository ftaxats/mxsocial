<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CurrencyRequest extends FormRequest
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
            'name'          => 'required|max:155|unique:currencies,name,'.request()->id,
            'code'          => 'required|max:155|unique:currencies,code,'.request()->id,
            'symbol'        => 'required|max:155|unique:currencies,symbol,'.request()->id,
            'exchange_rate' => 'required|gt:-1|numeric|max:99999999|decimal:0,8',
        ];
        if(request()->routeIs('admin.currencies.update')){
            $rules['id']    = "required|exists:currencies,id";
        }
        return $rules;
    }
}
