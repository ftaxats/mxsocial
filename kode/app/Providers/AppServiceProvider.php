<?php

namespace App\Providers;

use App\Enums\MenuVisibilty;


use App\Models\Admin\Menu;
use App\Models\Admin\Page;

use App\Models\Core\Language;

use App\Models\KycLog;
use App\Models\PaymentLog;
use App\Models\Ticket;
use App\Models\WithdrawLog;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    
    /**
     * Register any application services.
     */
    public function register(): void
    {
 

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {

            $this->app->booted(function () {
                config(['firewall.responses.block.redirect' => route('access.denied')]);
            });
            
            Paginator::useBootstrap();
            
            if(env("APP_DEBUG")){
                Config::set('sentry',[
                    'dns' => site_settings('sentry_dns')
                ]);
            }
        
    
            view()->composer('admin.partials.sidebar', function ($view)  {

                $view->with([
                    'pending_deposits'      => PaymentLog::pending()->count(),
                    'pending_tickets'       => Ticket::pending()->count(),
                    'pending_withdraws'     => WithdrawLog::pending()->count(),
                    'pending_kycs'          => KycLog::pending()->count(),
                ]);
            });


            view()->composer('frontend.partials.header', function ($view)  {

                $view->with([
                    'menus'      => getCachedMenus()
                                              ->whereIn('menu_visibility',[(string)MenuVisibilty::BOTH->value,(string)MenuVisibilty::HEADER->value]),

                    'pages'      => Page::active()
                                          ->orderBy('serial_id')
                                          ->header()
                                          ->get(),
                ]);
            });




        
            view()->composer('frontend.partials.footer', function ($view)  {
                
                $view->with([
                    'menus'      => getCachedMenus()
                                        ->whereIn('menu_visibility',[(string)MenuVisibilty::BOTH->value ,(string) MenuVisibilty::FOOTER->value ]),

                    'pages'      => Page::active()
                                        ->orderBy('serial_id')
                                        ->footer()
                                        ->get(),
                ]);
            });



            view()->share([
                'languages'       => Language::active()->get(),
            ]);
            
        } catch (\Throwable $th) {
        
        }
    }
}
