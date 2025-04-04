<?php
namespace App\Http\Utility;

use App\Enums\StatusEnum;
use App\Models\Admin\Template;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules\Enum;

class SendNotification
{

       

       /**
        * send database notifications
        */
       public static function  database_notifications(mixed $sendTo , string $template ,array $tmpCodes ,mixed $route) :void{
        
            $template     = Template::where('slug', $template)->first();

            $messages     = notificationMessage($tmpCodes ,$template->sms_body,$sendTo);

            $notification = new Notification([
                'message'      => $messages,
                'url'          => $route,
                'is_read'      =>(StatusEnum::false)->status() ,
            ]);

            $sendTo->notifications()->save($notification);

    
       }

       public static function slack_notifications(mixed $sendTo , string $template , array $tmpCodes ,mixed $route ) :array{

            $template = Template::where('slug', $template)->first();
            $messages = strip_tags(notificationMessage($tmpCodes ,$template->sms_body,$sendTo));
            $webhookUrl =  site_settings("slack_web_hook_url");
            $client = new Client();
            $dateTime = "*  ".get_date_time(Carbon::now())."* ".  " | ".  translate($template->name);
            $payload = [
                "blocks" => array(
                    array(
                        "type" => "header",
                        "text" => array(
                            "type" => "plain_text",
                            "text" => ":bell:  ".  translate('New Notifications')  ." :bell:"
                        )
                    ),
                    array(
                        "type" => "context",
                        "elements" => array(
                            array(
                                "text" => $dateTime,
                                "type" => "mrkdwn"
                            )
                        )
                    ),
                    array(
                        "type" => "divider"
                    ),
                    array(
                        "type" => "section",
                        "text" => array(
                            "type" => "mrkdwn",
                            "text" => " :loud_sound: *IN CASE YOU MISSED IT* :loud_sound:"
                        )
                    ),
                    array(
                        "type" => "section",
                        "text" => array(
                            "type" => "mrkdwn",
                            "text" => $messages
                    
                        ),
                        "accessory" => array(
                            "type" => "button",
                            "text" => array(
                                "type" => "plain_text",
                                "text" => $template->subject,
                                "emoji" => true
                            ),
                            "url"   =>  $route,
                            "style" => "primary",
                        )
                    ),
                
                )
         
            ];

            if(site_settings("slack_channel")){
                $payload['channel'] = site_settings("slack_channel");
            }

            try {
                $message = "notified";
                $status  = true;
                $client->request('POST', $webhookUrl, [
                    'json' => $payload,
                ]);
            } catch (\Exception $e) {
                 $status = false;
                 $message = $e->getMessage();
            }
    

            return [
                'status' =>  $status ,
                'message' =>  $message ,
            ];
       }

}