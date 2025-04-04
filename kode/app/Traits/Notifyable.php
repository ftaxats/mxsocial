<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
trait Notifyable
{



    protected function notify($notifications) :void {

        foreach ($notifications as $notification => $config) {
            if (notify($notification)) {
                $action = $config['action'];
                $params = $config['params'];
        
                foreach ($params as $paramSet) {
                    if ($paramSet) {
                        call_user_func_array($action, $paramSet);
                    }
                }
            }
        }

    }

}