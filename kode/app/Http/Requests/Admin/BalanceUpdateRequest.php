<?php

namespace App\Http\Requests\Admin;

use App\Enums\BalanceTransferType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BalanceUpdateRequest extends FormRequest
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
        $rules = [
            'id'              => ['required',"exists:users,id"],
            "amount"          => ['required','numeric','min:-1'],
            "type"            => ['required',Rule::in(BalanceTransferType::toArray())],
            "payment_id"      => [ Rule::requiredIf(fn():bool => request()->input('type') === BalanceTransferType::DEPOSIT->value),
                                    Rule::exists('payment_methods', 'id')
                                 ],
            'method_id'       => [
                                    Rule::requiredIf(fn():bool => request()->input('type') === BalanceTransferType::WITHDRAW->value),
                                    Rule::exists('withdraws', 'id')
                                ],
            "remarks"         => ['required',"string"],
        ];

        $forgetKey = request()->input("type") ==  BalanceTransferType::DEPOSIT->value
                        ? "method_id" 
                        : "payment_id" ;

        data_forget($rules, $forgetKey);
        
        return $rules;
    }
}
