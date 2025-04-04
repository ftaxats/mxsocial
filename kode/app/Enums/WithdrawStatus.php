<?php
  
namespace App\Enums;
 
enum WithdrawStatus :int {

    use EnumTrait;


    case APPROVED       = 1;
    case REJECTED       = 2;
    case PENDING        = 3;


}