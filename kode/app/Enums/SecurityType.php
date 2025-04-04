<?php

namespace App\Enums;

enum SecurityType: string 
{
    use EnumTrait;

    case CAPTCHA            = 'captcha';
    case IP_BLOCK           = 'block_ip';




}