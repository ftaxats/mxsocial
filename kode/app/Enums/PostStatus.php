<?php

namespace App\Enums;

enum PostStatus: int 
{
    use EnumTrait;

    case PENDING         = 0;
    case SUCCESS         = 1;
    case FAILED          = 2;
    case SCHEDULE        = 3;




}