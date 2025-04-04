<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FileKey;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Requests\Admin\WithdrawRequest;
use App\Http\Services\SettingService;
use App\Models\Admin\Withdraw;
use App\Models\Core\File;
use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;
use App\Traits\ModelAction;
use App\Traits\Fileable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{

    use ModelAction,Fileable;

    private $currencies;
    public function __construct(){


        $this->middleware(['permissions:view_withdraw'])->only('list');
        $this->middleware(['permissions:create_withdraw'])->only('create');
        $this->middleware(['permissions:update_withdraw'])->only('edit', 'update','bulk','updateStatus','configuration');
        $this->middleware(['permissions:delete_withdraw'])->only('destroy','bulk');

    }

    public function list() :View{
        
        return view('admin.withdraw.list', [
            'title'        => 'Withdraw List',
            'breadcrumbs'  => ['Dashboard' => 'admin.home', 'Withdraw methods' => null],
            'withdraws'    => Withdraw::with(['file'])->search(['name'])
                                    ->latest()
                                    ->paginate(paginateNumber())
                                    ->appends(request()->all())
        ]);
    }

    public function create() :View{

        return view('admin.withdraw.create', [
            'title'         => 'Create Withdraw Method',
            'breadcrumbs'   =>  ['Dashboard' => 'admin.home', 'Withdraw methods' => 'admin.withdraw.list', 'Create' => null],
        ]);
    }


    
    public function configuration(Request $request) :RedirectResponse{

        $request->validate([
            "site_settings"                                           => ["required","array"],
            "site_settings.max_pending_withdraw"                      => ["required","integer",'gt:0' ,'max:100'],
        ]);

        (new SettingService())->updateSettings($request->site_settings);
        return back()->with(response_status('Updated successfully'));

    }



    public function store(WithdrawRequest $request) :RedirectResponse{

        DB::transaction(function() use ($request) {

            $withdraw                    = new Withdraw();
            $withdraw->name              = $request->input('name');
            $withdraw->duration          = $request->input('duration');
            $withdraw->minimum_amount    = $request->input('minimum_amount');
            $withdraw->maximum_amount    = $request->input('maximum_amount');
            $withdraw->fixed_charge      = $request->input('fixed_charge');
            $withdraw->percent_charge    = $request->input('percent_charge');
            $withdraw->note              = $request->input('note');
            $withdraw->save();

            if($request->hasFile('image')){
                $this->saveFile($withdraw ,$this->storeFile(
                    $request->file('image'), 
                    config("settings")['file_path']['withdraw_method']['path'])
                    ,FileKey::FEATURE->value);

            }


        });
     

        return  back()->with(response_status(trans('default.created_successfully')));

    }

    public function edit(int | string $uid) :View{


        return view('admin.withdraw.edit', [
            'title'         => 'Withdraw Update',
            'breadcrumbs'   => ['Dashboard' => 'admin.home', 'Withdraw methods' => 'admin.withdraw.list' , "Update" => null],
            'withdraw'      => Withdraw::with(['file'])->where('uid', $uid)->firstOrFail(),

        ]);
    }

   
    public function updateStatus(Request $request) :string{

        $request->validate([
            'id'      => 'required|exists:withdraws,uid',
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['status'])],
        ]);

        return $this->changeStatus($request->except("_token"),[
            "model"    => new Withdraw(),
        ]);
    }
    public function update(WithdrawRequest $request) :RedirectResponse{
       

        DB::transaction(function() use ($request) {

            $withdraw                    = Withdraw::with(['file'])->findOrfail($request->input('id'));
            $withdraw->name              = $request->input('name');
            $withdraw->duration          = $request->input('duration');
            $withdraw->minimum_amount    = $request->input('minimum_amount');
            $withdraw->maximum_amount    = $request->input('maximum_amount');
            $withdraw->fixed_charge      = $request->input('fixed_charge');
            $withdraw->percent_charge    = $request->input('percent_charge');
            $withdraw->note              = $request->input('note');
            $withdraw->setParameters();
            $withdraw->save();

            if($request->hasFile('image')){

                $oldFile = $withdraw->file()->where('type',FileKey::FEATURE->value)->first();
                $this->saveFile($withdraw ,$this->storeFile(
                    file        : $request->file('image'), 
                    location    : config("settings")['file_path']['withdraw_method']['path'],
                    removeFile  : $oldFile
                    )
                    ,FileKey::FEATURE->value);

            }


        });
     
        return back()->with(response_status('Updated Successfully'));
    }

    public function destroy(string $uid) :RedirectResponse{

        $withdraw = Withdraw::with(['file','log'])->withCount(['log'])
                                    ->where('uid', $uid)
                                    ->firstOrFail();

        $response =  response_status('Can not be deleted!! item has related data','error');

        if(1  > $withdraw->log_count){

            $oldFile = $withdraw->file()->where('type',FileKey::FEATURE->value)->first();
            $this->unlink(
                location    : config("settings")['file_path']['withdraw_method']['path'],
                file        : $oldFile
            );
            $withdraw->delete();
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
                "model"        => new Withdraw(),
            ]);
    
        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
        
    }

}







































