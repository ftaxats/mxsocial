<?php

namespace App\Http\Middleware;

use App\Enums\StatusEnum;
use Closure;
use Illuminate\Http\Request;
use Predis\Response\Status;
use Symfony\Component\HttpFoundation\Response;

class HttpsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if(site_settings('force_ssl') == StatusEnum::true->status() && $request->secure()) \URL::forceScheme('https');  
             return $next($request);
            
        } catch (\Throwable $th) {
         
        }
        return $next($request);
    }
}
