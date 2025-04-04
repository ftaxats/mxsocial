<?php

namespace App\Http\Middleware;

use App\Enums\StatusEnum;
use Closure;
use Illuminate\Http\Request;
use Predis\Response\Status;
use Symfony\Component\HttpFoundation\Response;

class KycMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {

            $user = auth_user('web');
            if(site_settings('kyc_verification') == StatusEnum::true->status() && $user->is_kyc_verified ==  StatusEnum::false->status()) {
                        return redirect()->route('user.kyc.form')
                                           ->with(response_status("Please apply for KYC verification",'error'));
            }

          

            return $next($request);
        } catch (\Exception $ex) {
        
        }
        return $next($request);
    }
}
