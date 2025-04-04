<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Admin\Template;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use Illuminate\View\View;


class TemplateController extends Controller
{


    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['permissions:view_template'])->only('list','global');
        $this->middleware(['permissions:update_template'])->only(['edit','update','globalUpdate']);
    }


    /**
     * template list
     *
     * @return View
     */
    public function list() :View{

        return view('admin.template.list',[
            'breadcrumbs'   =>  ['Home'=>'admin.home','Templates'=> null],
            'title'         =>  'Manage Template',
            'templates'     =>  Template::search(['name','subject'])->with(['updatedBy'])
                                        ->latest()
                                        ->paginate(paginateNumber())
                                        ->appends(request()->all())
        ]);
    }




    /**
     * global template
     *
     * @return View
     */
    public function global() :View{

        return view('admin.template.global',[

            'breadcrumbs' =>  ['Home'=>'admin.home','Global Template'=> null],
            'title'       =>  'Manage Global Template',
        ]);
    }


    /**
     * update global template
     * @param Request $request
     * @return RedirectResponse
     */
    public function globalUpdate(Request $request) :RedirectResponse{

        $response = json_decode((new SettingController())->store($request),true);
        optimize_clear();
        return  back()->with(response_status($response['message'],$response['status'] ? "success" :"error"));
    }


    /**
     * update template
     *
     * @param int|string $uid
     * @return View
     */
    public function edit(int | string $uid) :View{


        return view('admin.template.edit',[
            'breadcrumbs'  =>  ['Home'=>'admin.home','Templates'=> "admin.template.list" ,"Update" => null],
            'title'        => 'Update Template',
            'template'     =>  Template::where('uid',$uid)->firstOrFail()
        ]);
    }

    /**
     * update template
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request) :RedirectResponse{

        $request->validate([
            'id'       => "required|exists:templates,id",
            'name'     => "required|max:155",
            'subject'  => "required|max:255",
            'sms_body' => "required",
            'body'     => "required",
        ]);

        $template             = Template::findOrfail($request->input("id"));
        $template->name       = $request->input('name');
        $template->sms_body   = $request->input('sms_body');
        $template->subject    = $request->input('subject');
        $template->body       = $request->input('body');
        $template->save();

        return  back()->with(response_status('Updated successfully'));
    }

}
