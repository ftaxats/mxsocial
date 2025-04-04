<?php

namespace App\Http\Middleware;

use App\Enums\StatusEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        try {
     
            if(site_settings('maintenance_mode') == (StatusEnum::true)->status() )  return redirect()->route('maintenance.mode');
            return $next($request);

        } catch (\Exception $ex) {
            
        }
        return $next($request);
    }
}
