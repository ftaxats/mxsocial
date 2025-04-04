<?php

namespace App\Http\Controllers\Admin;

use App\Enums\FileKey;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaffRequest;
use App\Models\Admin as Staff;
use App\Models\Admin\Role;
use App\Models\Core\File;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use App\Traits\ModelAction;
use App\Traits\Fileable;

class StaffController extends Controller
{
    use Fileable ,ModelAction;
    private $staffService;

    /**
     *
     * @return void
     */
    public function __construct()
    {

        $this->middleware(['permissions:view_staff'])->only('list',);
        $this->middleware(['permissions:create_staff'])->only(['store','create']);
        $this->middleware(['permissions:update_staff'])->only(['updateStatus','update','updatePassword','login','bulk']);
        $this->middleware(['permissions:delete_staff'])->only(['destroy','restore','permanentDestroy','bulk']);
    }


    /**
     * Staff list
     *
     * @return View
     */
    public function list() :View{

        return view('admin.staff.list',[
            'roles'        =>  Role::active()->latest()->pluck('id','name')->prepend(
                "",
                translate('Select Role')
            ),
            'breadcrumbs'  =>  ['Home'=>'admin.home','Staffs'=> null],
            'title'        => 'Manage'.(request()->routeIs('admin.*.recycle.list') ? " recycled":"")." staff",
            'staffs'       =>  Staff::with(['file', 'createdBy'])
                                            ->recycle()
                                            ->search(['username','name','email','phone','role:name'])
                                            ->staff()->latest()->paginate(paginateNumber())->appends(request()->all())
        ]);
    }


    /**
     * store a  new staff
     *
     * @param StaffRequest $request
     * @return RedirectResponse
     */
    public function store(StaffRequest $request) :RedirectResponse{


        DB::transaction(function() use ($request) {

            $staff = Staff::create([
                'name'         => $request->input("name"),
                'username'     => $request->input("username"),
                'role_id'      => $request->input("role_id"),
                'phone'        => $request->input("phone"),
                'email'        => $request->input("email"),
                'password'     => Hash::make($request->input("password")),
            ]);

            if($request->hasFile('image')){

                $this->saveFile($staff ,$this->storeFile(
                    $request->file('image'), 
                    config("settings")['file_path']['profile']['admin']['path'])
                    ,FileKey::AVATAR->value);
            }
        });

        return  back()->with(response_status('Staff created successfully'));
    }




    /**
     * Update a specific staff
     *
     * @param StaffRequest $request
     * @return RedirectResponse
     */
    public function update(StaffRequest $request) :RedirectResponse{

        DB::transaction(function() use ($request) {

            $staff = Staff::with('file')->staff()->where('id',$request->input('id'))->firstOrfail();
            $staff->username      =  $request->input("username");
            $staff->name          =  $request->input("name");
            $staff->role_id       =  $request->input("role_id");
            $staff->phone         =  $request->input("phone");
            $staff->email         =  $request->input("email");
            $staff->update();

            if($request->hasFile('image')){
                
                $oldFile = $staff->file()->where('type',FileKey::AVATAR->value)->first();
               
                $this->saveFile($staff ,$this->storeFile(
                        file        : $request->file('image'), 
                        location    : config("settings")['file_path']['profile']['admin']['path'],
                        removeFile  : $oldFile
                    )
                    ,FileKey::AVATAR->value);
            }
        });

        return  back()->with(response_status('Staff updated successfully'));
    }

    /**
     * Update a specific staff status
     *
     * @param Request $request
     * @return string
     */
    public function updateStatus(Request $request) :string{


        $request->validate([
            'id'      => 'required|exists:admins,uid',
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['status'])],
        ]);

        return $this->changeStatus($request->except("_token"),[
            "model"    => new Admin(),
            'recycle'  => true,
        ]);


    }


    /**
     * destroy a specific staff
     *
     * @param string $uid
     * @return RedirectResponse
     */
    public function destroy(string $uid) :RedirectResponse {

        $staff  = Staff::staff()->where('uid',$uid)->firstOrfail();
        $staff->delete();
        return  back()->with(response_status('Item successfully moved to the recycle bin'));
    }


    /**
     * restore a specific staff
     *
     * @param string $uid
     * @return RedirectResponse
     */
    public function restore(string $uid) :RedirectResponse {

        $staff  = Staff::staff()->where('uid',$uid)
                  ->onlyTrashed()
                  ->firstOrfail();
        $staff->restore();
        return  back()->with(response_status('Item Restored successfully'));
    }


    /**
     * destroy a specific staff
     *
     * @param string $uid
     * @return RedirectResponse
     */
    public function permanentDestroy(string $uid) :RedirectResponse {

        $staff  = Staff::with(['file','notifications','otp'])->staff()->where('uid',$uid)
                                                                ->onlyTrashed()
                                                                ->firstOrfail();
        $staff->otp()->delete();
        $staff->notifications()->delete();
        $oldFile = $staff->file()->where('type','avatar')->first();
        $this->unlink(
            location    : config("settings")['file_path']['profile']['admin']['path'],
            file        : $oldFile
        );
        $staff->forceDelete();
        return  back()->with(response_status('Item permanently deleted'));
    }



    /**
     * login in as staff
     *
     * @param string $uid
     * @return RedirectResponse
     */
    public function login(string $uid) :RedirectResponse{

        $staff          = Staff::staff()->where('uid',$uid)->firstOrFail();
        $staff->status  = StatusEnum::true->status();
        $staff->save();

        Auth::guard('admin')->loginUsingId($staff->id);
        return redirect()->route('admin.home')->with(response_status('Successfully logged in as a staff'));
    }


    /**
     * update password
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updatePassword(Request $request) :RedirectResponse{

        $request->validate([
            'uid'       => 'required|exists:admins,uid',
            'password'  => "required|confirmed|min:5"
        ]);

        Staff::staff()->where('uid',$request->input("uid"))->update([
            'password'=> Hash::make($request->input("password"))
        ]);

        return  back()->with(response_status('Password updated'));

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
                "model"        => new Admin(),
                'recycle'      => true,
                "with"         => ['file','notifications','otp'],
                "file_unlink"  => [
                    "avatar"   =>  config("settings")['file_path']['profile']['admin']['path']
                ],
                "force_flag"  => true
            ]);
    
        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
    }

}
