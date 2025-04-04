<?php

namespace App\Enums;

enum LinkedinConnection: int 
{
    use EnumTrait;

    case LINKEDIN_OAUTH           = 1;

}