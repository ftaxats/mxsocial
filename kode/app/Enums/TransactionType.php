<?php
  
namespace App\Enums;

use Illuminate\Support\Arr;

enum TransactionType: string{

    use EnumTrait;
    case PLUS        = "+";
    case MINUS       = "-";
}