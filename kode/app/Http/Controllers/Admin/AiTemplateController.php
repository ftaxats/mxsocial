<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\AiTemplateRequest;
use App\Http\Services\AiService;
use App\Models\Admin\Category;
use App\Models\AiTemplate;
use Illuminate\Http\Request;
use App\Traits\ModelAction;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class AiTemplateController extends Controller
{

    use ModelAction;
    protected $categories , $aiService;
    /**
     *
     * @return void
     */
     public function __construct(){

        //check permissions middleware
        $this->middleware(['permissions:view_ai_template'])->only(['list']);
        $this->middleware(['permissions:create_ai_template'])->only(['store','create','categoryCreate','categories']);
        $this->middleware(['permissions:update_ai_template'])->only(['updateStatus','update','edit','bulk','categoryCreateEdit']);
        $this->middleware(['permissions:delete_ai_template'])->only(['destroy','bulk']);

        $this->middleware(function (Request $request, Closure $next) {
            $this->categories = Category::template()->doesntHave('parent')->get();
            return $next($request);
        });
        $this->aiService =  new AiService();

    }


    /**
     * template list
     *
     * @return View
     */
    public function list(): View
    {
        $title = !request()->routeIs('admin.ai.template.default') ? 'Manage AI Template' :"Default Templates" ;

        if(request()->input(key: 'category')){
            $category = Category::where('slug',request()->input(key: 'category'))->first();
            if($category)  $title = $category->title . " : ". "AI Template";
        }

        return view('admin.ai_template.list',[

            'breadcrumbs'     => ['Home'=>'admin.home','AI Templates '=> null],
            'title'           =>  $title,
            'templates'       => AiTemplate::with(['category','user','admin','templateUsages','subCategory'])->search(['name'])
                                ->filter(["status",'category:slug','subCategory:slug','status','is_default','user:username,name'])
                                ->when(request()->routeIs('admin.ai.template.default'),function(Builder $q){
                                    return $q->default();
                                })
                                ->latest()
                                ->paginate(paginateNumber())
                                ->appends(request()->all()),

            "categories"      => $this->categories,
            "subCategories"   =>  Category::whereNotNull('parent_id')->get(),
        ]);
    }



    /**
     *  get list of categories
     *
     */
    public function categories (): View
    {
        return (new CategoryController())->list();
    }

    /**
     * create a  new category
     */
    public function categoryCreate (): View
    {
        return (new CategoryController())->create();
    }



    public function categoryCreateEdit (string $uid): View
    {
        return (new CategoryController())->edit($uid);
    }


    /**
     * @param int|string $uid
     * @return View
     */
     public function content(int|string $uid): View
     {

        $template = AiTemplate::with(['category'])->where('uid',$uid)->firstOrfail();
        return view('admin.ai_template.generate_content',[
            'breadcrumbs' =>  ['Home'=>'admin.home','AI Templates'=> 'admin.ai.template.list',"Generate Content"=>null],
            'title'       =>  'Generate Content For ' .$template->name ,
            'template'    =>  $template,
        ]);

     }




     public function contentGenrate(Request $request) :string {
        $templateRules =  $this->aiService->setRules($request);
        $request->validate(Arr::get($templateRules, 'rules', []),Arr::get($templateRules, 'messages', []));
        $response =  $request->input('custom_prompt') == StatusEnum::false->status()
                              ? $this->aiService->generatreContent($request,$templateRules['template'])
                              : $this->aiService->generatreCustomPromptContent($request,) ;
        return json_encode( $response);

     }


    /**
     * @return View
     */
    public function create(): View
    {
        return view('admin.ai_template.create',[
            'breadcrumbs' =>  ['Home'=>'admin.home','AI Templates'=> 'admin.ai.template.list',"Create"=>null],
            'title'       => 'Create Template',
            'categories'  =>  $this->categories,
        ]);

    }


    /**
     * store a  new template
     *
     * @param AiTemplateRequest $request
     * @return RedirectResponse
     */
    public function store(AiTemplateRequest $request) :RedirectResponse{
        return  back()->with($this->aiService->saveTemplate($request));
    }





    /**
     * edit a  new template
     *
     */
    public function edit(string $uid) :View{

        return view('admin.ai_template.edit',[
            'breadcrumbs' => ['Home'=>'admin.home','AI Templates'=> 'admin.ai.template.list',"Edit"=>null],
            'title'       => 'Update Template',
            'categories'  => $this->categories,
            'template'    => AiTemplate::where('uid',$uid)->firstOrfail()
        ]);

    }



    /**
     * Update a specific template
     *
     * @param AiTemplateRequest $request
     * @return RedirectResponse
     */
    public function update(AiTemplateRequest $request) :RedirectResponse {
        return  back()->with($this->aiService->updateTemplate($request));
    }

    /**
     * Update a specific template status
     *
     * @param Request $request
     * @return string
     */
    public function updateStatus(Request $request) :string{

        $request->validate([
            'id'      => 'required|exists:ai_templates,uid',
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['status','is_default'])],
        ]);

        return $this->changeStatus($request->except("_token"),[
            "model"    => new AiTemplate(),
        ]);
    }


    public function destroy(string|int $uid) :RedirectResponse{

        $template  = AiTemplate::withCount(['templateUsages'])->custom()->where('uid',$uid)->firstOrfail();
        $response  =  response_status('Can not be deleted!! item has related data','error');
        if(1  > $template->template_usages_count ){
            $template->delete();
            $response  =  response_status('Item deleted successfully');
        }

        return  back()->with($response);
    }


    /**
     * Bulk action
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulk(Request $request): RedirectResponse
    {
        try {
            $response =  $this->bulkAction($request,[
                "model" => new AiTemplate(),
            ]);

        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
    }
}
