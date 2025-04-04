<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PackageRequest;
use App\Http\Services\SettingService;
use App\Models\AiTemplate;
use App\Models\MediaPlatform;
use App\Models\Package;
use App\Traits\ModelAction;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PackageController extends Controller
{
    use ModelAction;

    /**
     *
     * @return void
     */
    public function __construct(){
        $this->middleware(['permissions:view_package'])->only(['list','selectSearch']);
        $this->middleware(['permissions:create_package'])->only(['store','create']);
        $this->middleware(['permissions:update_package'])->only(['updateStatus','update','edit','bulk','configuration']);
        $this->middleware(['permissions:delete_package'])->only(['destroy']);
    }


    /**
     * Packages list
     *
     * @return View
     */
    public function list() :View{

        return view('admin.subscription_package.list',[

            'breadcrumbs'  => ['Home'=>'admin.home','Packages'=> null],
            'title'        => 'Subscription Packages',
            'packages'     => Package::search(['title'])->with(['updatedBy','createdBy'])
                                        ->withCount(["subscriptions"])
                                        ->latest()
                                        ->get()

        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function selectSearch(Request $request): \Illuminate\Http\JsonResponse {

        $searchData   = $request->input("term");

        $users        =  AiTemplate::default()->active()->where(function ($query) use($searchData) {
                                $query->where('name','LIKE',  '%' . $searchData. '%')
                                ->orWhere('slug','LIKE',  '%' . $searchData. '%');
                            })->select('id as id','name as text')
                            ->latest()
                            ->simplePaginate(paginateNumber());
        $pages = true;
        if (empty($users->nextPageUrl())) $pages = false;
        $results = array(
          "results"    => $users->items(),
          "pagination" => array(
            "more"     => $pages
          )
        );

        return response()->json($results);
    }


    /**
     * @return View
     */
    public function create() :View{


        return view('admin.subscription_package.create',[

            'breadcrumbs'  => ['Home'=>'admin.home','Packages'=> 'admin.subscription.package.list',"Create"=>null],
            'title'        => 'Create Package',
            "platforms"    => MediaPlatform::active()->integrated()->get(),

        ]);

    }


    /**
     * package configuration
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function configuration(Request $request): RedirectResponse
    {
        $request->validate([
            "site_settings"                                   => ["required","array"],
            "site_settings.signup_bonus"                      => ["required","exists:packages,id"],
            "site_settings.auto_subscription_package"         => ["required","exists:packages,id"],

        ]);

        (new SettingService())->updateSettings($request->site_settings);
        return  back()->with(response_status(trans('default.created_successfully')));
    }


    /**
     * store a  new package
     *
     * @param PackageRequest $request
     * @return RedirectResponse
     */
    public function store(PackageRequest $request) :RedirectResponse{


        $package                           =  new Package();
        $package->title                    =  $request->input("title");
        $package->icon                     =  $request->input("icon");
        $package->duration                 =  $request->input("duration");
        $package->description              =  $request->input("description");
        $package->price                    =  $request->input("price");
        $package->discount_price           =  $request->input("discount_price")?? 0;
        $package->affiliate_commission     =  $request->input("affiliate_commission") ?? 0;
        $package->social_access            =  $request->input("social_access");
        $package->ai_configuration         =  $request->input("ai_configuration");
        $package->template_access          =  $request->input("template_access");
        $package->save();

        return  back()->with(response_status(trans('default.created_successfully')));
    }




    /**
     * edit a  new package
     *
     */
    public function edit(string $uid): View
    {
        $package = Package::where('uid',$uid)->firstOrFail();
        return view('admin.subscription_package.edit',[

            'breadcrumbs'   =>  ['Home'=>'admin.home','Packages'=> 'admin.subscription.package.list',"Edit"=>null],
            'title'         => 'Update Package',
            'package'       =>  $package,
            "platforms"     =>  MediaPlatform::active()->integrated()->get(),
            "templates"     =>  AiTemplate::whereIn('id',(array)$package->template_access)->default()->active()->pluck('name','id')->lazy()->toArray(),
        ]);

    }

    /**
     * Update a specific package
     *
     * @param PackageRequest $request
     * @return RedirectResponse
     */
    public function update(PackageRequest $request) :RedirectResponse {

        $package                           =  Package::where("id",$request->input('id'))->firstOrfail();

        $package->duration                 =  $request->input("duration");
        $package->title                    =  $request->input("title");
        $package->icon                     =  $request->input("icon");
        $package->description              =  $request->input("description");
        $package->price                    =  $request->input("price");
        $package->discount_price           =  $request->input("discount_price")?? 0;
        $package->affiliate_commission     =  $request->input("affiliate_commission") ?? 0;
        $package->social_access            =  $request->input("social_access");
        $package->ai_configuration         =  $request->input("ai_configuration");

        $package->template_access          =  $request->input("template_access");
        $package->save();

        return back()->with(response_status('Package updated successfully'));
    }

    /**
     * Update a specific  Package Status
     *
     * @param Request $request
     * @return string
     */
    public function updateStatus(Request $request) :string{


        $request->validate([
            'id'      => 'required|exists:packages,uid',
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['status','is_recommended','is_feature'])],
        ]);

        $response['reload']   = true;
        $response['status']   = true;
        $response['message']  = translate(trans('default.updated_successfully'));

        $package = Package::where('uid',$request->input("id"))->update([
            $request->input("column") => $request->input("status") ,
        ]);

        if($request->input("column") == 'is_recommended' && $request->input("status") == StatusEnum::true->status()){
            Package::where('uid', '!=',$request->input("id"))->update([
                "is_recommended" => StatusEnum::false->status()
            ]);
        }

        if(!$package){
            $response['status']   = false;
            $response['message']  = translate(trans('default.failed_to_update'));
        }

        return json_encode($response);
    }


    /**
     * destroy a specific package
     *
     * @param int|string $id
     * @return RedirectResponse
     */
    public function destroy(int|string $id) :RedirectResponse{

        $package   = Package::withCount(['subscriptions'])->where('is_free',StatusEnum::false->status())->findOrfail($id);
        $response  =  response_status('Can not be deleted!! item has related data','error');

        if(1  > $package->subscriptions_count){
            $package->delete();
            $response =  response_status('Item deleted successfully');
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
                "model"        => new Package(),
            ]);

        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);

    }
}
