<?php
  
namespace App\Enums\Gateway;

use App\Enums\EnumTrait;
use App\Enums\StatusEnum;
use Illuminate\Support\Arr;

enum SMSGatewayEnum :string {

    use EnumTrait;

    case VONAGE            = '101VON';
    case TWILIO            = '102TWI';
    case MESSAGEBIRD       = '103BIRD';
    case INFOBIP           = '104INFO';



    /**
     * Get SMS Gateway Credential
     *
     * @return array
     */
    public static function getGatewayCredential(? string $gateway =  null) : array {


       $gateways =   [
                        self::VONAGE->value => [
                            "name" => t2k(self::VONAGE->name),
                            "credential" => ([
                                'api_key'    => "@@",
                                'api_secret' => "@@",
                                'sender_id'  => "@@"
                            ]),
                            'default' => StatusEnum::true->status()
                        ],

                        self::TWILIO->value => [
                            "name" => t2k(self::TWILIO->name),
                            "credential" => ([
                                'account_sid'  => "@@",
                                'auth_token'   => "@@",
                                'from_number'  => "@@"
                            ])
                        ],
                        self::MESSAGEBIRD->value => [
                            "name" => t2k(self::MESSAGEBIRD->name),
                            "credential" => ([
                                'access_key' => "@@",
                
                            ])
                        ] ,
                
                        self::INFOBIP->value => [
                            "name" => t2k(self::INFOBIP->name),
                            "credential" => ([
                                'sender_id'        => "@@",
                                'infobip_api_key'  => "@@",
                                'infobip_base_url' => "@@"
                            ])
                
                        ],
                ];

       return $gateway 
                  ? Arr::get($gateways , $gateway , [])
                  : $gateways ;
      

    }
}