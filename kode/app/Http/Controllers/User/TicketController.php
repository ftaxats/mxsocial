<?php

namespace App\Http\Controllers\User;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\TicketRequest;
use App\Http\Services\TicketService;
use App\Http\Utility\SendNotification;
use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;
use App\Models\Admin;
use App\Models\Core\File;
use App\Models\Message;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Traits\Notifyable;
use Illuminate\Support\Arr;

use App\Traits\Fileable;
class TicketController extends Controller
{

    use Notifyable , Fileable;

    protected $ticketService ,$user;


    public function __construct(){
        $this->ticketService = new TicketService();

        $this->middleware(function ($request, $next) {
            $this->user = auth_user('web');
            return $next($request);
        });
    }

    /**
     * Support Ticket View
     *
     * @return View
     */
    public function list() :View{


        return view('user.ticket.list',[
            'meta_data' => $this->metaData(["title" => translate("Ticket List")]),
            'tickets'   => Ticket::with(['user','messages'])
                            ->where('user_id',$this->user->id)
                            ->latest()
                            ->search(['subject'])
                            ->filter(['ticket_number',"status",'priority'])
                            ->date()
                            ->paginate(paginateNumber())
        ]);
    }


    /**
     * Support Ticket create
     *
     * @return View
     */
    public function create() :View{

        return view('user.ticket.create',[
            'meta_data'=> $this->metaData(["title" => translate("Create Ticket")]),
        ]);
    }



    /**
     * Create A new Ticket
     *
     * @param TicketRequest $request
     * @return RedirectResponse
     */
    public function store(TicketRequest $request) :RedirectResponse{


        try {
            $ticket =  $this->ticketService->store($request->except('_token') ,$this->user->id);
            return redirect()->route('user.ticket.show',$ticket->ticket_number)
                             ->with(response_status('Ticket Successfully Created'));

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
    public function show(string $ticketNumber) : View {

        return view('user.ticket.show',[

            'meta_data'=> $this->metaData(["title" => translate("Ticket Details")]),

            'ticket'   => Ticket::with(['user','user.file','messages','messages.admin' ,'messages.admin.file'])
                                    ->where('user_id',$this->user->id)
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
            'id' => "required|exists:tickets,id",
            "message" => 'required|string'
        ]);


        $ticket              = Ticket::with(['user'])
                                        ->where('user_id',$this->user->id)
                                        ->where('id',$request->input('id'))->firstOrFail();

        $message             = $this->ticketService->reply($ticket,$request);

        $admin               = get_superadmin();

        if($message){

            $code = [
                "link"          => route("admin.ticket.show",$ticket->ticket_number),
                "name"          => $this->user->name,
                "ticket_number" => $ticket->ticket_number
            ];

            $notifications = [

                'database_notifications' => [
                    'action' => [SendNotification::class, 'database_notifications'],
                    'params' => [
                        [ $admin, 'TICKET_REPLY', $code, Arr::get( $code , "link", null) ],
                    ],
                ],

                'email_notifications' => [
                    'action' => [SendMailJob::class, 'dispatch'],
                    'params' => [
                        [$admin, 'TICKET_REPLY', $code],
                    ],
                ],

                'sms_notifications' => [
                    'action' => [SendSmsJob::class, 'dispatch'],
                    'params' => [
                        [$admin, 'TICKET_REPLY', $code],
                    ],
                ],
            ];

            $this->notify($notifications);

        }

        return back()->with(response_status('Replied Successfully'));
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
    public function destroy(string $id) {

        $ticket   = Ticket::with(['messages','file'])
                                    ->where('id',$id)
                                    ->where('user_id',$this->user->id)
                                    ->firstOrFail();
        return back()->with($this->ticketService->delete($ticket));

    }


}
