<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketRequest;
use App\Http\Services\TicketService;
use App\Http\Utility\SendNotification;
use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;
use App\Models\Core\File;
use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Traits\ModelAction;
use App\Traits\Fileable;
use Illuminate\Support\Arr;
use App\Traits\Notifyable;
class TicketController extends Controller
{

    use ModelAction ,Fileable ,Notifyable;
    protected $ticketService ;

    public function __construct(){

        $this->ticketService = new TicketService();

        $this->middleware(['permissions:view_ticket'])->only('list','show','reply','download','update','create','store');
        $this->middleware(['permissions:delete_ticket'])->only('destroyFile','destroy',"destroyMessage");
    }

    /**
     * Support Ticket View
     *
     * @return View
     */
    public function list() :View{


        return view('admin.ticket.list',[

            "title"           => translate("Ticket List"),
            'breadcrumbs'     => ['Home'=>'admin.home','Tickets'=> null],
            'tickets'         => Ticket::with(['user','messages'])
                                            ->search(['subject'])
                                            ->filter(['ticket_number',"status",'user:username','priority'])
                                            ->date()
                                            ->latest()
                                            ->paginate(paginateNumber())
                                            ->appends(request()->all()),

            'counter'         => $this->counter(),


        ]);
    }




    /**
     * count ticket data
     */

     public function counter() :array{

        $counter = array();
        $counter['pending'] = Ticket::pending()->count();
        $counter['solved']  = Ticket::solved()->count();
        $counter['closed']  = Ticket::closed()->count();
        $counter['hold']    = Ticket::hold()->count();

        return $counter;

     }




    /**
     * Create a new ticket
     *
     * @return View
     */
    public function create() :View{


        return view('admin.ticket.create',[

            "title"           => translate("Ticket Create"),
            'breadcrumbs'     => ['Home'=>'admin.home','Tickets'=> "admin.ticket.list","Create" => null],


        ]);
    }


    /**
     * store a new ticket
     * @param TicketRequest $request
     * @return RedirectResponse
     */
    public function store(TicketRequest $request) :RedirectResponse{

        $response = response_status("Ticket created successfully");

        try {
            $ticket =  $this->ticketService->store($request->except('_token') ,$request->input("user_id"));
        } catch (\Exception $ex) {
            $response = response_status(strip_tags($ex->getMessage()),'error');
        }
        return back()->with($response);
    }


    /**
     * Support Ticket View
     * @param string $ticketNumber
     * @return View
     */
    public function show(string $ticketNumber) :View{

        return view('admin.ticket.show',[

            "title"        => translate("Ticket Details"),
            'breadcrumbs'  => ['Home'=>'admin.home','Tickets'=> "admin.ticket.list" ,"Reply" => null],
            'ticket'       => Ticket::with(['user',"user.file",'messages','messages.admin' ,'messages.admin.file'])
                                                    ->where("ticket_number",$ticketNumber)
                                                    ->latest()
                                                    ->firstOrFail()
        ]);
    }


    /**
     * Reply Ticket
     * @param Request $request
     * @return RedirectResponse
     */
    public function reply(Request $request) :RedirectResponse{

        $request->validate([
            'id'      => "required|exists:tickets,id",
            "message" => 'required'
        ]);

        $ticket              = Ticket::with(['user'])
                                    ->where('id',$request->input('id'))
                                    ->firstOrFail();

        $message             = $this->ticketService->reply($ticket,$request ,auth_user()->id);

        if($message){

            $code = [
                "link"          => route("user.ticket.show",$ticket->ticket_number),
                "name"          => auth_user()->name,
                "ticket_number" => $ticket->ticket_number
            ];

            $notifications = [
                'database_notifications' => [
                    'action' => [SendNotification::class, 'database_notifications'],
                    'params' => [
                        [ $ticket->user, 'TICKET_REPLY', $code, Arr::get( $code , "link", null) ],
                    ],
                ],

                'email_notifications' => [
                    'action' => [SendMailJob::class, 'dispatch'],
                    'params' => [
                        [$ticket->user, 'TICKET_REPLY', $code],
                    ],
                ],
                'sms_notifications' => [
                    'action' => [SendSmsJob::class, 'dispatch'],
                    'params' => [
                        [$ticket->user, 'TICKET_REPLY', $code],
                    ],
                ],
            ];

            $this->notify($notifications);


        }


        return back()->with(response_status('Replied successfully'));
    }


    /**
     * download a file
     */
    public function download(Request $request) :mixed {

        $request->validate([
            'id'=>'required|exists:files,id',
        ]);

        $file = File::where('id',$request->input('id'))->firstOrFail();
        return $this->downloadFile(config("settings")['file_path']['ticket']['path'],$file);
    }




    /**
     * destroy a ticket
     */
    public function destroy(string $id) :RedirectResponse {

        $ticket   = Ticket::with(['messages','file'])->where('id',$id)->firstOrFail();
        return back()->with($this->ticketService->delete($ticket));

    }


    /**
     * Destroy Message
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function destroyMessage(string $id) :RedirectResponse {

        $message = Message::where('id',$id)->firstOrFail();
        $message->delete();
        return back()->with(response_status('Message Deleted Successfully'));
    }


    /**
     * Update Ticket Status
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request) :RedirectResponse {

        $request->validate([
            "id"     => ["required","exists:tickets,id"],
            "key"    => ["required",Rule::in(['priority','status'])],
            'status' => ["required"]
        ]);

        $responseStatus    = response_status('Status Updated');
        $ticket            = Ticket::where('id',$request->input("id"))->firstOrfail();
        $ticket->{$request->input("key")} = $request->input("status");
        $ticket->update();
        return back()->with($responseStatus );
    }



    /**
     * destroy a file
     */
    public function destroyFile(string $id) :RedirectResponse {

        $file = File::where('id',$id)->firstOrFail();
        try {
            $this->unlink(
                location    : config("settings")['file_path']['ticket']['path'],
                file        : $file
            );
        } catch (\Exception $ex) {
            return back()->with('error',strip_tags($ex->getMessage()));
        }
        return back()->with('success',translate('File Deleted'));
    }

}
