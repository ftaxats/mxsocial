<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FrontendSectionRequest;
use App\Http\Services\FrontendService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\Admin\Frontend;
use App\Models\Core\File;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Traits\Fileable;
use Illuminate\Validation\Rule;
use App\Traits\ModelAction;
use Illuminate\Support\Facades\DB;

class FrontendManageController extends Controller
{

    use Fileable ,ModelAction;
    private  $frontEndService;

    /**
     *
     * @return void
     */
    public function __construct()
    {

        $this->frontEndService = new FrontendService();
        $this->middleware(['permissions:view_frontend'])->only('list','children');
        $this->middleware(['permissions:update_frontend'])->only(['update','updateStatus','bulk','destroy']);
    }







    /**
     * Get appearance list
     *
     * @param  string  $key | string  $children ,
     */
    public function list(string $key , ? int $parent = null) :View{


        $appearance = @get_appearance()->{$key};

        if (!$appearance)   abort(404);

        $title = ucFirst(str_replace("_"," ",$appearance->name));

        if($parent){
            $parentSection = Frontend::with(['file'])
                                ->whereNull('parent_id')
                                ->where('id',$parent )->firstOrfail();


          $title =  ($parentSection->value->title ?? ucFirst(str_replace("_"," ",$appearance->name))) ." Details" ;
        }


        $frontend          =  Frontend::with(['file'])
                                       ->when($parent , fn(Builder $q) :Builder =>  $q->where('parent_id',$parent))
                                       ->where('key','content_'.$key )
                                       ->first();

        $frontendElements  =  Frontend::with(['file'])
                                      ->when($parent , fn(Builder $q) :Builder =>  $q->where('parent_id',$parent))
                                      ->where('key', 'element_'.$key)
                                      ->latest()->get();



        return view('admin.frontend.list',[
            'breadcrumbs'            =>  ['Home'=>'admin.home','Frontends'=> null],
            'title'                  =>  $title,
            "appearance"             =>  $appearance,
            'appearance_content'     =>  $frontend,
            'appearance_elements'    =>  $frontendElements,
            'parent_section'          => @$parentSection,
        ]);

    }


    /**
     * @param FrontendSectionRequest $request
     * @return RedirectResponse
     */
     public function update(FrontendSectionRequest $request) :RedirectResponse{
        return back()->with( $this->frontEndService->save($request));
     }



    /**
     * Update status
     *
     * @param Request $request
     * @return string
     */
    public function updateStatus(Request $request) :string{

        $request->validate([
            'id'      => 'required|exists:frontends,uid',
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['status'])],
        ]);

        return $this->changeStatus($request->except("_token"),["model"    => new Frontend()]);

    }


    /**
     * @param string $id
     * @return RedirectResponse
     */
    public function destroy(string $id) :RedirectResponse{


        DB::transaction(function() use ($id) {
            $frontend  =  Frontend::with('file')
                ->withCount('file')
                ->where('id',$id)
                ->firstOrFail();

            if(0 < $frontend->file_count){
                $frontend->file->map(fn(File $file):bool =>
                     $this->unlink(
                        location    : config("settings")['file_path']['frontend']['path'],
                        file        :  $file
                    ));

            }

            $frontend->childrens->map(function(Frontend $children): void{
                    $children->file->map(fn(File $file):bool =>
                        $this->unlink(
                        location    : config("settings")['file_path']['frontend']['path'],
                        file        :  $file
                    ));

                    $children->delete();
           });

            $frontend->delete();
        });
        return  back()->with(response_status('Deleted successfully'));
    }


    public function bulk(Request $request) :RedirectResponse {

        try {
            $response =  $this->bulkAction($request,[
                "model"        => new Frontend(),
            ]);

        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);

    }
}
