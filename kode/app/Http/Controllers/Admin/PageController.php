<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Admin\Page;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Traits\ModelAction;

class PageController extends Controller
{

    use ModelAction;
    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['permissions:view_page'])->only(['list']);
        $this->middleware(['permissions:create_page'])->only(['store','create']);
        $this->middleware(['permissions:update_page'])->only(['updateStatus','update','edit','bulk']);
        $this->middleware(['permissions:delete_page'])->only(['destroy','bulk']);
    }


    /**
     * page list
     *
     * @return View
     */
    public function list () :View{

        return view('admin.page.list',[

            'breadcrumbs'  =>  ['Home'=>'admin.home','Pages'=> null],
            'title'        =>  'Manage Page',
            'pages'        =>  Page::search(['title'])->with(['createdBy'])
                                 ->latest()
                                 ->paginate(paginateNumber())
                                 ->appends(request()->all()),

        ]);
    }


    /**
     * @return View
     */
    public function create(): View
    {


        $page = Page::orderBy('serial_id','desc')->first();
        $serialId = $page ? $page->serial_id + 1 : 1;

        return view('admin.page.create',[
            'breadcrumbs'  => ['Home'=>'admin.home','Pages'=> 'admin.page.list',"Create" => null],
            'title'        => 'Create page',
            'serialId'     => $serialId
        ]);

    }



    /**
     * store a  new page
     *
     * @param PageRequest $request
     * @return RedirectResponse
     */
    public function store(PageRequest $request) :RedirectResponse{

        $page                      = new Page();
        $page->serial_id           = $request->input("serial_id");
        $page->title               = $request->input("title");
        $page->description         = $request->input("description");
        $page->save();

        return  back()->with(response_status('Page created successfully'));
    }



    /**
     * edit a page
     *
     */
    public function edit(string $uid) :View {

        return view('admin.page.edit',[
            'breadcrumbs' => ['Home'=>'admin.home','Page'=> 'admin.page.list',"Edit" => null],
            'title'       => 'Edit page',
            'page'        => Page::where("uid",$uid)->firstOrFail(),
        ]);

    }


    /**
     * Update a specific page
     *
     * @param PageRequest $request
     * @return RedirectResponse
     */
    public function update(PageRequest $request) :RedirectResponse{

        $page                      = Page::findOrfail($request->input('id'));
        $page->serial_id           = $request->input("serial_id");
        $page->title               = $request->input("title");
        $page->description         = $request->input("description");
        $page->save();

        return  back()->with(response_status('Page updated successfully'));
    }

    /**
     * Update a specific page status
     *
     * @param Request $request
     * @return string
     */
    public function updateStatus(Request $request) :string{

        $request->validate([
            'id'      => 'required|exists:pages,uid',
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['status','show_in_footer','show_in_header'])],
        ]);

        return $this->changeStatus($request->except("_token"),[
            "model"    => new Page(),
        ]);
    }


    /**
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id) :RedirectResponse{

        Page::where('id',$id)->delete();
        return  back()->with(response_status('Page Deleted'));
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
                "model"        => new Page(),
            ]);

        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
    }

}
