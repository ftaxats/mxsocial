<?php

namespace App\Enums\Frontend;

use App\Enums\EnumTrait;

enum ContentType: string 
{
    use EnumTrait;

    case CONTENT = 'content';
    case ELEMENT = 'element';


}