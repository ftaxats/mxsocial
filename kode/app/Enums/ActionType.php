<?php

namespace App\Enums;

enum ActionType: string
{
    use EnumTrait;

    case RESTORE   = "restore";
    case FORCE_DELETE  = "force_delete";
    case DELETE   = "delete";


    
}