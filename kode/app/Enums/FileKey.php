<?php

namespace App\Enums;

enum FileKey: string
{
    use EnumTrait;

    case AVATAR                = "avatar";
    case FEATURE               = "feature";
    case POST_FILE             = "post_file";
    
    
}