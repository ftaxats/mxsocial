<?php

namespace App\Enums;

enum TiktokConnection: int
{
    use EnumTrait;

    case TIKTOK_OAUTH           = 1;

}
