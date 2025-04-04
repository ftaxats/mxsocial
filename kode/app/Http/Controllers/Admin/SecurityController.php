<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Services\SettingService;
use App\Models\Country;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Traits\ModelAction;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Validation\Rule;
class SecurityController extends Controller
{

    use ModelAction;


    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['permissions:view_security'])->only(['countries','ipList']);
        $this->middleware(['permissions:update_security'])->only(['countryStatus','update','ipBulk','ipDestroy','countryBulk','ipStore','dosUpdate','dos','ipUpdate']);

    }


    public function dos(): View
    {
        return view('admin.security.dos',[

            'breadcrumbs'  => ['Home'=>'admin.home','Dos'=> null],
            'title'        => 'Dos Security',
        ]);
    }

    public function dosUpdate(Request $request) :RedirectResponse{

        $request->validate([
            "site_settings"                        => ['required',"array"],
            "site_settings.dos_attempts"           => ['required',"numeric","min:1","max:10000"],
            "site_settings.dos_attempts_in_second" => ['required',"numeric","min:1","max:10000"],
        ]);

        (new SettingService())->updateSettings($request->site_settings);
        return back()->with(response_status('Updated Successfully'));
    }


    /**
     * Countries list
     *
     * @return View
     */
    public function countries() :View{

        return view('admin.security.countries',[

            'breadcrumbs'  => ['Home'=>'admin.home','Countries'=> null],
            'title'        => 'Manage Country',
            'countries'    => Country::withCount(['ip'])->search(['name','code'])->filter(["is_blocked"])
                                                ->latest()
                                                ->paginate(paginateNumber())
                                                ->appends(request()->all())

        ]);
    }




    /**
     * Visitor ip list
     *
     * @return View
     */
    public function ipList(): View
    {


        return view('admin.security.ip_list',[

            'breadcrumbs'  => ['Home'=>'admin.home','Ip List'=> null],
            'title'        => 'Manage Ip',
            'countries'    => Country::get(),
            'ip_lists'     => Visitor::with('country')
                                ->search(['ip_address'])
                                ->filter(["is_blocked",'country_id','ip_address'])
                                ->latest()
                                ->when(request()->input('country_id'),function(Builder $q){
                                    return $q->where('country_id' ,request()->input('country_id'));
                                })
                                ->paginate(paginateNumber())
                                ->appends(request()->all())

        ]);
    }


    /**
     * Update a specific country status
     *
     * @param Request $request
     * @return string
     */
    public function countryStatus(Request $request): string
    {
        $request->validate([
            'id'      => 'required|exists:countries,id',
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['is_blocked'])],
        ]);

        return $this->changeStatus($request->except("_token"),[
            "model"    => new Country(),
            "find_by"  => "id"
        ]);
    }


    /**
     * Update a specific ip status
     *
     * @param Request $request
     * @return string
     */
    public function ipStatus(Request $request) :string{

        $request->validate([
            'id'      => ['required','exists:visitors,id'],
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['is_blocked'])],
        ]);

        return $this->changeStatus($request->except("_token"),[
            "model"    => new Visitor(),
            "find_by"  => "id"
        ]);
    }


    public function ipStore(Request $request) :RedirectResponse{

        $request->validate([
            'ip_address' => ['required','ip',"max:155"],
            'country_id' => ['required' ,'exists:countries,id'],
        ]);

        Visitor::create([
            "ip_address" => $request->input('ip_address'),
            "country_id" => $request->input('country_id'),
        ]);

        return  back()->with(response_status('Ip created successfully'));

    }


    public function ipUpdate(Request $request) :RedirectResponse{

        $request->validate([
            'id'          => ['required','exists:visitors,id'],
            'country_id'  => ['required' ,'exists:countries,id'],
        ]);

        Visitor::where('id',$request->input("id"))->update([
            "country_id" => $request->input('country_id'),
        ]);

        return  back()->with(response_status('Updated successfully'));

    }



    public function ipDestroy(string | int $id) :RedirectResponse{
        Visitor::where('id',$id)->delete();
        return  back()->with(response_status('Item deleted succesfully'));
    }


    /**
     * Bulk action
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function ipBulk(Request $request) :RedirectResponse {

        try {
            $response =  $this->bulkAction($request,[
                "model"        => new Visitor(),
            ]);

        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
    }


    /**
     * country bulk action
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function countryBulk(Request $request) :RedirectResponse {

        try {
            $response =  $this->bulkAction($request,[
                "model"        => new Country(),
            ]);

        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
    }

}
