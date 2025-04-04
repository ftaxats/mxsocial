<?php
  
namespace App\Enums;
 
enum BalanceTransferType :string {

    use EnumTrait;
    
    case DEPOSIT    = 'deposit';
    case WITHDRAW   = 'withdraw';


}