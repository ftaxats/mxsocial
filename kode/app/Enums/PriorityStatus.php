<?php

namespace App\Enums;

use App\Enums\BadgeClass;
use App\Enums\EnumTrait;
use Illuminate\Support\Arr;

enum PriorityStatus: int
{
    use EnumTrait;

    case URGENT   = 1;
    case HIGH     = 2;
    case LOW      = 3;
    case MEDIUM   = 4;

    /**
     * Get Badges
     *
     * @param integer $status
     * @return string
     */
    public static function getBadges(int  $status) :string {

        $badges  = [
            self::URGENT->value     => BadgeClass::DANGER->value,
            self::HIGH->value       => BadgeClass::WARNING->value,
            self::LOW->value        => BadgeClass::INFO->value,
            self::MEDIUM->value     => BadgeClass::SUCCESS->value,
        ];
        
        $class    = Arr::get($badges , $status , 'info');
        $status   = ucfirst(t2k(Arr::get(array_flip(self::toArray()) ,$status , 'Pending')));
        return "<span class=\"i-badge $class\">$status</span>";

    }
   
}