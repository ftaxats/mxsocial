<?php

namespace App\Enums;

enum BadgeClass: string
{
    use EnumTrait;

    case PRIMARY = "primary";
    case SECONDARY = "secondary";
    case WARNING = "warning";
    case SUCCESS = "success";
    case INFO = "info";
    case DANGER = "danger";
    
}