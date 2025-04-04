<?php

namespace App\Http;

use App\Http\Middleware\AdminVerification;
use App\Http\Middleware\AuthorizationMiddleware;
use App\Http\Middleware\CurrencySwitcher;
use App\Http\Middleware\DemoMode;
use App\Http\Middleware\HttpsMiddleware;
use App\Http\Middleware\KycMiddleware;
use App\Http\Middleware\LanguageMiddleware;
use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\Permissions;
use App\Http\Middleware\PurchaseValidation;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use App\Http\Middleware\Sanitization;
use App\Http\Middleware\SecurityMiddleware;
use App\Http\Middleware\SoftwareVerification;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,

    ];


    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [

            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // \Spatie\Csp\AddCspHeaders::class,
            LanguageMiddleware::class,
            CurrencySwitcher::class,
            SoftwareVerification::class,
            PurchaseValidation::class


        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'refresh' => [
            'throttle:refresh',
            'bindings',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        'permissions' => Permissions::class,

        'sanitizer' => Sanitization::class,
        'user.verified' => AuthorizationMiddleware::class,
        'demo' => DemoMode::class,

        'admin.verified' => AdminVerification::class,
        'https' => HttpsMiddleware::class,
        'dos.security' => SecurityMiddleware::class,
        'kyc' => KycMiddleware::class,
        'maintenance.mode' => MaintenanceMode::class

    ];
}
