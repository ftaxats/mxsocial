<?php

namespace App\Enums;

enum InstagramConnection: int 
{
    use EnumTrait;
    case INSTAGRAM_OAUTH           = 1;

}