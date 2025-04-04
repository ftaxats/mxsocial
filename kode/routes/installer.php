<?php

use App\Http\Controllers\InstallerController;
use App\Http\Middleware\CurrencySwitcher;
use App\Http\Middleware\HttpsMiddleware;
use App\Http\Middleware\LanguageMiddleware;
use App\Http\Middleware\PurchaseValidation;
use App\Http\Middleware\SoftwareVerification;
use Illuminate\Support\Facades\Route;


    #Install
    Route::controller(InstallerController::class)->prefix("/install")->name('install.')
     ->middleware(['sanitizer'])
     ->withoutMiddleware([SoftwareVerification::class,LanguageMiddleware::class,CurrencySwitcher::class,HttpsMiddleware::class,PurchaseValidation::class])->group(function(){

        Route::get('/','init')->name('init');
        Route::get('/requirement-verification','requirementVerification')->name('requirement.verification');
        Route::get('/envato-verification','envatoVerification')->name('envato.verification');
        Route::post('/purchase-code/verification','purchaseVerification')->name('purchase.code.verification');
        Route::get('/db-setup','dbSetup')->name('db.setup');
        Route::post('/db-store','dbStore')->name('db.store');
        Route::get('account/setup','accountSetup')->name('account.setup');
        Route::post('account/setup/store','accountSetupStore')->name('account.setup.store');
        Route::get('setup-finished','setupFinished')->name('setup.finished');

   });



   Route::get('invalid-license',[InstallerController::class ,'invalidPurchase'])->name('invalid.purchase')->middleware(['sanitizer','firewall.all'])
   ->withoutMiddleware([LanguageMiddleware::class,HttpsMiddleware::class,PurchaseValidation::class]);

   Route::post('verify-purchase',[InstallerController::class ,'verifyPurchase'])->name('verify.puchase')->middleware(['sanitizer','firewall.all'])
   ->withoutMiddleware([LanguageMiddleware::class,HttpsMiddleware::class,PurchaseValidation::class]);

