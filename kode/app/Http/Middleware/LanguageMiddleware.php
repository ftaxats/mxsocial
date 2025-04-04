<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use App\Enums\StatusEnum;
use App\Models\Core\Language;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {

            if(!session()->has('locale')){
                $locale = (Language::where('is_default',(StatusEnum::true)->status())->first())->code;
                session()->put('locale', $locale);
            }else{
                $locale =  session()->get('locale');
            }
           
            App::setLocale($locale);

        } catch (\Exception $ex) {
        
        }

        return $next($request);
    }
}
