<?php
  
namespace App\Enums\Gateway;

use App\Enums\EnumTrait;
use App\Enums\StatusEnum;
use Illuminate\Support\Arr;

enum PaymentGatewayEnum :string {



    use EnumTrait;

    case BKASH               = 'bkash';
    case NAGAD               = 'nagad';
    case PAYPAL              = 'paypal';
    case STRIPE              = 'stripe';
    case PAYEER              = 'payeer';
    case PAYSTACK            = 'paystack';
    case FLUTTERWAVE         = 'flutterwave';
    case RAZORPAY            = 'razorpay';
    case INSTAMOJO           = 'instamojo';
    case MOLLIE              = 'mollie';
    case PAYUMONEY           = 'payumoney';

    case MERCADOPOGO         = 'mercadopago';
    case CASHMAAL            = 'cashmaal';
    case PAYTM               = 'paytm';

    case AUTHORIZEDOTNET     = 'authorizedotnet';
    case NMI                 = 'nmi';
    case BTCPAY              = 'btcpay';
    case PERFECTMONEY        = 'perfectmoney';
    case COINGATE            = 'coingate';
    case SKRILL              = 'skrill';
    case COINBASE            = 'coinbase';





    /**
     * Get Mail Gateway Credential
     *
     * @return array
     */
    public static function getGatewayCredential(? string $gateway =  null) : array {


       
       $gateways =   [
                    self::BKASH->value => [
                            "name"             => self::BKASH->name,
                            "serial_id"        => "1",
                            "currency_id"      => base_currency()->id,
                            "parameters"       => [
                                    "api_key"    => "@@",
                                    "username"   => "@@",
                                    "password"   => "@@",
                                    "api_secret" => "@@",
                                    "sandbox"    => StatusEnum::true->status()
                            ],
                            "extra_parameters" =>[
                                "callback" => "ipn"
                            ],
                    ],

                    self::NAGAD->value => [
                        "name"             => self::NAGAD->name,
                        "serial_id"        => "2",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "pub_key"         => "@@",
                            "pri_key"         => "@@",
                            "marchent_number" => "@@",
                            "marchent_id"     => "@@",
                            "sandbox"         => StatusEnum::true->status()
                        ],
                        "extra_parameters" => [
                            "callback" => "ipn"
                        ],
                    ],

                    self::PAYPAL->value => [
                        "name"             => self::PAYPAL->name,
                        "serial_id"        => "3",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "cleint_id" => "@@",
                            "secret"    => "@@",
                        ],
                        
                    ],

                    self::STRIPE->value => [
                        "name"             => self::STRIPE->name,
                        "serial_id"        => "4",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "secret_key"      => "@@",
                            "publishable_key" => "@@",
                        ],
                        
                    ],

                    self::PAYEER->value => [
                        "name"             => self::PAYEER->name,
                        "serial_id"        => "5",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "merchant_id" => "@@",
                            "secret_key"  => "@@",
                        ],
                        "extra_parameters" => [
                            "status" => "ipn"
                        ],
                    ],

                    self::PAYSTACK->value => [
                        "name"             => self::PAYSTACK->name,
                        "serial_id"        => "6",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "public_key" => "@@",
                            "secret_key" => "@@",
                        ],
                        "extra_parameters" => [
                            "callback" => "ipn",
                            "webhook"  => "ipn"
                        ],
                    ],

                    self::FLUTTERWAVE->value => [
                        "name"             => self::FLUTTERWAVE->name,
                        "serial_id"        => "7",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "public_key"     => "@@",
                            "secret_key"     => "@@",
                            "encryption_key" => "@@"
                        ],
                        
                    ],

                    self::RAZORPAY->value => [
                        "name"             => self::RAZORPAY->name,
                        "serial_id"        => "8",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "key_id"     => "@@",
                            "key_secret" => "@@"
                        ],
                        
                    ],

                    self::INSTAMOJO->value => [
                        "name"             => self::INSTAMOJO->name,
                        "serial_id"        => "9",
                        "currency_id"      => base_currency()->id,

                        "parameters"       => [
                            "api_key"    => "@@",
                            "auth_token" => "@@",
                            "salt"       => "@@"
                        ]
                        
                    ],

                    self::MOLLIE->value => [
                        "name"             => self::MOLLIE->name,
                        "serial_id"        => "10",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "api_key" => "@@",
                        ]
                        
                    ],

                    self::PAYUMONEY->value => [
                        "name"             => self::PAYUMONEY->name,
                        "serial_id"        => "11",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "merchant_key" => "@@",
                            "salt"         => "@@"
                        ],
                        
                        
                        
                    ],
                    
                    self::MERCADOPOGO->value => [
                        "name"             => self::MERCADOPOGO->name,
                        "serial_id"        => "12",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "access_token" => "@@",
                        ],
                        "extra_parameters" =>  [],
                    ],

                    self::CASHMAAL->value => [
                        "name"             => self::CASHMAAL->name,
                        "serial_id"        => "13",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "web_id"  => "@@",
                            "ipn_key" => "@@"
                        ],
                        "extra_parameters" => [
                            "ipn_url" => "ipn"
                        ],
                    ],

                    self::PAYTM->value => [
                        "name"             => self::PAYTM->name,
                        "serial_id"        => "14",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "mid"                    => "@@",
                            "merchant_key"           => "@@",
                            "website"                => "@@",
                            "industry_type_id"       => "@@",
                            "channel_id"             => "@@",
                            "transaction_url"        => "@@",
                            "transaction_status_url" => "@@"
                        ],
                        
                    ],

                
                    
                    self::AUTHORIZEDOTNET->value => [
                        "name"             => self::AUTHORIZEDOTNET->name,
                        "serial_id"        => "16",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "login_id"                => "@@",
                            "current_transaction_key" => "@@"
                        ]
                        
                    ],

                    self::NMI->value => [
                        "name"             => self::NMI->name,
                        "serial_id"        => "17",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "api_key" => '@@',
                        ],
                        
                    ],

                    self::BTCPAY->value => [
                        "name"             => self::BTCPAY->name,
                        "serial_id"        => "18",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "store_id"    => '@@',
                            "api_key"     => '@@',
                            "server_name" => '@@',
                            "secret_code" => '@@',
                        ],
                        "extra_parameters" => [
                            "callback" => 'ipn',
                        ]
                    ],
                    self::PERFECTMONEY->value => [
                        "name"             => self::PERFECTMONEY->name,
                        "serial_id"        => "19",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "passphrase" => '@@',
                            "wallet_id" => '@@',
                        ],
                        
                    ],

                    self::COINGATE->value => [
                        "name"             => self::COINGATE->name,
                        "serial_id"        => "22",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "api_key" => '@@',
                        ]
                        
                    ],

                    self::SKRILL->value => [
                        "name"             => self::SKRILL->name,
                        "serial_id"        => "23",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "secret_key"   => '@@',
                            "skrill_email" => '@@',
                        ]
                        
                    ],

                    self::COINBASE->value => [
                        "name"             => self::COINBASE->name,
                        "serial_id"        => "24",
                        "currency_id"      => base_currency()->id,
                        "parameters"       => [
                            "api_key"        => '@@',
                            "webhook_secret" => '@@',
                        ],
                        "extra_parameters" => [
                            "webhook" => "ipn"
                        ]
                    ],
                ];

       return $gateway 
                  ? Arr::get($gateways , $gateway , [])
                  : $gateways ;
      

    }
}