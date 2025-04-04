<?php

namespace App\Http\Middleware;

use App\Enums\StatusEnum;
use App\Models\Admin\Currency;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CurrencySwitcher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        try {
            
            if (!session()->has('currency')) {
                $currency = Currency::where('default', StatusEnum::true->status())->first();
              
                session()->put('currency', $currency);
            }
            


        } catch (\Exception $ex) {
        
        }
        return $next($request);
    }
}
