<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContentRequest extends FormRequest
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
          'name'            => ["required","unique:contents,name,".request()->id ,'max:191'],
          'content'         => ["required","string"],
        ];
        if(request()->routeIs('admin.content.update')){
            $rules['id'] = ["required",'exists:contents,id'];
        }
        return $rules;
    }
}
