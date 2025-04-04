<?php

namespace App\Providers;

use App\Models\Admin\MailGateway;
use Illuminate\Support\Facades\Config;
use App\Models\MailConfiguration;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            $mail = MailGateway::where('code', '101SMTP')->first();
            if($mail){
                $config = array(
                    'driver'     => @$mail->credential->driver,
                    'host'       => @$mail->credential->host,
                    'port'       => @$mail->credential->port,
                    'from'       => [
                        'address'=> @$mail->credential->from->address,
                        'name'   => @$mail->credential->from->name
                    ],
                    'encryption' => @$mail->credential->encryption=="PWMTA"?null:$mail->credential->encryption,
                    'username'   => @$mail->credential->username,
                    'password'   => @$mail->credential->password,
                    'sendmail'   => '/usr/sbin/sendmail -bs',
                    'pretend'    => false,
                );
                Config::set('mail', $config);

            }
        }catch (\Exception $ex) {

        }
    }
}
