<?php

namespace App\Enums;

enum InputEnum: string
{
    use EnumTrait;

    case TEXT                = "text";
    case HTML_TEXT           = "html_text";
    case TEXTAREA            = "textarea";
    case TEXT_EDITOR         = "text_editor";
    case FILE                = "file";
    case SELECT              = "select";
    case EMAIL               = "email";
    case PASSWORD            = "password";
    case HIDDEN              = "hidden";
    case DISABLE             = "disable";
    case ICON_PICKER         = "icon";
    
    
    
}