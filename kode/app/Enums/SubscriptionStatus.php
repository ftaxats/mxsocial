<?php

namespace App\Enums;

enum SubscriptionStatus: int
{
    use EnumTrait;

    case RUNNING   = 1;
    case INACTIVE  = 2;
    case EXPIRED   = 3;



}