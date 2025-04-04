<?php

namespace App\Enums;

enum ConnectionType: int 
{
    use EnumTrait;

    case UNOFFICIAL              = 0;
    case OFFICIAL                = 1;

}