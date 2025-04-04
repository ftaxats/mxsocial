<?php
  
namespace App\Enums\Gateway;

use App\Enums\EnumTrait;
use App\Enums\StatusEnum;
use Illuminate\Support\Arr;

enum MailGatewayEnum: string{


    

    use EnumTrait;

    case SMTP               = '101SMTP';
    case PHPMAIL            = '104PHP';
    case SENDGRID           = '102SENDGRID';




    /**
     * Get Mail Gateway Credential
     *
     * @return array
     */
    public static function getGatewayCredential(?string $gateway =  null): array{


       $gateways =   [
                       self::SMTP->value => [
                            "name" => t2k(self::SMTP->name),
                            "credential" => [
                                'driver'     => "@@",
                                'host'       => "@@",
                                'port'       => "@@",
                                'encryption' => "@@",
                                'username'   => "@@",
                                'password'   => "@@",
                                "from" => [
                                    "address" => "@@",
                                    "name"    => "@@",
                                ]
                            ],
                            'default' => StatusEnum::true->status()
                        ],

                        self::PHPMAIL->value => [
                            "name" => t2k(self::PHPMAIL->name),
                            "credential" => []
                        ],
                        self::SENDGRID->value => [
                            "name" => t2k(self::SENDGRID->name),
                            "credential" => [
                                'app_key' => "@@",
                                "from" => [
                                    "address" => "@@",
                                    "name"    => "@@",
                                ]
                
                            ]
                        ],
                       
                ];

       return $gateway 
                  ? Arr::get($gateways , $gateway , [])
                  : $gateways ;
      

    }
}