<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Utility\SendMail;
use App\Jobs\SendMailJob;
use App\Models\Contact;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use App\Traits\ModelAction;

class CommunicationsController extends Controller
{


    use ModelAction;

    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['permissions:view_frontend'])->only('subscribers','contacts');
        $this->middleware(['permissions:update_frontend'])->only(['destroySubscriber','destroy']);
    }


    /**
     * subscriber list
     *
     * @return View
     */
    public function subscribers() :View{

        return view('admin.communication.subscribers',[

            'breadcrumbs' =>  ['Home'=>'admin.home','Subscribers'=> null],
            'title'       =>  translate("Manage Subscribers"),
            'subscribers' =>  Subscriber::filter(['email'])
                                ->latest()
                                ->paginate(paginateNumber())->appends(request()->all())
        ]);
    }


    /**
     * destroy a specific subscriber
     *
     * @param string $uid
     * @return RedirectResponse
     */
    public function destroySubscriber(string $uid) :RedirectResponse{

        $subscriber  = Subscriber::where('uid',$uid)->firstOrfail();
        $subscriber->delete();
        return  back()->with(response_status('Subscriber Deleted'));
    }




    /**
     * contact list
     *
     * @return View
     */
    public function contacts() :View{

        return view('admin.communication.contacts',[
            'breadcrumbs' => ['Home'=>'admin.home','Contacts'=> null],
            'title'       => translate('Manage Contacts'),
            'contacts'    => Contact::search(['name','email','phone'])->latest()->paginate(paginateNumber())->appends(request()->all())
        ]);
    }


    /**
     * destroy a specific contact
     *
     * @param string $uid
     * @return RedirectResponse
     */
    public function destroy(string $uid) :RedirectResponse{

        $contact  = Contact::where('uid',$uid)->firstOrfail();
        $contact->delete();
        return  back()->with(response_status('Contact Deleted'));
    }





    /**
     * send mail
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function sendMail(Request $request) :RedirectResponse{

        $request->validate([
            'message'=>'required|string',
            'email'=>'email|required',
        ],[
            'message.required' => translate('Message Is Required'),
            'email.required' => translate('Email Required'),
        ]);

        $templateCode =[
            'name'     =>  $request->email,
            'email'    => $request->email,
            "message"  => $request->message
        ];

        $response = SendMail::mailNotifications('CONTACT_REPLY',$templateCode ,(object) $templateCode);

        return back()->with(response_status(Arr::get($response,'message',""),$response['status'] ? "success" :"error"));
    }


    /**
     * send mail to all subscribers
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function sendMailSubscriber(Request $request) :RedirectResponse{

        $subscribers = Subscriber::latest()->cursor();
        if($subscribers->count() > 0){

            foreach($subscribers->chunk(20) as $chunkSubscribers){

                foreach($chunkSubscribers as $subscriber){
                    $templateCode = [

                        'name'     =>  $subscriber->email,
                        'email'    => $subscriber->email,
                        "message"  => $request->input('message')
                    ];

                    SendMailJob::dispatch((object) $templateCode,'CONTACT_REPLY',$templateCode);
                }
            }

            return back()->with(response_status("Email Successfully Sent to Subscribers","success" ));

        }

        return back()->with(response_status("No subscribers found","error" ));



     }



    /**
     * Bulk action
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulkSubscriberDestory(Request $request) :RedirectResponse {

        try {
            $response =  $this->bulkAction($request,[
                "model"        => new Subscriber(),
            ]);

        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
    }



    /**
     * Bulk action
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulkContactDestroey(Request $request) :RedirectResponse {

        try {
            $response =  $this->bulkAction($request,[
                "model"        => new Contact(),
            ]);

        } catch (\Exception $exception) {
            $response  = \response_status($exception->getMessage(),'error');
        }
        return  back()->with($response);
    }
}
