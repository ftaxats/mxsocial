<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Enums\PlanDuration;
use App\Enums\StatusEnum;
use App\Http\Requests\SocialPostRequest;
use App\Models\Admin\Category;
use App\Models\AiTemplate;
use App\Models\Content;
use App\Models\MediaPlatform;
use App\Models\Package;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Traits\ModelAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Traits\AccountManager;
use App\Traits\PostManager;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;


class SocialPostController extends Controller
{

    use ModelAction , AccountManager ,PostManager;
    protected  $user ,$subscription , $accessPlatforms ,$remainingPost ,$templates;


    public function __construct(){

        $this->middleware(function ($request, $next) {

            $this->user                   = auth_user('web');
            $this->subscription           = $this->user->runningSubscription;
            $this->accessPlatforms        = (array) ($this->subscription ? @$this->subscription->package->social_access->platform_access : []);
            $this->remainingPost          = (int) ($this->subscription ? @$this->subscription->remaining_post_balance : 0);

            $templateAccess               = $this->subscription ? (array)subscription_value($this->subscription,"template_access",true) :[];
            $this->templates              = AiTemplate::whereIn('id',$templateAccess)->get();
            return $next($request);
        });
    }






    /**
     * Social post list
     *
     * @return View
     */
    public function list() :View{

        return view('user.social.post.list',[
            'meta_data'       => $this->metaData(['title'=> translate("Social Post List")]),
            'posts'           => SocialPost::with(['user','account','account.platform','account.platform.file'])
                                        ->where("user_id",$this->user->id)
                                        ->filter(["status",'account:account_id'])
                                        ->date()
                                        ->latest()
                                        ->paginate(paginateNumber())
                                        ->appends(request()->all()),

            'accounts'        =>  SocialAccount::where("user_id",$this->user->id)->get()

        ]);

    }





    /**
     * Create a new post
     *
     * @return View
     */
    public function create(): View
    {
                $platforms = MediaPlatform::with(['file' ,'accounts' => fn($q):HasMany =>
                         $q->where('user_id',$this->user->id)
                                    ->where('subscription_id', @$this->subscription?->id)
                                    ->active()
                    ])
                    ->whereIn('id',(array)$this->accessPlatforms)
                    ->integrated()
                    ->active()
                    ->get();



        $accounts = $platforms->flatMap(fn(MediaPlatform $platform) : Collection =>  $platform->accounts);
        $accessCategories = (array)@$this->templates->pluck('category_id')->unique()->toArray();
        return view('user.social.post.create',[

            'meta_data'       => $this->metaData(['title'=> translate("Create Post")]),
            'accounts'        => $accounts,
            'platforms'       => $platforms,
            'contents'        => Content::where("user_id",$this->user->id)->get(),
            'categories'      => Category::template()
                                       ->doesntHave('parent')
                                       ->whereIn('id',$accessCategories)
                                       ->get(),
            'templates'  =>     $this->templates


        ]);
    }


    /**
     * store a new post
     * @param SocialPostRequest $request
     * @return RedirectResponse
     */
    public function store(SocialPostRequest $request) :RedirectResponse{


        $status   = false ;
        $message  = translate("Unable to create a new post: Insufficient subscription balance. Please subscribe a new plan to proceed with the post creation process. Thank you");
        $schedule = false;

        if($this->subscription->package){
            $package = $this->subscription->package;
            if(@$package->social_access->schedule_post == StatusEnum::true->status()) $schedule = true;
        }
        if($request->input("schedule_date") && !$schedule ) $request->merge(['schedule_date' => null]);

        if($this->checkRemainingPost()){
            $status   = true ;
            $response = $this->savePost( request : $request->except(['_token']) ,user  : $this->user);
        }
        return back()->with( $status?'success':'error',$status ?Arr::get($response,'message') : $message);
    }


    public function checkRemainingPost() :bool{
        return ($this->remainingPost == PlanDuration::value('UNLIMITED') ||
                    $this->remainingPost > 0 ) ?  true : false  ;
    }


    /**
     * show a new post
     *
     * @param string $uid
     *
     * @return View
     */
    public function show(string $uid) :View
    {
        $post  = SocialPost::with(['file','user','admin','account','account.platform','account.platform.file'])
                                        ->where("uid",$uid)
                                        ->where('user_id',$this->user->id)
                                        ->firstOrfail();
        return view('user.social.post.show',[
            'meta_data'       => $this->metaData(['title'=> translate("Show Post")]),
            'post'            => $post,

        ]);
    }


    public function destroy(string $id) :RedirectResponse {
        $post  = SocialPost::with(['file','user','account','account.platform','account.platform.file'])
                                                ->where('user_id',$this->user->id)
                                                ->where("id",$id)
                                                ->firstOrfail();
        foreach($post->file as $file){
            $this->unlink(
                location    : config("settings")['file_path']['post']['path'],
                file        : $file
            );
        }
        $post->delete();
        $response =  response_status('Item deleted successfully');
        return  back()->with($response);
    }


}
