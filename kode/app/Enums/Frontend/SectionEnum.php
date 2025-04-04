<?php

namespace App\Enums\Frontend;

use App\Enums\EnumTrait;

enum SectionEnum: string 
{
    use EnumTrait;

    case BANNER                      = "banner";
    case SERVICE                     = "service";
    case PLATFORM_SERVICE            = "platform_service";
    case FEATURE_SERVICE             = "feature_service";
    case INSIGHT_SERVICE             = "insight_service";
    case TESTIMONIAL       = "testimonial";
    case FAQ               = "faq";
    case BLOG              = "blog";
    case SIGN_IN           = "sign_in";
    case SIGN_UP           = "sign_up";
    case FEATURE           = "feature";
    case POWERFUL_FEATURE  = "powerful_feature";
    case FOOTER            = "footer";
    case PRICING_PLAN      = "pricing_plan";
    case TEMPLATE          = "template";
    case INTREGRATION      = "intregration";
    case SOCIAL            = "social";
    case CONTACT           = 'contact';
    case COOKIE            = 'cookie';
    case SUPPORT_TICKET    = 'support_ticket';
    case FEEDBACK          = 'feed_back';
    case OUR_TEAM          = 'our_team';
    case ABOUT             = 'about';

}