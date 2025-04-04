<?php

namespace App\Http\Requests\Admin;

use App\Models\Admin\Page;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Admin\TranslationRule;
use Illuminate\Validation\Rule;
use App\Enums\StatusEnum;
use App\Rules\Admin\TranslationUniqueRule;

class PageRequest extends FormRequest
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
            'serial_id'        =>  ['required','numeric','gt:-1','max:100000'],
            'title'            =>  ['required',"max:155","unique:pages,title,".request()->id],
            'description'      =>  ["required"],
            'meta_title'       =>  ['nullable',"max:155"],
            'slug'             =>  ["nullable",'unique:pages,slug,'.request()->id],
            'meta_description' =>  ['nullable'],
            'meta_keywords'    =>  ['array'],
            'show_in_header'   =>  [Rule::in(StatusEnum::toArray())],
            'show_in_footer'   =>  [Rule::in(StatusEnum::toArray())],
        ];
        if(request()->routeIs('admin.page.update')){
            $rules['id'] = ["required",'exists:pages,id'];
        }
        return $rules;
    }
}
