<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

use App\Enums\StatusEnum;
use App\Rules\Admin\TranslationRule;
use Illuminate\Validation\Rule;
class MenuRequest extends FormRequest
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
            'serial_id'       => ['required','numeric','gt:-1','max:100000'],
            'name'            => ['required',"max:255","unique:menus,name,".request()->id,],
            'url'             => ['required','max:100',"unique:menus,url,".request()->id],
            'section'         => ['array'],
        ];

        if(request()->routeIs('admin.menu.update')){
             $rules ['id'] = "required|exists:menus,id";
        }
        return $rules;
    }
}
