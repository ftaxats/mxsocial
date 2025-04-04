<?php

namespace App\Enums;

enum YoutubeConnection: int
{
    use EnumTrait;

    case YOUTUBE_OAUTH           = 1;

}
