<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use App\Models\Admin\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Closure;

class AiTemplateRequest extends FormRequest
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
            'name'             => ["required","unique:ai_templates,name,".request()->id],
            'slug'             => ['unique:ai_templates,slug,'.request()->id],
            'category_id'      => ["required","exists:categories,id"],
            'sub_category_id'  => ["nullable","exists:categories,id",function (string $attribute, mixed $value, Closure $fail)  {
                                        $parent = Category::active()
                                                ->where('parent_id',request()->input('category_id'))
                                                ->where('id',request()->input('sub_category_id'))
                                                ->first();
                                
                                        if (!$parent) {
                                            $fail(translate("Invalid subcategory id"));
                                        }
                                  }],
            'description'      => ["required",'max:200',"string"],
            'icon'             => ["required",'max:100'],
            'is_default'       => ["required", Rule::in(StatusEnum::toArray())],
            'custom_prompt'    => ["required","string"],
            "field_name"       => ["nullable",'array'],
            "field_name.*"     => ["nullable","max:255"],
            "type"             => ["nullable",'array'],
            "instraction"      => ["nullable",'array'],
            "type.*"           => ["nullable", Rule::in(['text','file','textarea',"password"])],
            "validation"       => ["nullable",'array', Rule::in(['required','nullable'])],
            "validation.*"     => ["nullable"],
            "instraction.*"    => ["nullable"],

        ];
        if(request()->routeIs('admin.ai.template.update')){
            $rules['id'] = ["required",'exists:ai_templates,id'];
        }
        return $rules;
    }
}
