<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use App\Rules\General\FileExtentionCheckRule;
use App\Rules\General\FileLengthCheckRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ArticleRequest extends FormRequest
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
            'title'            => ["required","unique:blogs,title,".request()->id],
            'slug'             => ['unique:blogs,slug,'.request()->id],
            'description'      => ["required"],
            'is_feature'       => ["nullable", Rule::in(StatusEnum::toArray())],
            "image"            => ['nullable','image', new FileExtentionCheckRule(json_decode(site_settings('mime_types'),true))],
            'meta_title'       => ["nullable","string","max:155"],
            'meta_description' => ["nullable",'string'],
            'meta_keywords'    => ['array'],
            'meta_keywords.*'  => ['max:150'],
        ];
        if(request()->routeIs('admin.blog.update')){
            $rules['id'] = ["required",'exists:blogs,id'];
        }
        return  $rules;
    }
}
