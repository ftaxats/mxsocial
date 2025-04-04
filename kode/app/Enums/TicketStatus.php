<?php
  
namespace App\Enums;

use App\Enums\BadgeClass;
use App\Enums\EnumTrait;
use Illuminate\Support\Arr;

enum TicketStatus :int {

    use EnumTrait;
    

    case OPEN       = 1;
    case PENDING    = 2;
    case PROCESSING = 3;
    case SOLVED     = 4;
    case HOLD       = 5;
    case CLOSED     = 6;


    /**
     * Get Badges
     *
     * @param integer $status
     * @return string
     */
    public static function getBadges(int  $status) :string {

        $badges  = [
            self::PENDING->value     => BadgeClass::WARNING->value,
            self::OPEN->value        => BadgeClass::DANGER->value,
            self::PROCESSING->value  => BadgeClass::INFO->value,
            self::SOLVED->value      => BadgeClass::SUCCESS->value,
            self::HOLD->value        => BadgeClass::WARNING->value,
            self::CLOSED->value      => BadgeClass::DANGER->value
        ];
        $class    = Arr::get($badges , $status , 'info');
   
        $status   = ucfirst(t2k(Arr::get(array_flip(self::toArray()) ,$status , 'Pending')));
        return "<span class=\"i-badge $class\">$status</span>";

    }

}