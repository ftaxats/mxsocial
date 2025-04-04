<?php

namespace App\Enums;

enum CategoryDisplay: int 
{
    use EnumTrait;

    case BLOG      = 0;
    case TEMPLATE     = 1;
    case BOTH         = 2;

}