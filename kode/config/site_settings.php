<?php

use App\Enums\InputEnum;
use App\Enums\LoginKeyEnum;
use App\Enums\SecurityType;
use App\Enums\StatusEnum;
use App\Enums\StorageKey;
use App\Enums\ThemeColor;
use Carbon\Carbon;


return [

    "site_name"      => "demo",
    "logo_icon"      => "@@",
    "site_logo"      => "@@",
    "user_site_logo" => "@@",
    "favicon"        => "@@",
    "phone"          => "0xxxxxxxx",
    "address"        => "demo",
    "email"               => "demo@gmail.com",
    "last_corn_run"       => Carbon::now(),

    'registration'   => StatusEnum::true->status(),
    'login'          => StatusEnum::true->status(),

    "login_with" => json_encode([
        LoginKeyEnum::EMAIL->value,
        LoginKeyEnum::PHONE->value,
        LoginKeyEnum::USERNAME->value
    ]),


    "default_sms_template"  => "hi {{name}}, {{message}}",
    "default_mail_template" => "hi {{name}}, {{message}}",
    "two_factor_auth"       => StatusEnum::false->status(),


    "sms_otp_verification"          => StatusEnum::false->status(),
    "registration_otp_verification" => StatusEnum::false->status(),
    "otp_expired_status"            => StatusEnum::false->status(),
    "sms_notifications"             => StatusEnum::false->status(),
    "email_verification"            => StatusEnum::false->status(),
    "email_notifications"           => StatusEnum::false->status(),
    "slack_notifications"           => StatusEnum::false->status(),
    "currency_alignment"            => 0,
    "num_of_decimal"                => "0",
    "decimal_separator"             => ".",
    "thousands_separator"           => ",",
    "price_format"                  => StatusEnum::false->status(),
    "truncate_after"                => 1000,
    "slack_channel"                 => "@@",
    "slack_web_hook_url"            => "@@",
    "time_zone"                     => null,
    "site_seo"                      => StatusEnum::false->status(),
    "app_debug"                     => StatusEnum::false->status(),
    "maintenance_mode"              => StatusEnum::false->status(),
    "pagination_number"             => 10,
    "copy_right_text"               =>'@@@@',
    "same_site_name"                => StatusEnum::false->status(),


    "user_site_name"   => "demo_site",
    "google_recaptcha" => json_encode([
        'key'        => '@@@',
        'secret_key' => '@@@',
        'status'     => StatusEnum::false->status()
    ]),

    "strong_password" => StatusEnum::true->status(),

    "captcha" => StatusEnum::false->status(),
    "vistors" => 500,

    "sign_up_bonus"             => StatusEnum::false->status(),

    "default_recaptcha"         => StatusEnum::false->status(),
    "captcha_with_login"        => StatusEnum::true->status(),
    "captcha_with_registration" => StatusEnum::true->status(),
    "social_login"              => StatusEnum::false->status(),
    "social_login_with"         => json_encode([
        'google_oauth' => [
            'client_id'     => '@@',
            'client_secret' => '@@',
            'status'        => StatusEnum::true->status(),
        ],
        'facebook_oauth' => [
            'client_id'     => '@@',
            'client_secret' => '@@',
            'status'        => StatusEnum::true->status(),
        ],
    ]),

    'google_map_api_key' => '@@@@',

    'storage'    => StorageKey::LOCAL->value,
    'mime_types' => json_encode([
        'png',
        'jpg',
        'jpeg',
        'jpe',
    ]),

    'max_file_size'   => 20000,
    "max_file_upload" => 4,
    'aws_s3' => json_encode( [
        's3_key'    => '@@',
        's3_secret' => '@@',
        's3_region' => '@@',
        's3_bucket' => '@@'
    ]),

    'ftp' => json_encode( [
        'host'      => '@@',
        'port'      => '@@',
        'user_name' => '@@',
        'password'  => '@@',
        'root'      => '/'
    ]),

    'database_notifications'    => StatusEnum::false->status(),
    'cookie'                    => StatusEnum::false->status(),
    'frontend_preloader'        => StatusEnum::false->status(),
    'cookie_text'               => "demo cookie_text",
    'google_map_key'            => "@@",
    'geo_location'              => "map_base",
    'sentry_dns'                => "@@",
    'login_attempt_validation'  =>  StatusEnum::false->status(),
    "max_login_attemtps"        => 5,

    "otp_expired_in"       => 2,
    'api_route_rate_limit' => 1000,
    'web_route_rate_limit' => 1000,

    'primary_color'       => ThemeColor::PRIMARY_COLOR->value,
    'secondary_color'     => ThemeColor::SECONDARY_COLOR->value,
    'text_primary'        => ThemeColor::TEXT_PRIMARY->value,
    'text_secondary'      => ThemeColor::TEXT_SECONDARY->value,
    'btn_text_primary'    => ThemeColor::BTN_TEXT_PRIMARY->value,
    'btn_text_secondary'  => ThemeColor::BTN_TEXT_SECONDARY->value,
    

    /** newly added content */
    'site_description'       => 'demo description',

    "sms_notification"       => StatusEnum::false->status(),

    "max_pending_withdraw"   => 1,
    "force_ssl"              => StatusEnum::false->status(),
    "dos_prevent"            => StatusEnum::false->status(),
    "dos_attempts"           => StatusEnum::false->status(),
    "dos_attempts_in_second" => 5,
    "dos_security"           => SecurityType::CAPTCHA->value,

    "google_ads"                   => StatusEnum::false->status(),
    'google_adsense_publisher_id'  => "@@",
    "google_analytics"             => StatusEnum::false->status(),
    'google_analytics_tracking_id' => "@@",
    
    'breadcrumbs'                  => StatusEnum::true->status(),

    'expired_data_delete'       => StatusEnum::false->status(),
    'expired_data_delete_after' => 10,

    "site_meta_keywords" => json_encode(['demo']),
    "title_separator"    => ":",



    "ai_default_creativity" => 0.5,
    "ai_default_tone"       => "Casual",
    "ai_max_result"         => 4,
    "default_max_result"    => 20,
    "ai_result_length"      => 20,
    "ai_bad_words"          => null,
    "open_ai_model"         => null,
    "open_ai_secret"        => "@@",
    "ai_key_usage"          => StatusEnum::false->status(),
    "rand_api_key"          => "@@",

    "subscription_carry_forword" => StatusEnum::false->status(),
    "auto_subscription"          => StatusEnum::false->status(),
    "auto_subscription_package"  => null,

    "signup_bonus"          => null,
    "webhook_api_key"       => "@@",
    "kyc_settings"          => json_encode(
    [
        [
            'labels'      => 'Name',
            'name'        => 'name',
            'placeholder' => 'Name',
            'type'        => InputEnum::TEXT->value,
            'required'    => StatusEnum::true->status(),
            'default'     => StatusEnum::true->status(),
            'multiple'    => StatusEnum::false->status()
        ]
    ]),
    "kyc_verification"      => StatusEnum::false->status(),
    "ticket_settings" => json_encode(
    [
        [
            'labels'      => 'Name',
            'name'        => 'name',
            'placeholder' => 'Name',
            'type'        => InputEnum::TEXT->value,
            'required'    => StatusEnum::true->status(),
            'default'     => StatusEnum::true->status(),
            'multiple'    => StatusEnum::false->status()
        ],
        [
            'labels'      => 'Subject',
            'name'        => 'subject',
            'placeholder' => 'Subject',
            'type'        => InputEnum::TEXT->value,
            'required'    => StatusEnum::true->status(),
            'default'     => StatusEnum::true->status(),
            'multiple'    => StatusEnum::false->status()
        ],
        [
            'labels'      => 'Description',
            'name'        => 'description',
            'placeholder' => 'Description',
            'type'        => InputEnum::TEXTAREA->value,
            'required'    => StatusEnum::true->status(),
            'default'     => StatusEnum::true->status(),
            'multiple'    => StatusEnum::false->status()
        ], 
        [
            'labels'      => 'File',
            'name'        => 'attachment',
            'placeholder' => 'Upload file',
            'type'        => InputEnum::FILE->value,
            'required'    => StatusEnum::true->status(),
            'default'     => StatusEnum::true->status(),
            'multiple'    => StatusEnum::true->status()
        ]
    ]),

    "continuous_commission" => StatusEnum::false->status(),
    "affiliate_system"      => StatusEnum::false->status(),

    "multi_lang"            => StatusEnum::false->status(),
    "multi_currency"        => StatusEnum::false->status(),
    "meta_image"            => '@@',
];
