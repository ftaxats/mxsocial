<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContentRequest;
use App\Http\Services\ContentService;
use App\Models\Admin\Category;
use App\Models\AiTemplate;
use App\Models\Content;
use Illuminate\Http\Request;
use App\Traits\ModelAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
class CannedContentController extends Controller
{
    use ModelAction ;
    public $contentService;

    /**
     *
     * @return void
     */
     public function __construct(){

        //check permissions middleware
        $this->middleware(['permissions:view_content'])->only(['list']);
        $this->middleware(['permissions:create_content'])->only(['store','create']);
        $this->middleware(['permissions:update_content'])->only(['updateStatus','update','edit','bulk']);
        $this->middleware(['permissions:delete_content'])->only(['destroy','bulk']);

        $this->contentService  = new ContentService();

    }


    /**
     * Content list
     *
     * @return View
     */
    public function list() :View{

        return view('admin.content.list',[

            'breadcrumbs'  => ['Home'=>'admin.home','Contents'=> null],
            'title'        => 'Manage Predefined Content',
            'contents'     => Content::whereNull('user_id')
                                ->search(['name'])
                                ->latest()
                                ->paginate(paginateNumber())
                                ->appends(request()->all()),

            'categories'  => Category::template()
                                        ->doesntHave('parent')
                                        ->get(),

            'templates'   => AiTemplate::active()->get(),


        ]);
    }




    /**
     * store a  new content
     *
     * @param ContentRequest $request
     * @return RedirectResponse | string
     */
    public function store(ContentRequest $request) :RedirectResponse | string {

        $response = $this->contentService->save($request);

        if($request->ajax()){
            return json_encode([
                "message"  => translate('Content created successfully'),
                "status"   => true,
            ]);
        }
        return  back()->with($response);
    }




    /**
     * Update a specific Article
     *
     * @param ContentRequest $request
     * @return RedirectResponse
     */
    public function update(ContentRequest $request) :RedirectResponse {

        $content = Content::whereNull('user_id')
                  ->where("id",$request->input('id'))->firstOrfail();

        return  back()->with($this->contentService->update($request , $content));
    }

    /**
     * Update a specific Article status
     *
     * @param Request $request
     * @return string
     */
    public function updateStatus(Request $request) :string{

        $request->validate([
            'id'      => 'required|exists:contents,uid',
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['status'])],
        ]);

        return $this->changeStatus($request->except("_token"),[
            "model"    => new Content(),
        ]);
    }


    public function destroy(string | int $id) :RedirectResponse{

        $content  = Content::whereNull('user_id')->where('id',$id)->firstOrfail();
        $content->delete();
        return  back()->with(response_status('Item deleted succesfully'));
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
                "model"        => new Content(),
            ]);

        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
    }
}
