<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Services\CategoryService;
use App\Models\Admin\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Traits\ModelAction;
use App\Traits\Fileable;
use Illuminate\Database\Eloquent\Builder;

class CategoryController extends Controller
{

    use ModelAction ,Fileable;
    private $categoryService;

    /**
     *
     * @return void
     */
    public function __construct()
    {

        $this->categoryService = new CategoryService();
        //check permissions middleware
        $this->middleware(['permissions:view_category'])->only(['list','subcategories']);
        $this->middleware(['permissions:create_category'])->only(['store','create']);
        $this->middleware(['permissions:update_category'])->only(['updateStatus','update','edit','bulk']);
        $this->middleware(['permissions:delete_category'])->only(['destroy','bulk']);
    }


    /**
     * category list
     *
     * @return View
     */
    public function list(): View
    {
        $title         =  translate('Manage Categories');
        $breadcrumbs   =  ['Home'=>'admin.home','Categories'=> null];
        if(request()->routeIs("admin.category.subcategories")){
            $category = Category::where('slug',request()->input('parent'))->firstOrfail();
            $title             = $category->title . ' : '.translate('Subcategories');
            $breadcrumbs       = ['Home'=>'admin.home','Categories'=> route('admin.category.list') ,"Subcategories" => null];
        }


        return view('admin.category.list',[
            'breadcrumbs'  =>  $breadcrumbs,
            'title'        =>  $title,
            'categories'   =>  Category::with(['createdBy','childrens'])
                                ->when(request()->routeIs(patterns: "admin.category.subcategories") && @$category  , function (Builder $q)  : void {
                                    $q->whereNotNull('parent_id');
                                },  function (Builder $q): void {
                                    $q->whereNull('parent_id');
                                })
                                ->template()
                                ->withCount(['templates','childrens','parent'])
                                ->search(['title','translations:value'])
                                ->filter(['parent:slug'])
                                ->latest()
                                ->paginate(paginateNumber())
                                ->appends(request()->all())
        ]);
    }


    /**
     * @return View
     */
    public function create() :View{

        return view('admin.category.create',[
            'breadcrumbs'      =>  ['Home'=>'admin.home','Categories'=> !request()->routeIs('admin.category.create') ? 'admin.category.list' :"admin.category.list","Create" => null],
            'title'            => 'Create Category',
            'categories'       => Category::active()->doesntHave('parent')->get(),
        ]);

    }


    /**
     * store a  new category
     *
     * @param CategoryRequest $request
     * @return RedirectResponse
     */
    public function store(CategoryRequest $request) :RedirectResponse{
        $this->categoryService->save($request);
        return  back()->with(response_status('Category created successfully'));
    }



    /**
     * edit a category
     *
     */
    public function edit(string $uid) :View{

        $category = Category::withoutGlobalScope('autoload')
                                            ->with(['translations'])
                                            ->where("uid",$uid)->firstOrfail();

        return view('admin.category.edit',[
            'breadcrumbs'       => ['Home'=>'admin.home','Categories'=> 'admin.category.list',"Edit" => null],
            'title'             => 'Edit Category',
            'category'          => Category::withoutGlobalScope('autoload')
                                        ->with(['translations'])
                                        ->where("uid",$uid)->firstOrfail(),
            'categories'        => Category::where('id','!=',$category->id)
                                        ->active()
                                        ->doesntHave('parent')
                                        ->get(),
        ]);

    }


    /**
     * Update a specific category
     *
     * @param CategoryRequest $request
     * @return RedirectResponse
     */
    public function update(CategoryRequest $request) :RedirectResponse{

        $this->categoryService->update($request);
        return  back()->with(response_status('Category updated successfully'));
    }

    /**
     * Update a specific category status
     *
     * @param Request $request
     * @return string
     */
    public function updateStatus(Request $request) :string{

        $request->validate([
            'id'      => 'required|exists:categories,uid',
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['status','is_feature'])],
        ]);

        return $this->changeStatus($request->except("_token"),[
            "model"    => new Category(),
        ]);

    }


    /**
     * destroy a specific category
     *
     * @param string|int $id
     * @return RedirectResponse
     */
    public function destroy(string | int $id) :RedirectResponse{

        $category  = Category::withoutGlobalScope('autoload')
        ->with(['translations'])->withCount(['articles','templates','childrens'])->where('id',$id)->firstOrfail();

        $response =  response_status('Can not be deleted!! item has related data','error');
        if(1  > $category->articles_count &&  1  > $category->templates_count &&  1  > $category->childrens_count ){
            $category->delete();
            @$category->translations()->delete();
            $response =  response_status('Item deleted succesfully');

        }
        return  back()->with($response);

    }

  /**
     * Bulk action
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulk(Request $request) :RedirectResponse {

        try {
            $response =  $this->bulkAction($request,[
                "model"        => new Category(),
                "with_count"   => ['articles','templates','childrens'],
            ]);

        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
    }
}
