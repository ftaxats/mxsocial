<?php
namespace App\Http\Services;

use App\Enums\PriorityStatus;
use App\Enums\StatusEnum;
use App\Enums\TicketStatus;
use App\Http\Utility\SendNotification;
use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;
use App\Models\Admin;
use App\Models\Core\File;
use App\Models\Message;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Traits\Fileable;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\Notifyable;
class TicketService
{


    use Fileable , Notifyable;

    /**
     * Create A Ticket
     *
     * @param array $request
     * @return Ticket
     */
    public function store(array $request ,int | string $user_id) :Ticket{


       $ticket = 

        DB::transaction(function() use ($request ,$user_id) {

            $user = User::where('id',$user_id)->first();
            $ticket                  = new Ticket();
            $ticket->ticket_number   = generateTicketNumber();
            $ticket->user_id         = $user->id;
            $ticket->subject         = Arr::get($request['ticket_data'],'subject' ,'');
            $ticket->message         = Arr::get($request['ticket_data'],'description' ,'');
            $ticket->priority        = Arr::get($request,'priority' ,1);
            $ticket->status          = TicketStatus::PENDING;
            $ticket->ticket_data     = (Arr::except($request['ticket_data'],['attachment']));
            $ticket->save();

            if(isset($request["ticket_data"] ['attachment'][0])){
                
                foreach($request["ticket_data"] ['attachment'] as $file){
                    $response = $this->storeFile(
                        file        : $file, 
                        location    : config("settings")['file_path']['ticket']['path'],
                    );
                    if(isset($response['status'])){
                        $image = new File([
                            'name'      => Arr::get($response, 'name', '#'),
                            'disk'      => Arr::get($response, 'disk', 'local'),
                            'type'      => 'ticket_file',
                            'size'      => Arr::get($response, 'size', ''),
                            'extension' => Arr::get($response, 'extension', ''),
                        ]);

                        $ticket->file()->save($image);
                    }
                }
            }

            $message        = $this->ticketMessage($ticket);
            $route          =  route("admin.ticket.list");
            $userRoute      =  route("user.ticket.list");

            $admin          = get_superadmin();
            $priority       = Arr::get(array_flip(PriorityStatus::toArray()), $ticket->priority, 'Low');

            $code           = [
                "ticket_number" =>  $ticket->ticket_number,
                "name"          =>  $user->name,
                "time"          =>  Carbon::now(),
                "priority"      =>  t2k($priority),
                "link"          =>  route("user.ticket.show",$ticket->ticket_number),
            ];

            $notifications = [

                'database_notifications' => [
                    'action' => [SendNotification::class, 'database_notifications'],
                    'params' => [
                        [ $admin, 'NEW_TICKET', $code, $route ],
                        [ $user, 'SUPPORT_TICKET_REPLY', $code, $userRoute ],
                    ],
                ],

              

                'email_notifications' => [
                    'action' => [SendMailJob::class, 'dispatch'],
                    'params' => [
                        [$admin,'NEW_TICKET',$code],
                        [$user, 'SUPPORT_TICKET_REPLY', $code],
                    ],
                ],
                'sms_notifications' => [
                    'action' => [SendSmsJob::class, 'dispatch'],
                    'params' => [
                        [$admin,'NEW_TICKET',$code],
                        [$user, 'SUPPORT_TICKET_REPLY', $code],
                    ],
                ],
               
            ];

            $this->notify($notifications);

            return $ticket;

        });

        return  $ticket;

    }

    /**
     * store ticket message
     *
     * @param Ticket $ticket
     * @return Message
     */
    public function ticketMessage(Ticket $ticket) :Message{

        $message             = new Message();
        $message->ticket_id  = $ticket->id;
        $message->message    = $ticket->message;
        $message->save();
        return $message;
    }




    /**
     * store ticket message
     *
     * @param Ticket $ticket
     * @return Message
     */
    public function reply(Ticket $ticket ,Request $request ,mixed $adminId = null) :Message{

        $message             = new Message();
        $message->admin_id   = $adminId;
        $message->ticket_id  = $ticket->id;
        $message->message    = $request->input("message");
        $message->save();
        
        return $message;
    }



   



    /**
     * delete a ticket
     *
     * @param Ticket $ticket
     * @return void
     */
    public function delete(Ticket $ticket) :array{

        $response = response_status('Ticket Deleted Successfully');

        try {
            $ticket->messages()->delete();
            foreach($ticket->file as $file){
                $this->unlink(
                    location    : config("settings")['file_path']['ticket']['path'],
                    file        : $file
                );
            }
            $ticket->delete();
        } catch (\Exception $ex) {
            $response = response_status(strip_tags($ex->getMessage()),'error');
        }

        return $response;
        
    }

}
