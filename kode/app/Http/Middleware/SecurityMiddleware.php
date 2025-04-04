<?php

namespace App\Http\Middleware;

use App\Enums\StatusEnum;
use App\Models\Country;
use App\Models\Visitor;
use Closure;
use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;


class SecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        try {
                /** dos security check  */
                if(site_settings('dos_prevent') == StatusEnum::true->status() && !session()->has('dos_captcha') && session()->has('security_captcha')){
                    session()->put('requested_route',Route::currentRouteName());
                    return ($request->expectsJson() || $request->isXmlHttpRequest()) ? response()->json(response_status('Unauthorized ip or country','error'), 403) : redirect()->route('dos.security');
                }
                else{
                    /** ip and country block check */
                    $ipinfo         = get_ip_info();
                    $ipAddress      = get_real_ip();
                    $country        = Country::insertOrupdtae($ipinfo);
                    $ip             = Visitor::insertOrupdtae($ipAddress,$ipinfo,$country);
                    if(($country && $country->is_blocked == StatusEnum::true->status()) || $ip->is_blocked == StatusEnum::true->status())
                    {
                        return ($request->expectsJson() || $request->isXmlHttpRequest()) ? response()->json(response_status('Unauthorized ip or country','error'), 403) : redirect()->route('error',t2k("Unauthorized","-"));
                    }
                }
        
            return $next($request);
           
        } catch (\Exception $ex) {
            
        }
        return $next($request);
    }
}
