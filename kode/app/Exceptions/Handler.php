<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Sentry\State\Scope;
use App\Traits\InstallerManager;
class Handler extends ExceptionHandler
{

    use InstallerManager;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry') && env("APP_DEBUG")) {
                app('sentry')->captureException($e);
            }
        });
    }




    /**
         * Render an exception into an HTTP response.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \Throwable  $exception
         * @return \Symfony\Component\HttpFoundation\Response
         *
         * @throws \Throwable
         */
        public function render($request, Throwable $exception)
        {

            if(!$this->is_installed())return redirect()->route('install.init');   
            
            
            return parent::render($request, $exception);

        }

}



