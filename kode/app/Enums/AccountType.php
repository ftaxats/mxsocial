<?php

namespace App\Enums;

enum AccountType: int
{
    use EnumTrait;


    case PAGE         = 1;
    case GROUP        = 2;

    case PROFILE      = 0;

    case CHANNEL      = 3;



}
