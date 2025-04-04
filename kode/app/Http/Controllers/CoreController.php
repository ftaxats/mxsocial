<?php

namespace App\Http\Controllers;

use App\Enums\AccountType;
use App\Enums\ConnectionType;
use App\Enums\PostStatus;
use App\Enums\StatusEnum;
use App\Enums\SubscriptionStatus;
use App\Http\Services\Account\facebook\Account;
use App\Http\Services\Account\instagram\Account as InstagramAccount;
use App\Http\Services\Account\linkedin\Account as LinkedinAccount;
use App\Http\Services\Account\twitter\Account as TwitterAccount;
use App\Http\Services\Account\tiktok\Account as TiktokAccount;
use App\Http\Services\Account\youtube\Account as YoutubeAccount;
use App\Http\Services\UserService;
use App\Http\Utility\SendNotification;
use App\Http\Utility\SendSMS;
use App\Jobs\SendMailJob;
use App\Jobs\SendSmsJob;
use App\Models\Admin\Category;
use App\Models\Admin\Currency;
use App\Models\AiTemplate;
use App\Models\Core\Language;
use App\Models\Core\Setting;
use App\Models\MediaPlatform;
use App\Models\Package;
use App\Models\PostWebhookLog;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\Subscriber;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Support\Facades\Session;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Intervention\Image\Facades\Image;
use App\Traits\PostManager;
use App\Traits\AccountManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\View\View as ViewView;

class CoreController extends Controller
{



    use AccountManager, PostManager;






    public function accessDenied(): ViewView
    {

        return view('access_denied');

    }


    /**
     * change  language
     *
     * @param string $code
     * @return RedirectResponse
     */
    public function languageChange(string $code): RedirectResponse
    {

        if (!Language::where('code', $code)->exists())
            $code = 'en';
        optimize_clear();
        session()->put('locale', $code);
        app()->setLocale($code);

        return back()->with("success", translate('Language Switched Successfully'));
    }



    /**
     * change  language
     *
     * @param string $code
     * @return RedirectResponse
     */
    public function currencyChange(string $code): RedirectResponse
    {
        $currency = Currency::active()->where('code', $code)->firstOrFail();
        session()->put('currency', $currency);
        return back()->with("success", translate('Currency switched to ' . $currency->name));
    }


    /**
     * create default image
     *
     * @param string $size
     * @return Response
     */
    public function defaultImageCreate(string $size): Response
    {



        $width = explode('x', $size)[0];
        $height = explode('x', $size)[1];
        $img = Image::canvas($width, $height, '#ccc');
        $text = $width . 'X' . $height;

        $fontSize = $width > 100 && $height > 100
            ? 60 : 20;


        $img->text($text, $width / 2, $height / 2, function ($font) use ($fontSize) {
            $font->file(realpath('assets/font') . DIRECTORY_SEPARATOR . 'RobotoMono-Regular.ttf');
            $font->color('#000');
            $font->align('center');
            $font->valign('middle');
            $font->size($fontSize);
        });

        return $img->response('png');

    }

    /**
     * @param int|string $randCode
     * @return void
     */
    public function defaultCaptcha(int|string $randCode): void
    {

        $phrase = new PhraseBuilder;
        $code = $phrase->build(4);
        $builder = new CaptchaBuilder($code, $phrase);
        $builder->setBackgroundColor(220, 210, 230);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);
        $builder->build($width = 100, $height = 40, $font = null);
        $phrase = $builder->getPhrase();

        if (Session::has('gcaptcha_code')) {
            Session::forget('gcaptcha_code');
        }
        Session::put('gcaptcha_code', $phrase);
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type:image/jpeg");
        $builder->output();
    }

    public function clear(): RedirectResponse
    {

        optimize_clear();
        return back()->with(response_status("Cache Clean Successfully"));
    }

    /**
     * Process cron job
     *
     * @return void
     */
    public function cron(): void
    {

        try {
            $this->handleSchedulePost();
            $this->handleExpireSubscriptions();
            $this->refreshToken();

        } catch (\Throwable $th) {

        }

        Setting::updateOrInsert(
            ['key' => 'last_cron_run'],
            ['value' => Carbon::now()]
        );



    }



    public function refreshToken(): void
    {


        $dateTime = now()->addDay()->format('Y-m-d h:i:s');


        $accounts = SocialAccount::with(['platform'])
            ->where('access_token_expire_at', '<=', $dateTime)
            ->lazyById(1000, 'id')
            ->each(function (SocialAccount $account) use ($dateTime) {

                $platform = $account?->platform;
                $token = $account?->refresh_token ?? $account?->token;
                if ($platform) {

                    $class = 'App\\Http\\Services\\Account\\' . $platform->slug . '\\Account';

                    $response = $class::refreshAccessToken($platform, $token);



                    if ($response->successful()) {



                        if ($platform->slug == 'facebook' || $platform->slug == 'instagram') {

                            $responseData = $response->json();
                            $accessToken = Arr::get($responseData, 'access_token');

                            $account->token = $accessToken;
                            $account->access_token_expire_at = now()->addMonths(2);

                        } else if ($platform->slug == 'twitter') {


                            $responseData = $response->json();
                            $token = Arr::get($responseData, 'access_token');
                            $refresh_token = Arr::get($responseData, 'refresh_token');

                            $account->token = $token;
                            $account->access_token_expire_at = now()->addMonths(2);
                            $account->refresh_token = $refresh_token;
                            $account->refresh_token_expire_at = now()->addMonths(2);

                        } else if ($platform->slug == 'linkedin') {

                            $responseData = $response->json();

                            $accessToken = Arr::get($responseData, 'access_token');
                            $refreshToken = Arr::get($responseData, 'refresh_token');

                            $account->token = $accessToken;
                            $account->access_token_expire_at = now()->seconds($responseData['expires_in']);
                            $account->refresh_token = $refreshToken;
                            $account->refresh_token_expire_at = now()->seconds($responseData['refresh_token_expires_in']);
                        }
                        $account->save();
                    }
                }

            });



    }



    /**
     * Handle schedule post
     *
     * @return void
     */
    public function handleSchedulePost(): void
    {

        $posts = SocialPost::with(['file'])
            ->postable()
            ->cursor();

        foreach ($posts->chunk(20) as $chunkPosts) {
            foreach ($chunkPosts as $post) {
                sleep(1);

                if (
                    $post->schedule_time <= Carbon::now() ||
                    $post->status == strval(
                        PostStatus::value('PENDING', true)
                    )
                ) {
                    $this->publishPost($post);
                }
            }
        }




    }

    /**
     * Handle expire subscriptions
     *
     *  @return void
     */
    public function handleExpireSubscriptions(): void
    {

        $subscriptions = Subscription::with(['user', 'package'])
            ->running()
            ->expired()
            ->cursor();

        foreach ($subscriptions as $subscription) {

            $subscription->update([
                'status' => SubscriptionStatus::value('EXPIRED', true),
                'expired_at' => date('Y-m-d'),
            ]);


            // inactive  user profile
            (new UserService())->inactiveSocialAccounts($subscription);

            $code = [
                'time' => Carbon::now(),
                'name' => $subscription->package->title,
                'link' => route('user.subscription.report.list'),
                'reason' => translate("Auto renewal package does not exist"),
            ];


            $notificationTypes = [
                "database_notifications" => "App\Http\Utility\SendNotification",
                "email_notifications" => "App\Jobs\SendMailJob",
                "sms_notifications" => "App\Jobs\SendSmsJob",
            ];

            foreach ($notificationTypes as $type => $key) {

                if (notify($type)) {
                    if ($type == "database_notifications") {
                        $key::database_notifications($subscription->user, "SUBSCRIPTION_EXPIRED", $code, Arr::get($code, "link", null));
                    } else {
                        $key::dispatch($subscription->user, 'SUBSCRIPTION_EXPIRED', $code);
                    }
                }
            }

            // Auto-renewal
            if ($subscription->user && $subscription->user->auto_subscription == StatusEnum::true->status()) {

                $getPackageId = site_settings("auto_subscription_package");
                if (site_settings('auto_subscription') == StatusEnum::true->status() && $subscription->user->auto_subscription_by) {
                    $getPackageId = $subscription->user->auto_subscription_by;
                }

                $package = Package::where('id', $getPackageId)->first();

                $flag = 1;
                if ($package) {
                    $userService = new UserService();
                    $response = $userService->createSubscription($subscription->user, $package, translate("Auto Subscription renewal"));
                    $code['reason'] = Arr::get($response, 'message', translate("Auto renewal package doesnot exists"));
                    if (isset($response['status']) && $response['status']) {
                        $flag = 0;
                    }

                }
                if ($flag == 1) {
                    foreach ($notificationTypes as $type => $key) {
                        if (notify($type)) {
                            if ($type == "database_notifications") {
                                $key::database_notifications($subscription->user, "SUBSCRIPTION_FAILED", $code, Arr::get($code, "link", null));
                            } else {
                                $key::dispatch($subscription->user, 'SUBSCRIPTION_FAILED', $code);
                            }
                        }
                    }
                }

            }

        }

    }





    /** security control */
    public function security(): View
    {

        if (
            site_settings('dos_prevent') == StatusEnum::true->status() &&
            !session()->has('dos_captcha')
        ) {
            return view('dos_security', [
                'meta_data' => $this->metaData(["title" => trans('default.too_many_request')]),
            ]);
        }
        abort(403);
    }


    public function securityVerify(Request $request): RedirectResponse
    {


        $request->validate([
            "captcha" => [
                'required',
                function (string $attribute, mixed $value, Closure $fail) {
                    if (strtolower($value) != strtolower(session()->get('gcaptcha_code')))
                        $fail(translate("Invalid captcha code"));
                }
            ]
        ]);

        session()->forget('gcaptcha_code');
        session()->forget('security_captcha');
        session()->put('dos_captcha', $request->input("captcha"));

        $route = 'home';
        if (session()->has('requested_route'))
            $route = session()->get('requested_route');

        return redirect()->route($route);
    }



    public function acceptCookie(Request $request): \Illuminate\Http\Response
    {

        $response = response(["message" => 'Cookie accepted'])->cookie('cookie_consent', 'accepted')->cookie('accepted_at', now());
        $this->saveCookieData($request->cookie());
        return $response;
    }

    public function rejectCookie(Request $request): \Illuminate\Http\Response
    {
        $response = response([
            "message" => 'Cookie rejected',
        ])->cookie(Cookie::forget('cookie_consent'))->cookie(Cookie::forget('accepted_at'));
        $this->saveCookieData($request->cookie());
        return $response;
    }

    public function downloadCookieData(): \Illuminate\Http\Response
    {

        $cookieData = $this->getSavedCookieData();

        $csv = implode(',', array_keys($cookieData)) . PHP_EOL;
        $csv .= implode(',', $cookieData) . PHP_EOL;

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="cookie_data.csv"');
    }

    private function saveCookieData(array $data): void
    {

        session()->put("cookie_consent", true);

        $data = array_merge($data, get_ip_info());
        $folderPath = storage_path('app');
        $filePath = $folderPath . '/cookie_data.json';

        if (!file_exists($folderPath))
            mkdir($folderPath, 0755, true);

        $existingData = [];

        if (!file_exists($filePath)) {
            file_put_contents($filePath, json_encode([]));
        } else {
            $existingData = (array) json_decode(file_get_contents($filePath), true);
        }

        $combinedData = array_merge($existingData, $data);
        file_put_contents($filePath, json_encode($combinedData));
    }

    private function getSavedCookieData(): array
    {
        $path = storage_path('app/cookie_data.json');

        if (file_exists($path))
            return json_decode(file_get_contents($path), true);

        return [];
    }




    public function getSubcategories(int|string $id, bool $html = false): array
    {


        $categories = Category::where('parent_id', $id)
            ->active()->get();

        $options = "<option value=''> Select Subcategory </option>";
        if ($html) {
            foreach ($categories as $category) {
                $options .= '<option value="' . $category->id . '">' . $category->title . '</option>';
            }
        }

        return [

            'status' => true,
            'html' => $options,
            'categories' => $categories->pluck('title', 'id')->toArray(),
        ];

    }




    public function getTemplate(Request $request): array
    {


        $request->validate([
            'category_id' => "required|exists:categories,id",
            'sub_category_id' => "nullable|exists:categories,id",
            'user_id' => 'nullable|exists:users,id'
        ]);

        $flag = false;
        $templateAccess = [];
        if ($request->input('user_id')) {
            $user = User::with(['runningSubscription'])->find($request->input('user_id'));
            if (!$user || !$user->runningSubscription)
                return ['status' => false, 'message' => translate('Invalid User!! No Template Found')];
            $subscription = $user->runningSubscription;
            $templateAccess = $subscription ? (array) subscription_value($subscription, "template_access", true) : [];
            $flag = true;
        }

        $category = Category::template()
            ->doesntHave('parent')
            ->where("id", $request->input("category_id"))->first();


        $templates = AiTemplate::where("category_id", @$category->id)
            ->when($request->input('sub_category_id'), function ($query) use ($request, $category) {
                $subCategory = Category::where("parent_id", $category->id)
                    ->where('id', $request->input('sub_category_id'))
                    ->first();
                $query->where('sub_category_id', @$subCategory->id);
            })->when($flag && count($templateAccess) > 0, function ($query) use ($request, $templateAccess) {
                $query->whereIn('id', $templateAccess);

            })->active()->get();


        return [
            'status' => true,
            'html' => view("partials.ai_template", [
                'templates' => @$templates
            ])->render(),
            'templates' => $templates->pluck('name', 'id')->toArray(),
        ];

    }



    /**
     * Summary of getTemplateCategories
     * @param Request $request
     * @return array
     */
    public function getTemplateCategories(Request $request): array
    {

        $request->validate([
            'category_id' => "nullable|exists:categories,id",
            'parent_id' => "nullable|exists:categories,id",
            'user_id' => 'nullable|exists:users,id'
        ]);


        $categoryId = $request->input('category_id');

        $templateAccess = [];

        if ($request->input('user_id')) {
            $user = User::with(['runningSubscription'])->find($request->input('user_id'));
            if (!$user || !$user->runningSubscription)
                return ['status' => false, 'message' => translate('Invalid User!! No Template Found')];
            $subscription = $user->runningSubscription;
            $templateAccess = $subscription
                ? (array) subscription_value($subscription, "template_access", true)
                : [];

        }

        if (!$categoryId) {
            $templates = AiTemplate::whereIn('id', $templateAccess)->get();
            $accessCategories = (array) @$templates->pluck('category_id')->unique()->toArray();
            $categories = Category::template()
                ->doesntHave('parent')
                ->when(
                    @$user && $accessCategories && count($accessCategories) > 0,

                    function (Builder $q) use ($accessCategories): Builder {
                        return $q->whereIn('id', $accessCategories);
                    }
                )->get();

            return [
                'status' => true,
                'html' => view("partials.template.list", [
                    'categories' => $categories,
                ])->render(),
            ];

        }










        $parentCategory = Category::template()
            ->doesntHave('parent')
            ->where("id", $request->input("parent_id"))->first();


        $category = Category::template()
            ->with("parent")
            ->when($parentCategory, fn(Builder $q): Builder => $q->where('parent_id', $parentCategory->id))
            ->where("id", $categoryId)
            ->first();



        $categories = Category::template()

            ->with('parent')
            ->where("parent_id", $category->id)
            ->get();




        $templates = AiTemplate::query()
            ->when(
                $parentCategory && $category,
                function (Builder $q) use ($parentCategory, $category): Builder {
                    return $q->where('category_id', $parentCategory->id)
                        ->where('sub_category_id', $category->id);

                }
            )
            ->when(!$parentCategory && $category, function (Builder $q) use ($category): Builder {
                return $q->where('category_id', $category->id);

            })
            ->when(
                @$user && $templateAccess && is_array($templateAccess) && count($templateAccess) > 0,

                function (Builder $q) use ($templateAccess): Builder {

                    return $q->whereIn('id', $templateAccess);

                }
            )
            ->active()->get();




        return [
            'status' => true,
            'html' => view("partials.template.list", [
                'custom_templates' => $templates,
                'template_category' => $category,
                'categories' => $categories,
            ])->render(),

        ];


    }


    public function templateConfig(): array
    {

        $template = AiTemplate::with(['category', 'subCategory'])
            ->active()->where('id', request()->input('template_id'))
            ->first();


        $html = '';
        $message = translate('You dont have access to this template');
        $flag = true;



        if (request()->input("user_id") && $template) {
            $user = auth_user('web')->load(['runningSubscription']);


            $flag = false;

            if (
                $user
                && $user->runningSubscription
                && in_array($template->id, (array) subscription_value($user->runningSubscription, "template_access", true))
            ) {


                $flag = true;
            }


        }


        $languages = Language::active()->get();
        if ($flag) {
            if ($template) {
                $html = view("partials.ai_template_input", [
                    'template' => @$template,
                    'languages' => @$languages,
                    'user' => @$user,
                    'is_user_request' => request()->input("user_id") ? true : false,

                ])->render();


                return [
                    'status' => $flag,
                    "html" => $html,
                    "message" => $message,
                ];

            }


            return [
                'status' => $flag,
                "html" => view("partials.ai_template_input", [
                    'languages' => @$languages,
                    'user' => @$user,
                    'is_user_request' => request()->input("user_id") ? true : false,

                ])->render(),
                "message" => $message,
            ];


        }


        return [
            'status' => $flag,
            "html" => $html,
            "message" => $message,

        ];

    }


    /**
     * @param Request $request
     * @param string $guard
     * @param string $medium
     * @param string|null $type
     * @return mixed
     */
    public function redirectAccount(Request $request, string $guard, string $medium, string $type = null): mixed
    {


        session()->put("guard", $guard);

        $platform = MediaPlatform::where('slug', $medium)->firstOrfail();

        switch ($platform->slug) {
            case 'facebook':
                return redirect(Account::authRedirect($platform));
            case 'instagram':
                return redirect(InstagramAccount::authRedirect($platform));
            case 'twitter':
                return redirect(TwitterAccount::authRedirect($platform));
            case 'linkedin':
                return redirect(LinkedinAccount::authRedirect($platform));
            case 'tiktok':
                return redirect(TiktokAccount::authRedirect($platform));
            case 'youtube':
                return redirect(YoutubeAccount::authRedirect($platform));

            default:

                break;
        }


    }


    /**
     * @return RedirectResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function handleAccountCallback($medium = null): RedirectResponse
    {
        try {
            $platform   = request()->input('medium') ?? $medium;
            $code       = request()->input('code');
            $guard      = session()->get('guard');
            if (!$guard || !auth()->guard($guard)->check())
                abort(403, "Unauthenticated user request");
            if (!$code)
                abort(403, "Something went wrong, please try again.");
            $platform = MediaPlatform::where('slug', $platform)->firstOrfail();
            $routeName = $guard == 'admin' ? "admin.social.account.list" : "user.social.account.list";

            switch ($platform->slug) {

                case 'facebook':
                    $token = Account::getAccessToken($code, $platform)->throw()->json('access_token');
                    $pages = Account::getPagesInfo(
                        ['name,username,picture,access_token'],
                        $platform,
                        $token
                    )
                        ->throw()
                        ->json('data');

                    Account::saveFbAccount($pages, $guard, $platform, AccountType::PAGE->value, ConnectionType::OFFICIAL->value);
                    return redirect()->route($routeName, ['platform' => $platform->slug])
                        ->with(response_status("Account Added"));

                case 'instagram':



                    $token = InstagramAccount::getAccessToken($code, $platform)->throw()->json('access_token');
                    $pages = InstagramAccount::getAccounts(
                        ['connected_instagram_account,name,access_token'],
                        $platform,
                        $token
                    )
                        ->throw()
                        ->json('data');




                    InstagramAccount::saveIgAccount(
                        $pages,
                        $guard,
                        $platform,
                        AccountType::PAGE->value,
                        ConnectionType::OFFICIAL->value,
                        $token,
                    );

                    return redirect()->route($routeName, ['platform' => $platform->slug])
                        ->with(response_status("Account Added"));



                case 'twitter':


                    $response = TwitterAccount::getAccessToken($code, $platform)->throw();

                    $token = Arr::get($response, 'access_token');

                    TwitterAccount::saveTwAccount(
                        $response,

                        $guard,
                        $platform,
                        AccountType::PROFILE->value,
                        ConnectionType::OFFICIAL->value,
                    );




                    return redirect()->route($routeName, ['platform' => $platform->slug])
                        ->with(response_status("Account Added"));


                case 'linkedin':

                    $getAccessTokenResponse = LinkedinAccount::getAccessToken($code, $platform);
                    $tokenResponse = $getAccessTokenResponse->json();


                    $accessToken = @$tokenResponse['access_token'] ?? null;


                    if ($getAccessTokenResponse->failed() || !$accessToken) {

                        return redirect()->route($routeName, )
                            ->with(response_status('Failed to connect', 'error'));
                    }

                    $tokenExpireIn = @$tokenResponse['expires_in'] ?? null;


                    $linkedInAccount = LinkedinAccount::getAccount($accessToken, $platform);

                    if ($linkedInAccount->failed()) {

                        return redirect()->route($routeName, )
                            ->with(response_status('Failed to connect', 'error'));

                    }

                    $user = $linkedInAccount->json();


                    LinkedinAccount::saveLdAccount(
                        $user,
                        $guard,
                        $platform,
                        AccountType::PROFILE->value,
                        ConnectionType::OFFICIAL->value,
                        $accessToken,
                        $tokenExpireIn
                    );




                    return redirect()->route($routeName, ['platform' => $platform->slug])
                        ->with(response_status("Account Added"));

                case "tiktok":
                    $response       = TikTokAccount::getAccessToken($code, $platform)->throw();
                    $tokenResponse  = $response->json();
                    $accessToken    = Arr::get($tokenResponse, 'access_token');


                    TikTokAccount::saveTtAccount(
                        $response,
                        $guard,
                        $platform,
                        AccountType::PROFILE->value,
                        ConnectionType::OFFICIAL->value
                    );

                    return redirect()->route($routeName, ['platform' => $platform->slug])
                        ->with(response_status("Account Added"));

                case "youtube":
                    $response       = YouTubeAccount::getAccessToken($code, $platform)->throw();
                    $tokenResponse  = $response->json();
                    $accessToken    = Arr::get($tokenResponse, 'access_token');

                    YouTubeAccount::saveYtAccount(
                        $response,
                        $guard,
                        $platform,
                        AccountType::PROFILE->value,
                        ConnectionType::OFFICIAL->value
                    );

                    return redirect()->route($routeName, ['platform' => $platform->slug])
                        ->with(response_status("Account Added"));

                default:

                    break;
            }

            return redirect()->route($routeName, ['platform' => $platform->slug])
                ->with(response_status("Account Added"));





        } catch (\Exception $e) {
            $routeName = $guard == 'admin' ? "admin.social.account.list" : "user.social.account.list";
            if (@$platform) {
                return redirect()->route($routeName, )
                    ->with(response_status($e->getMessage(), 'error'));

            }


            abort(403, "Something went wrong, please try again.");
        }


    }



    public function maintenanceMode(): View|RedirectResponse
    {


        $title = translate('Maintenance Mode');

        if (site_settings('maintenance_mode') == (StatusEnum::false)->status())
            return redirect()->route('home');

        return view('maintenance_mode', [
            'title' => $title,
        ]);

    }




    /**
     * Handle post webhook
     *
     */
    public function postWebhook()
    {


        $hubToken = request()->query('hub_verify_token');
        $apiKey = site_settings("webhook_api_key");
        $isUserToken = User::whereNotNull('webhook_api_key')->where('webhook_api_key', $hubToken)->exists();


        if ($apiKey == $hubToken || $isUserToken)
            return response(request()->query('hub_challenge'));


        $user = User::where('uid', request()->input('uid'))->first();

        PostWebhookLog::create([
            'user_id' => $user ? $user->id : null,
            'webhook_response' => request()->all()
        ]);

    }

}
