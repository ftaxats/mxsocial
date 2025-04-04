<?php

namespace App\Enums;

enum StorageKey: string
{
    use EnumTrait;

    case S3           = "setAWSConfig";
    case FTP          = "setFTPConfig";
    case LOCAL        = "local";


    
}