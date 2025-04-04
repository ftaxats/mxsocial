<?php
  
namespace App\Enums;

use Illuminate\Support\Arr;

enum DepositStatus :int {

    use EnumTrait;

    case INITIATE        = -1;
    case PAID            =  1;
    case CANCEL          =  2;
    case PENDING         =  3;
    case FAILED          =  4;
    case REJECTED        =  5;


    /**
     * Get Badges
     *
     * @param integer $status
     * @return string
     */
    public static function getBadges(int  $status) :string {

        $badges  = [
            self::INITIATE->value     => BadgeClass::INFO->value,
            self::PENDING->value      => BadgeClass::DANGER->value,
            self::PAID->value         => BadgeClass::SUCCESS->value,
            self::FAILED->value       => BadgeClass::DANGER->value,
            self::REJECTED->value     => BadgeClass::DANGER->value,
            self::CANCEL->value       => BadgeClass::WARNING->value,
        ];

        $class    = Arr::get($badges , $status , 'info');

        $status   = ucfirst(t2k(Arr::get(array_flip(self::toArray()) ,$status , 'Pending')));
        return "<span class=\"i-badge $class\">$status</span>";

    }

}