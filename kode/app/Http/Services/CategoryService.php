<?php
namespace App\Http\Services;

use App\Enums\CategoryDisplay;
use App\Enums\StatusEnum;
use App\Models\Admin\Category;
use App\Models\Core\File;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Traits\Fileable;
use Illuminate\Support\Facades\DB;
use App\Traits\ModelAction;
class CategoryService
{

    use Fileable , ModelAction;



    /**
     * store category
     *
     * @param Request $request
     * @return void
     */
    public function save(Request $request) :void{

        $category = DB::transaction(function() use ($request) {

            $category                   = new Category();
            $category->parent_id        = $request->input('parent_id');
            $category->title            = Arr::get($request->input('title'),'default','');
            $category->display_in       = (string) CategoryDisplay::TEMPLATE->value;
            $category->icon             = $request->input('icon');
            $category->description      = $request->input('description');
            $category->save();


            $this->saveTranslation($category,$request->input('title'),'title');

            return $category;

        });

    }


    /**
     * update category
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request) :void{

        DB::transaction(function() use ($request) {
            $category                   = Category::where('id',$request->input('id'))->firstOrfail();
            $category->parent_id        = $request->input('parent_id');
            $category->title            = Arr::get($request->input('title'),'default','');
            $category->icon             = $request->input('icon');
            $category->display_in       = (string) CategoryDisplay::TEMPLATE->value;
            $category->description      = $request->input('description');
            $category->update();
            $this->saveTranslation($category,$request->input('title'),'title');
        });

    }


}
