<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Admin\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

use App\Traits\ModelAction;
use Illuminate\View\View;

class RoleController extends Controller
{

    use ModelAction;


    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['permissions:view_role'])->only('list');
        $this->middleware(['permissions:create_role'])->only(['store','create']);
        $this->middleware(['permissions:update_role'])->only(['updateStatus','update','bulk']);
        $this->middleware(['permissions:delete_role'])->only(['destroy','bulk']);

    }


    /**
     * Role list
     *
     * @return View
     */
    public function list() :View{

        return view('admin.role.list',[
            'breadcrumbs' =>  ['Home'=>'admin.home','Roles'=> null],
            'title'       =>  'Manage Roles',
            'roles'       =>  Role::with(['createdBy', 'updatedBy'])
                ->search(['name','createdBy:username','updatedBy:username'])
                ->latest()
                ->paginate(paginateNumber())
                ->appends(request()->all())

        ]);
    }


    /**
     * Role Create View
     *
     * @return View
     */
    public function create() :View{

        return view('admin.role.create',[
            'breadcrumbs' =>  ['Home'=>'admin.home','Roles'=> "admin.role.list",'Create'=> null],
            'title'       => 'Create Role',
        ]);
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request) :RedirectResponse{
        $request->validate([
            'name'          => 'required|max:155|unique:roles,name',
            'permissions'   => 'required|array',
            'permissions.*' => 'required|array',
        ]);

        $permissions = array();

        foreach($request->input("permissions") as $key => $value) {
            $permissions[$key] = array_values($value);
        }

        Role::create([
            'name'         => $request->input("name"),
            'permissions'  => $permissions,
        ]);

        return  back()->with(response_status(trans('default.created_successfully')));
    }


    /**
     * @param int|string $uid
     * @return View
     */
    public function edit(int | string $uid): View
    {
        return view('admin.role.edit',[
            'title'        =>  'Update Role',
            'breadcrumbs'  =>  ['Home'=>'admin.home','Roles'=> "admin.role.list",'Update'=> null],
            'role'         =>  Role::where('uid',$uid)->firstOrFail()
        ]);
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request) :RedirectResponse{

        $request->validate([
            'id'            => 'required|exists:roles,id',
            'name'          => 'required|max:155|unique:roles,name,'.$request->input("id"),
            'permissions'   => 'required|array',
            'permissions.*' => 'required|array',
        ]);


        $permissions = array();
        foreach ($request->permissions as $key => $value) {
            $permissions[$key] = array_values($value);
        }

        $role              = Role::where('id',$request->input("id"))->firstOrfail();
        $role->name        = $request->input("name");
        $role->permissions = $permissions;
        $role->save();


        return  back()->with(response_status(trans('default.updated_successfully')));
    }


    /**
     * Updates the status of a specif role.
     *
     * @param Request $request
     * @return string
     */
    public function updateStatus(Request $request) :string{

        $request->validate([
            'id'      => 'required|exists:roles,uid',
            'status'  => ['required',Rule::in(StatusEnum::toArray())],
            'column'  => ['required',Rule::in(['status'])],
        ]);

        return $this->changeStatus($request->except("_token"),[
            "model"    => new Role(),
        ]);

    }


    /**
     * destroy a specific role
     *
     * @param int|string $uid
     * @return RedirectResponse
     */
    public function destroy(int|string $uid) :RedirectResponse{

        $role         =  Role::withCount('staff')->where('uid',$uid)->firstOrfail();
        $response     =  response_status('Cant not be deleted , Item has related data','error');
        if($role->staff_count <  1){
            $response =  response_status('Item deletd successfully');
            $role->delete();
        }
        return  back()->with($response);
    }




     /**
     * Bulk action
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulk(Request $request): RedirectResponse {
        try {
            $response =  $this->bulkAction($request,[
                "model"       => new Role(),
                "with_count"  => ['staff'],
            ]);

        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }

        return  back()->with($response);
    }

}


