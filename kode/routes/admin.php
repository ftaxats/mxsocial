<?php

use App\Http\Controllers\Admin\AiTemplateController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\CannedContentController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommunicationsController;
use App\Http\Controllers\Admin\FrontendManageController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\ActivityHistoryController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\MailGatewayController;
use App\Http\Controllers\Admin\MenuController;

use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SmsGatewayController as AdminSmsGatewayController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TemplateController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WithdrawController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\PlatformController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\SocialAccountController;
use App\Http\Controllers\Admin\SocialPostController;
use App\Http\Controllers\SystemUpdateController;
use Illuminate\Support\Facades\Route;



$hitLimit = 500;
try {
    $hitLimit = site_settings('web_route_rate_limit');
} catch (\Throwable $th) {

}


Route::middleware([

                               'firewall.xss',
                            //    'firewall.agent' ,
                            //    'firewall.php' ,
                            //    'firewall.bot' ,
                            //    'firewall.geo' ,
                            //    'firewall.ip',
                            //    'firewall.lfi',
                            //    'firewall.referrer',
                            //    'firewall.session',
                            //    'firewall.sqli',
                            //    'firewall.swear',
                            //    'firewall.url',
                               'sanitizer',
                               'https',
                               "throttle:$hitLimit,1",
                               'demo'

                               ])->prefix('admin')->name('admin.')->group(function () use( $hitLimit){

     #guest admin route start here
	Route::middleware(['guest:admin'])->group(function () {
          #login route
          Route::controller(LoginController::class)->group(function () {
               Route::get('/', 'login')->name('login');
               Route::post('/authenticate', 'authenticate')->name('authenticate');
          });

            #password route
          Route::controller(NewPasswordController::class)->name('password.')->group(function () {
               Route::get('forgot-password', 'create')->name('request');
               Route::post('password/email','store')->name('email');
               Route::get('password/verify','verify')->name('verify');
               Route::post('password/verify/code','verifyCode')->name('verify.code');
               Route::get('password/reset', 'resetPassword')->name('reset');
               Route::post('password/update', 'updatePassword')->name('update.request');
          });

     });


	Route::middleware(['auth:admin','demo','admin.verified',"throttle:$hitLimit,1"])->group(function () {

          Route::controller(LoginController::class)->group(function () {
               Route::get('/logout', 'logout')->name('logout');
          });

          #home & profile route refactored
          Route::controller(HomeController::class)->group(function(){

               Route::any('dashboard','home')->name('home');
               Route::prefix('profile')->name('profile.')->group(function () {
                    Route::get('/','profile')->name('index');
                    Route::post('/update', 'profileUpdate')->name('update');
               });

               Route::prefix('passwords')->name('password.')->group(function () {
                    Route::post('/update', 'passwordUpdate')->name('update');
               });

               Route::get('/notifications','notification')->name('notifications');
               Route::post('/read-notification','readNotification')->name('read.notification');
          });

          #withdraw section
          Route::controller(WithdrawController::class)->prefix("/withdraw-method")->name('withdraw.')->group(function (){

               Route::get('/list', 'list')->name('list');
               Route::get('/create','create')->name('create');
               Route::post('/store','store')->name('store');
               Route::post('/update','update')->name('update');
               Route::post('/update/status','updateStatus')->name('update.status');
               Route::get('/edit/{uid}','edit')->name('edit');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::get('/destroy/{id}','destroy')->name('destroy');
               Route::post('configuration','configuration')->name('configuration');

          });

          #currency section refactored
          Route::controller(CurrencyController::class)->prefix("/currency")->name('currency.')->group(function (){

               Route::get('/list', 'list')->name('list');
               Route::post('/store','store')->name('store');
               Route::post('/update','update')->name('update');
               Route::post('/update/status','updateStatus')->name('update.status');
               Route::get('/make/default/{uid}','setDefault')->name('make.default');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::post('/currency-config','currencyConfig')->name('config');
               Route::get('/destroy/{id}','destroy')->name('destroy');

          });

          #staff section refactored
          Route::controller(StaffController::class)->prefix("/staff")->name('staff.')->group(function(){

               Route::get('/list','list')->name('list');
               Route::get('/recycle/list','list')->name('recycle.list');
               Route::post('/store','store')->name('store');
               Route::post('/update','update')->name('update');
               Route::post('/update/status','updateStatus')->name('update.status');
               Route::post('/update/password','updatePassword')->name('update.password');
               Route::get('/destroy/{uid}','destroy')->name('destroy');
               Route::get('/restore/{uid}','restore')->name('restore');
               Route::get('/permanent-destroy/{uid}','permanentDestroy')->name('permanent.destroy');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::get('/login/{uid}','login')->name('login');

          });

          #User
          Route::controller(UserController::class)->prefix("/user")->name('user.')->group(function(){

               Route::get('/list','list')->name('list');
               Route::get('/statistics','statistics')->name('statistics');
               Route::get('/banned','list')->name('banned');
               Route::get('/active','list')->name('active');
               Route::get('/kyc-verified','list')->name('kyc.verfied');
               Route::get('/kyc-banned','list')->name('kyc.banned');
               Route::get('/edit/{uid}','show')->name('edit');
               Route::post('/store','store')->name('store');
               Route::post('/update','update')->name('update');
               Route::post('/update/status','updateStatus')->name('update.status');
               Route::get('/destroy/{uid}','destroy')->name('destroy');
               Route::get('/show/{uid}','show')->name('show');
               Route::get('/login/{uid}','login')->name('login');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::post('/balance','balance')->name('balance');
               Route::post('/subscription','subscription')->name('subscription');
               Route::get('/select/search','selectSearch')->name('selectSearch');


          });

          #Role section refactored
          Route::controller(RoleController::class)->prefix("/role")->name('role.')->group(function(){

               Route::get('/list','list')->name('list');
               Route::get('/create','create')->name('create');
               Route::post('/store','store')->name('store');
               Route::get('/edit/{uid}','edit')->name('edit');
               Route::post('/update','update')->name('update');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::post('/update/status','updateStatus')->name('update.status');
               Route::get('/destroy/{uid}','destroy')->name('destroy');

          });


          #Payment methods refactored
          Route::controller(PaymentMethodController::class)->prefix("/payment-method")->name('paymentMethod.')->group(function(){

               Route::get('/{type}/list','list')->name('list');
               Route::get('/{type}/edit/{uid}','edit')->name('edit');
               Route::get('/{type}/create','create')->name('create');
               Route::post('/{type}/store','store')->name('store');
               Route::post('/{type}/update','update')->name('update');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::post('/update/status','updateStatus')->name('update.status');
               Route::get('/destroy/{uid}','destroy')->name('destroy');

          });


          #Template section refactored
          Route::controller(TemplateController::class)->prefix("/notification-template")->name('template.')->group(function(){

               Route::get('/list','list')->name('list');
               Route::get('/edit/{uid}','edit')->name('edit');
               Route::post('/update','update')->name('update');
               Route::get('/global','global')->name('global');
               Route::post('/global-update','globalUpdate')->name('global.update');

          });

          #Sms gateway list refactored
          Route::controller(AdminSmsGatewayController::class)->prefix("/sms-gateway")->name('smsGateway.')->group(function(){

               Route::get('/list','list')->name('list');
               Route::get('/edit/{uid}','edit')->name('edit');
               Route::post('/update','update')->name('update');
               Route::post('/update/status','updateStatus')->name('update.status');

          });

          #Email gateway refactored
          Route::controller(MailGatewayController::class)->prefix("/mail-gateway")->name('mailGateway.')->group(function(){

               Route::get('/list','list')->name('list');
               Route::get('/edit/{uid}','edit')->name('edit');
               Route::post('/update','update')->name('update');
               Route::post('/update/status','updateStatus')->name('update.status');
               Route::post('/test','test')->name('test');


          });


          #Language section refactored
          Route::controller(LanguageController::class)->prefix("/language")->name('language.')->group(function(){

               Route::get('/list','list')->name('list');
               Route::post('/store','store')->name('store');
               Route::post('/update/status','updateStatus')->name('update.status');
               Route::get('/make/default/{uid}','setDefaultLang')->name('make.default');
               Route::get('/destroy/{uid}','destroy')->name('destroy');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::get('translate/{code}','translate')->name('translate');
               Route::post('translate-key','tranlateKey')->name('tranlateKey');
               Route::get('destroy/translate-key/{id}','destroyTranslateKey')->name('destroy.key');

          });


          #predefined content
          Route::controller(CannedContentController::class)->prefix("/predefined-content")->name('content.')->group(function(){

               Route::get('/list', 'list')->name('list');
               Route::post('/store','store')->name('store');
               Route::post('/update','update')->name('update');
               Route::post('/update/status','updateStatus')->name('update.status');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::get('/destroy/{id}','destroy')->name('destroy');

          });


          #aitemplate section refactored
          Route::controller(AiTemplateController::class)->prefix("/ai-template")->name('ai.template.')->group(function(){

               Route::get('/list','list')->name('list');
               Route::get('/default/list','list')->name('default');
               Route::get('/create','create')->name('create');
               Route::get('/generate-content/{uid}','content')->name('content');
               Route::post('/generate-content','contentGenrate')->name('content.generate');
               Route::post('/store','store')->name('store');
               Route::post('/update','update')->name('update');
               Route::get('/edit/{uid}','edit')->name('edit');
               Route::post('/update/status','updateStatus')->name('update.status');
               Route::get('/destroy/{uid}','destroy')->name('destroy');
               Route::post('/bulk/action','bulk')->name('bulk');

          });


          #General Setting refactored
          Route::controller(SettingController::class)->prefix('settings')->name('setting.')->group(function () {

               Route::get('/', 'list')->name('list');
               Route::post('/store', 'store')->name('store');
               Route::post('/plugin/store', 'pluginSetting')->name('plugin.store');
               Route::post('/logo/store', 'logoSetting')->name('logo.store');
               Route::post('/update/status', 'updateStatus')->name('update.status');
               Route::get('/cache/clear', 'cacheClear')->name('cache.clear');
               Route::prefix('configurations')->name('configuration.')->group(function () {
                  Route::get('/', 'systemConfiguration')->name('index');
               });

               Route::get('server/info','serverInfo')->name('server.info');
               Route::post('/ticket/store', 'ticketSetting')->name('ticket.store');
               #ai config route
               Route::get('/open-ai', 'openAi')->name('openAi');
               Route::get('/webhook', 'webhook')->name('webhook');

               #kyc config
               Route::get('/kyc-configuration', 'kycConfig')->name('kyc');
               Route::post('/kyc-configuration/store', 'kycSetting')->name('kyc.store');

               #affiliate config
               Route::get('/affiliate/configurations', 'affiliate')->name('affiliate');



           });

          #Category section refactored
          Route::controller(CategoryController::class)->prefix("category")->name('category.')->group(function(){

               Route::get('/list','list')->name('list');
               Route::get('/sub-categories','list')->name('subcategories');
               Route::get('/create','create')->name('create');
               Route::post('/store','store')->name('store');
               Route::get('/edit/{uid}','edit')->name('edit');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::post('/update','update')->name('update');
               Route::post('/update/status','updateStatus')->name('update.status');
               Route::get('/destroy/{id}','destroy')->name('destroy');

          });

          #Article section
          Route::controller(BlogController::class)->prefix("/blog")->name('blog.')->group(function(){

               Route::get('/list','list')->name('list');
               Route::get('/create','create')->name('create');
               Route::post('/store','store')->name('store');
               Route::get('/edit/{uid}','edit')->name('edit');
               Route::post('/update','update')->name('update');
               Route::post('/update/status','updateStatus')->name('update.status');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::get('/destroy/{uid}','destroy')->name('destroy');

          });

          #menu section refactore
          Route::controller(MenuController::class)->prefix("/menu")->name('menu.')->group(function(){

               Route::get('/list','list')->name('list');
               Route::get('/edit/{uid}','edit')->name('edit');
               Route::post('/store','store')->name('store');
               Route::post('/seo/update','seoUpdate')->name('seo.update');
               Route::post('/update','update')->name('update');
               Route::post('/update/status','updateStatus')->name('update.status');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::get('/destroy/{id}','destroy')->name('destroy');

          });

          #Page section refactored
          Route::controller(PageController::class)->prefix("/page")->name('page.')->group(function(){

               Route::get('/list','list')->name('list');
               Route::get('/create','create')->name('create');
               Route::post('/store','store')->name('store');
               Route::get('/edit/{uid}','edit')->name('edit');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::post('/update','update')->name('update');
               Route::post('/update/status','updateStatus')->name('update.status');
               Route::get('/destroy/{id}','destroy')->name('destroy');

          });

          #Appearance section refactored
          Route::controller(FrontendManageController::class)->prefix("/appearance")->name('appearance.')->group(function(){

               Route::get('/{key}/{parent?}','list')->name('list');
               Route::post('/update','update')->name('update');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::post('/update/status','updateStatus')->name('update.status');

               Route::get('/destroy/{uid}/section','destroy')->name('destroy');

          });


          #Platform section refactores
          Route::controller(PlatformController::class)->prefix("/platform")->name('platform.')->group(function(){

               Route::get('/list','list')->name('list');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::post('/update','update')->name('update');
               Route::post('/configuration/update','configurationUpdate')->name('configuration.update');
               Route::post('/update/status','updateStatus')->name('update.status');

          });


          #security section refactored
          Route::controller(SecurityController::class)->prefix("/security")->name('security.')->group(function(){

               #country section
               Route::prefix("/countries")->name('country.')->group(function(){

                    Route::get('/list','countries')->name('list');
                    Route::post('/status-update','countryStatus')->name('update.status');
                    Route::post('/bulk/action','countryBulk')->name('bulk');
               });

               #ip section
               Route::prefix("/ip")->name('ip.')->group(function(){

                    Route::get('/list','ipList')->name('list');
                    Route::post('/store','ipStore')->name('store');
                    Route::post('/update','ipUpdate')->name('update');
                    Route::post('/status-update','ipStatus')->name('update.status');
                    Route::post('/bulk/action','ipBulk')->name('bulk');
                    Route::get('/destroy/{id}','ipDestroy')->name('destroy');

               });

               #dos security
               Route::get('/dos','dos')->name('dos');
               Route::post('/dos/update','dosUpdate')->name('dos.update');


          });

          #Communication Route
          Route::controller(CommunicationsController::class)->group(function(){

               /** contacts route */
               Route::get('/contacts','contacts')->name('contact.list');
               Route::get('/destroy/{uid}','destroy')->name('contact.destroy');
               Route::post('contact/bulk/action','bulkContactDestroey')->name('contact.bulk');


               /** subscription route */
               Route::get('/subscribers','subscribers')->name('subscriber.list');
               Route::get('/subscriber/destroy/{uid}','destroySubscriber')->name('subscriber.destroy');
               Route::post('subscriber/bulk/action','bulkSubscriberDestory')->name('subscriber.bulk');


               /** mail sending route */
               Route::post('/send-email','sendMail')->name('send.mail');
               Route::post('/send-email-all','sendMailSubscriber')->name('send.mail.all');

          });

          #Package section refactored
          Route::controller(PackageController::class)->prefix("/subscription-package")->name('subscription.package.')->group(function(){

               Route::get('/list','list')->name('list');
               Route::get('/create','create')->name('create');
               Route::post('/store','store')->name('store');
               Route::get('/edit/{uid}','edit')->name('edit');
               Route::post('/update','update')->name('update');
               Route::post('/update/status','updateStatus')->name('update.status');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::get('/destroy/{id}','destroy')->name('destroy');
               Route::post('/configuration','configuration')->name('configuration');
               Route::get('/select/search','selectSearch')->name('selectSearch');

          });

          #log section refcatored
          Route::controller(ActivityHistoryController::class)->group(function(){

               #word usage history and report
               Route::prefix("/template-usages/reports")->name('template.report.')->group(function(){
                    Route::get('/','templateReport')->name('list');
                    Route::get('/destroy/{id}','templateReportdestroy')->name('destroy');
               });

               #credit reports
               Route::prefix("/credit/reports")->name('credit.report.')->group(function(){
                    Route::get('/','creditReport')->name('list');
                    Route::get('/destroy/{id}','creditReportdestroy')->name('destroy');
                    Route::post('/bulk/action','creditReportBulk')->name('bulk');
               });

               #transaction report
               Route::prefix("/transaction/reports")->name('transaction.report.')->group(function(){

                    Route::get('/','transactionReport')->name('list');
                    Route::post('/bulk/action','transactionBulk')->name('bulk');
                    Route::get('/destroy/{id}','destroyTransaction')->name('destroy');

               });

               #subscriptioon report
               Route::prefix("/subscription/reports")->name('subscription.report.')->group(function(){
                    Route::get('/','subscriptionReport')->name('list');
                    Route::post('/update','updateSubscription')->name('update');

               });

               #deposit report
               Route::prefix("/deposit/reports")->name('deposit.report.')->group(function(){

                    Route::get('/','depositReport')->name('list');
                    Route::get('/details/{id}','depositDetails')->name('details');
                    Route::post('/update','updateDeposit')->name('update');
               });

               #withdraw reports
               Route::prefix("/withdraw/reports")->name('withdraw.report.')->group(function(){
                    Route::get('/','withdrawReport')->name('list');
                    Route::get('/details/{id}','withdrawDetails')->name('details');
                    Route::post('/update','withdrawUpdate')->name('update');
               });

               #affiliate reports
               Route::prefix("/affiliate/reports")->name('affiliate.report.')->group(function(){
                    Route::get('/','affiliateReport')->name('list');
               });


               #kyc reports
               Route::prefix("/kyc/reports")->name('kyc.report.')->group(function(){
                    Route::get('/','kycReport')->name('list');
                    Route::get('/details/{id}','kycDetails')->name('details');
                    Route::post('/update','kycUpdate')->name('update');

               });

               #kyc reports
               Route::prefix("/webhook/reports")->name('webhook.report.')->group(function(){
                    Route::get('/','webhookReport')->name('list');
                    Route::get('/destroy/{id}','destroyWebhook')->name('destroy');
               });

          });

          #support route
          Route::controller(TicketController::class)->name('ticket.')->prefix('ticket/')->group(function () {

               Route::any('/list','list')->name('list');
               Route::get('/create','create')->name('create');
               Route::post('/store','store')->name('store');
               Route::get('/reply/{ticket_number}','show')->name('show');
               Route::post('/reply/store','reply')->name('reply');
               Route::post('/file/download','download')->name('file.download');
               Route::get('/destroy/{id}','destroy')->name('destroy');
               Route::post('/update','update')->name('update');
               Route::get('/destroy/message/{id}','destroyMessage')->name('destroy.message');
               Route::post('/bulk/action','bulk')->name('bulk');
               Route::get('/destroy/file/{id}','destroyFile')->name('destroy.file');

          });

          #social account and post route
          Route::name('social.')->prefix('social/')->group(function () {

               #Account manager
               Route::controller(SocialAccountController::class)->name('account.')->prefix('account/')->group(function () {

                    Route::any('/list','list')->name('list');
                    Route::get('/create/{platform}','create')->name('create');
                    Route::post('/store','store')->name('store');
                    Route::post('/reconnect','reconnect')->name('reconnect');
                    Route::get('/edit/{uid}','edit')->name('edit');
                    Route::post('/update','update')->name('update');
                    Route::post('/update/status','updateStatus')->name('update.status');
                    Route::post('/bulk/action','bulk')->name('bulk');
                    Route::get('/destroy/{id}','destroy')->name('destroy');
                    Route::get('/show/{uid}','show')->name('show');

               });


               #Post manager
               Route::controller(SocialPostController::class)->name('post.')->prefix('post/')->group(function () {

                    Route::any('/list','list')->name('list');
                    Route::any('/analytics/dashboard','analytics')->name('analytics');
                    Route::get('/create','create')->name('create');
                    Route::post('/store','store')->name('store');
                    Route::get('/send/{uid}', 'send')->name('send');
                    Route::get('/destroy/{id}','destroy')->name('destroy');
                    Route::get('/show/{uid}','show')->name('show');

               });

          });

          /** system update */
          Route::controller(SystemUpdateController::class)->name('system.')->prefix('system/')->group(function () {
               Route::any('/update/init','init')->name('update.init');
               Route::post('/update','update')->name('update');

          });



	});


});










