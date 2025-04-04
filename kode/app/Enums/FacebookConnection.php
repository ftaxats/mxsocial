<?php

namespace App\Enums;

enum FacebookConnection: int 
{
    use EnumTrait;


    case FACEBOOK_OAUTH           = 1;
    case MANUAL_TOKEN             = 0;


}