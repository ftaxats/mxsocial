<?php

namespace App\Enums;

enum LoginKeyEnum: string
{
    use EnumTrait;

    case EMAIL           = "email";
    case PHONE           = "phone";
    case USERNAME        = "username";
    case PHONE_NUMBER    = "phone_number";


    
}