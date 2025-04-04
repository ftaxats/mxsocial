<?php

namespace App\Enums;

enum MenuVisibilty: int 
{
    use EnumTrait;

    case HEADER           = 0;
    case FOOTER           = 1;
    case BOTH             = 2;

}