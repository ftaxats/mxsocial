<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FileKey;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest as BlogRequest;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Traits\ModelAction;
use App\Traits\Fileable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;

class BlogController extends Controller
{


    use ModelAction , Fileable;
    protected Collection $categories;
    /**
     *
     * @return void
     */
     public function __construct(){

        //check permissions middleware
        $this->middleware(['permissions:view_blog'])->only(['list']);
        $this->middleware(['permissions:create_blog'])->only(['store','create']);
        $this->middleware(['permissions:update_blog'])->only(['updateStatus','update','edit','bulk']);
        $this->middleware(['permissions:delete_blog'])->only(['destroy','bulk']);
    }


    /**
     * Blog list
     *
     * @return View
     */
    public function list() :View{

        return view('admin.blog.list',[

            'breadcrumbs'  => ['Home'=>'admin.home','Blogs'=> null],
            'title'        => 'Manage Blogs',
            'articles'     => Blog::search(['title'])
                ->filter(["status",'category:slug','is_feature'])
                ->latest()
                ->paginate(paginateNumber())
                ->appends(request()->all()),

        ]);
    }


    /**
     * @return View
     */
    public function create() :View{

        return view('admin.blog.create',[
            'breadcrumbs' =>  ['Home'=>'admin.home','Blogs'=> 'admin.blog.list',"Create"=>null],
            'title'       => 'Create Blog'
        ]);

    }


    /**
     * store a  new Blog
     *
     * @param BlogRequest $request
     * @return RedirectResponse
     */
    public function store(BlogRequest $request) :RedirectResponse{

        DB::transaction(function() use ($request) {

            $blog         =  new Blog();
            $blog->title           =  $request->input("title");
            $blog->description     =  $request->input("description");
            $blog->save();
            if($request->hasFile('image')){
                $this->saveFile($blog ,$this->storeFile(
                    file        : $request->file('image'),
                    location    : config("settings")['file_path']['blog']['path'],
                    )
                    ,FileKey::FEATURE->value);
            }
        });

        return  back()->with(response_status('Blog created successfully'));
    }





    /**
     * edit a  new Blog
     *
     */
    public function edit(string $uid) :View{

        return view('admin.blog.edit',[
            'breadcrumbs' => ['Home'=>'admin.home','Blogs'=> 'admin.blog.list',"Edit"=>null],
            'title'       => 'Update Blog',
            'article'     => Blog::where('uid',$uid)->firstOrfail()
        ]);

    }



    /**
     * Update a specific Blog
     *
     * @param BlogRequest $request
     * @return RedirectResponse
     */
    public function update(BlogRequest $request) :RedirectResponse {
        DB::transaction(function() use ($request) {

            $blog                  =  Blog::where('id',$request->input('id'))->firstOrfail();
            $blog->title           =  $request->input("title");
            $blog->description     =  $request->input("description");
            $blog->save();
            if($request->hasFile('image')){
                $oldFile = $blog->file()->where('type',FileKey::FEATURE->value)->first();
                $this->saveFile($blog ,$this->storeFile(
                    file        : $request->file('image'),
                    location    : config("settings")['file_path']['blog']['path'],
                    removeFile  : $oldFile)
                    ,FileKey::FEATURE->value);

            }
        });

        return  back()->with(response_status('Blog Updated Successfully'));
    }

    /**
     * Update a specific Blog status
     *
     * @param Request $request
     * @return string
     */
    public function updateStatus(Request $request) :string{

        $request->validate([
            'id'      => 'required|exists:blogs,uid',
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['status','is_feature'])],
        ]);

        return $this->changeStatus($request->except("_token"),[
            "model"    => new Blog(),
        ]);
    }


    public function destroy(string | int $uid) :RedirectResponse{

        $blog  = Blog::where('uid',$uid)->firstOrfail();
        $this->unlink(
            location    : config("settings")['file_path']['blog']['path'],
            file        : $blog->file()->where('type',FileKey::FEATURE->value)->first()
        );
        $blog->delete();
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
                "model"        => new Blog(),
                "file_unlink"  => [
                    "feature"   =>  config("settings")['file_path']['blog']['path']
                ],
            ]);

        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
    }
}
