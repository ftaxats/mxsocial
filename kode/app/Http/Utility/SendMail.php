<?php
namespace App\Http\Utility;

use App\Enums\StatusEnum;
use App\Models\Admin\MailGateway;
use App\Models\Admin\Template;
use Illuminate\Support\Facades\Mail;

class SendMail
{




    /**
     * send  mail notification
     *
     * @param string $template
     * @param array $code
     * @param object | null  $userinfo
     * @param MailGateway $gateway
     * @return array
     */
    public static function mailNotifications(string $template, array $tmpCodes = [] , object | null $userinfo = null , ? MailGateway $gateway = null ):array
    {
        $gatewayCode = [
            "104PHP"        => "sendPhpMail",
            "101SMTP"       => "sendSMTPMail",
            "102SENDGRID"   => "sendGrid",
        ];

        if(!$gateway) $gateway = MailGateway::where('default',StatusEnum::true->status())->first();

        $template = Template::where('slug', $template)->first();

        $messages = notificationMessage($tmpCodes ,$template->body,$userinfo);
       
        if(isset($gatewayCode[$gateway->code])) return self::{$gatewayCode[$gateway->code]}($userinfo,$template,$gateway,$messages);

        return [
            "status"  => false,
            "message" => translate('Gateway Not Found')
        ];
    }

    /**
     * send php mail
     * @param string $emailFrom
     * @param string $sitename
     * @param string $emailTo
     * @param string $subject
     * @param string $messages
     * @return array
     */
    public static function sendPhpMail(object $userinfo, Template $template ,MailGateway $gateway , string $messages) :array
    {

        $emailFrom  = site_settings('email');
        $sitename   = site_settings('site_name');
        $status = true;
        $responseMessage = translate("Email Send Successfully");
        $headers = "From: $sitename <$emailFrom> \r\n";
        $headers .= "Reply-To: $sitename <$emailFrom> \r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
      
        try {
            @mail($userinfo->email, $template->subject, $messages, $headers); 
    
        } catch (\Exception $e) {
            $status = false;
            $responseMessage = $e->getMessage();
        }

        return [
            "status"  => $status,
            "message" => $responseMessage
        ];
    }

    
    /**
     * send smtp mail
     *
     * @param string $emailFrom
     * @param string $emailTo
     * @param string $fromName
     * @param string $subject
     * @param string $messages
     * @return array
     */
    public static function sendSMTPMail(object $userinfo, Template $template ,MailGateway $gateway , string $messages) :array
    {

        $status          = true;
        $responseMessage = translate("Email Send Successfully");

        try{
            Mail::send([], [], function ($message) use ($messages, $gateway, $template ,$userinfo) {
                $message->to($userinfo->email) 
                ->subject($template->subject)
                ->from($gateway->credential->from->address,site_settings('site_name'))
                ->html($messages, 'text/html','utf-8');
            });

        }catch (\Exception $e){
            $status = false;
            $responseMessage = $e->getMessage();
        }

        return [
            "status"  => $status,
            "message" => $responseMessage
        ];
    }


    /**
     * send sendgrid  mail
     *
     * @param string $emailFrom
     * @param string $fromName
     * @param string $emailTo
     * @param string $subject
     * @param string $messages
     * @param string $credentials
     * @return array
     */
    public static function sendGrid(object $userinfo, Template $template ,MailGateway $gateway , string $messages) :array
    { 


        $status           = true;
        $responseMessage  = translate("Email Send Successfully");
        try{
            $email = new \SendGrid\Mail\Mail();
            $email->setFrom($gateway->credential->from->address, site_settings('site_name'));
            $email->setSubject($template->subject);
            $email->addTo($userinfo->email);
            $email->addContent("text/html", $messages);
            $sendgrid = new \SendGrid(@$gateway->credential->app_key);
            $response = $sendgrid->send($email);
    
            if (!in_array($response->statusCode(), ['201','200','202'])){
                $status            = false;
                $responseMessage   = translate("Faild To Send Email!! Configuration Error");
            }
            
        }catch(\Exception $e){

            $status          = false;
            $responseMessage = $e->getMessage();
        }

        return [
            "status"  => $status,
            "message" => $responseMessage
        ];


    }
}