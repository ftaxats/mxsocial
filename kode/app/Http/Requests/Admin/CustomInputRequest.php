<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomInputRequest extends FormRequest
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
            'custom_inputs.*.labels'      => ['required'],
            'custom_inputs.*.type'        => ['required',Rule::in(['text','file','textarea','date','email','number'])],
            'custom_inputs.*.required'    => ['required',Rule::in(StatusEnum::toArray())],
            'custom_inputs.*.placeholder' => ['required'],
            'custom_inputs.*.default'     => ['required'],
            'custom_inputs.*.multiple'    => ['required'],
        ];
    }


    public function messages()
    {
         return [ 
            'custom_inputs.*.labels.required'      => translate('All Labels Field Is Required'),
            'custom_inputs.*.type.required'        => translate('All Type Field Is Required'),
            'custom_inputs.*.required.required'    => translate('All Required Field Is Required'),
            'custom_inputs.*.placeholder.required' => translate('All Placeholder Field Is Required'),
            'custom_inputs.*.default.required'     => translate('All Default Field Is Required'),
            'custom_inputs.*.multiple.required'    => translate('All Multiple Field Is Required'),
         ];
    }
}
