<?php

namespace App\Enums;

enum PostType: int 
{
    use EnumTrait;

    case FEED         = 0;
    case REELS         = 1;
    case STORY         = 2;

}