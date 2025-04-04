<?php
  
namespace App\Enums;
 
enum PaymentStatus :int {

    use EnumTrait;

    case INITIATE       = -1;
    case PENDING        = 0;
    case COMPLETE       = 1;
    case REJECTED       = 2;



}