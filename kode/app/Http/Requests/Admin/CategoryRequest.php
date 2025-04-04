<?php

namespace App\Http\Requests\Admin;

use App\Enums\CategoryDisplay;
use App\Enums\StatusEnum;
use App\Models\Admin\Category;
use App\Rules\General\FileExtentionCheckRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Closure;

class CategoryRequest extends FormRequest
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
            'title'             => ['array'],
            "title.*"           => ['max:155'],
            "title.default"     => ["required","unique:categories,title,".request()->id],
            'slug'              => ["max:150","unique:categories,slug,".request()->id],
            "parent_id"         => ["nullable","exists:categories,id",function (string $attribute, mixed $value, Closure $fail)  {

                $parent = Category::active()
                                ->doesntHave('parent')
                                ->where('id',request()->input('parent_id'))
                                ->first();
                
                if (!$parent) $fail(translate("Invalid Parent Category"));
            }],
            
            'description'       => ["nullable",'string','max:255'],
            'meta_title'        => ["nullable","string","max:155"],
            'meta_description'  => ["nullable",'string','max:255'],
            'icon'              => ["required",'max:100'],
            'meta_keywords'     => ['array'],
            'meta_keywords.*'   => ['max:150'],
            'is_feature'        => [Rule::in(StatusEnum::toArray())],
        ];
        if(request()->routeIs('admin.category.update')){
            $rules['id'] = ["required",'exists:categories,id'];
        }
        return  $rules;
    }
}
