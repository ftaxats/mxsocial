<?php

namespace App\Providers;

use App\Enums\StatusEnum;
use App\Models\Country;
use App\Models\Visitor;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {

        $this->configureRateLimiting();
 
        $this->routes(function () {

            Route::middleware('web')
            ->group(base_path('routes/installer.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/payment.php'));

            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
                Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting() :void
    {


        try {
            
            $hitLimit = site_settings('api_route_rate_limit');

            RateLimiter::for('api', function (Request $request) use($hitLimit) {
      
                return Limit::perMinute($hitLimit)->by($request->user()?->id ?: $request->ip());
            });

            RateLimiter::for('refresh', function(Request $request){

                if(site_settings('dos_prevent') == StatusEnum::true->status()){
             
                    $key          = 'dos.'.get_real_ip(); 
                    $maxAttempt   = (int) site_settings("dos_attempts");
                    $sec          = (int) site_settings("dos_attempts_in_second");
                    if(RateLimiter::tooManyAttempts($key,$maxAttempt)){

                        $ipinfo         = get_ip_info();
                        $ipAddress      = get_real_ip();
                        $country        = Country::insertOrupdtae($ipinfo);
                        $ip             = Visitor::insertOrupdtae($ipAddress,$ipinfo,$country);
                        if(site_settings("dos_security") == 'block_ip'){
                            $ip->is_blocked = StatusEnum::true->status();
                            $ip->save();
                        }
                        else{
                            session()->forget('dos_captcha');
                            session()->put("security_captcha",true);
                        }
                        
                    }
                    else{
                        RateLimiter::hit($key,$sec);
                    }
                }
            });



        } catch (\Throwable $th) {
        }

    }
}
