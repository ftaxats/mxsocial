<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
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

        return [
            'method_id' => ['required','exists:payment_methods,id'],
            "amount"    => ['required','numeric','min:0']
        ];
    }




    public function messages(): array
    {


        return [
            'method_id.required' => translate('Select a payment method'),
            'method_id.exists'   => translate('Invalid payment method'),
            'amount.required'    => translate('Enter deposit amount'),
            'amount.numeric'     => translate('Amount should be a valid number'),
            'amount.min'         => translate('Amount should be greater than -1'),
        ];
    }
}
