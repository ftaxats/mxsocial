<?php

namespace App\Enums;

enum TwitterConnection: int 
{
    use EnumTrait;

    case TWITTER_OAUTH           = 1;

}