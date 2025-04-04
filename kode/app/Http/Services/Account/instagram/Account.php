<?php

namespace App\Http\Services\Account\instagram;

use App\Enums\ConnectionType;
use App\Traits\AccountManager;
use App\Enums\AccountType;
use App\Enums\PostType;
use App\Models\MediaPlatform;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use App\Http\Services\Account\instagram\AccountConfig;
use App\Models\Core\File;

class Account
{


    use AccountManager;



    const BASE_URL = 'https://www.facebook.com';
    const API_URL = 'https://graph.facebook.com';


    /**
     * Summary of getScopes
     * @return array
     */
    public static function getScopes(string $type = 'auth'): array
    {


        switch ($type) {

            case 'auth':
                return [
                    'ads_management',
                    'business_management',
                    'instagram_basic',
                    'instagram_content_publish',
                    'pages_read_engagement'
                ];

            default:

                return [
                    'pages_read_engagement'
                ];
        }

    }




    /**
     * Summary of getApiUrl
     * @param string $endpoint
     * @param array $params
     * @param mixed $configuration
     * @param bool $isBaseUrl
     * @return mixed
     */
    public static function getApiUrl(string $endpoint, array $params = [], mixed $configuration, bool $isBaseUrl = false): mixed
    {


        $apiUrl = $isBaseUrl ? self::BASE_URL : $configuration->graph_api_url;

        if (str_starts_with($endpoint, '/'))
            $endpoint = substr($endpoint, 1);

        $v = $configuration->app_version;

        $versionedUrlWithEndpoint = $apiUrl . '/' . ($v ? ($v . '/') : '') . $endpoint;

        if (count($params))
            $versionedUrlWithEndpoint .= '?' . http_build_query($params);


        return $versionedUrlWithEndpoint;


    }





    /**
     * Summary of getAccessToken
     * @param string $code
     * @param \App\Models\MediaPlatform $mediaPlatform
     * @return \Illuminate\Http\Client\Response
     */
    public static function getAccessToken(string $code, MediaPlatform $mediaPlatform)
    {

        $configuration = $mediaPlatform->configuration;
        $apiUrl = self::getApiUrl('/oauth/access_token', [
            'code' => $code,
            'client_id' => $configuration->client_id,
            'client_secret' => $configuration->client_secret,
            'redirect_uri' => url('/account/instagram/callback?medium=' . $mediaPlatform->slug),
        ], $configuration);

        return Http::post($apiUrl);
    }





    /**
     * Summary of refreshAccessToken
     * @param \App\Models\MediaPlatform $mediaPlatform
     * @param string $token
     * @return \Illuminate\Http\Client\Response
     */
    public static function refreshAccessToken(MediaPlatform $mediaPlatform, string $token): \Illuminate\Http\Client\Response
    {
        $configuration = $mediaPlatform->configuration;


        $apiUrl = self::getApiUrl('/oauth/access_token', [
            'client_id' => $configuration->client_id,
            'client_secret' => $configuration->client_secret,
            'grant_type' => 'fb_exchange_token',
            'fb_exchange_token' => $token,
        ], $configuration);

        return Http::post($apiUrl);
    }




    /**
     * Summary of authRedirect
     * @param \App\Models\MediaPlatform $mediaPlatform
     * @return mixed
     */
    public static function authRedirect(MediaPlatform $mediaPlatform)
    {


        $scopes = collect(self::getScopes())->join(',');
        $configuration = $mediaPlatform->configuration;


        return self::getApiUrl('dialog/oauth', [
            'response_type' => 'code',
            'client_id' => $configuration->client_id,
            'redirect_uri' => url('/account/instagram/callback?medium=' . $mediaPlatform->slug),
            'scope' => $scopes,
        ], $configuration, true);


    }





    /**
     * Summary of getPagesInfo
     * @param array $fields
     * @param \App\Models\MediaPlatform $mediaPlatform
     * @param string $token
     * @return \Illuminate\Http\Client\Response
     */
    public static function getAccounts(array $fields = ['connected_instagram_account,name,access_token'], MediaPlatform $mediaPlatform, string $token): \Illuminate\Http\Client\Response
    {

        $configuration = $mediaPlatform->configuration;

        $apiUrl = self::getApiUrl('/me/accounts', [
            'access_token' => $token,
            'fields' => collect($fields)->join(',')
        ], $configuration);

        return Http::get($apiUrl);
    }






    /**
     * Summary of getInstagramAccountInfo
     * @param string $accountId
     * @param array $fields
     * @param \App\Models\MediaPlatform $mediaPlatform
     * @param string $token
     * @return \Illuminate\Http\Client\Response
     */
    public function getInstagramAccountInfo(string $accountId, array $fields = null, MediaPlatform $mediaPlatform, string $token): \Illuminate\Http\Client\Response
    {

        $configuration = $mediaPlatform->configuration;

        $redirect_uri = self::getApiUrl(endpoint: '/' . $accountId, params: [], configuration: $configuration);

        return Http::withToken($token)->get($redirect_uri, [
            'fields' => collect($fields)->join(','),
        ]);
    }


    /**
     * Summary of saveFbAccount
     * @param mixed $pages
     * @param string $guard
     * @param \App\Models\MediaPlatform $mediaPlatform
     * @param string $is_official
     * @param int|string $dbId
     * @return void
     */
    public static function saveIgAccount(
        mixed $pages,
        string $guard,
        MediaPlatform $mediaPlatform,
        string $account_type,
        string $is_official,
        string $token,
        int|string $dbId = null
    ) {





        $instagram = new self();



        foreach ($pages as $page) {




            if (isset($page['connected_instagram_account']) && isset($page['connected_instagram_account']['id'])) {


                $pageId = $page['connected_instagram_account']['id'];




                try {



                    $account = $instagram->getInstagramAccountInfo(
                        $pageId,
                        ['id,name,username,profile_picture_url'],
                        $mediaPlatform,
                        $token
                    )->throw()
                        ->json();

                    $accountInfo = [

                        'id' => $account['id'],
                        'account_id' => $account['id'],
                        'name' => Arr::get($account, 'username', null),
                        'avatar' => Arr::get($account, 'profile_picture_url', null),
                        'email' => Arr::get($account, 'email', null),
                        'token' => Arr::get($account, 'access_token', $token),

                        'access_token_expire_at' => now()->addMonths(2),
                        'refresh_token' => Arr::get($account, 'access_token', $token),
                        'refresh_token_expire_at' => now()->addMonths(2),

                    ];

                    $response = $instagram->saveAccount($guard, $mediaPlatform, $accountInfo, $account_type, $is_official, $dbId);


                } catch (\Exception $ex) {

                }




            }


        }
    }






    public function accountDetails(SocialAccount $account): array
    {


        try {

            $baseApi = $account->platform->configuration->graph_api_url;
            $apiVersion = $account->platform->configuration->app_version;
            $api = $baseApi . "/" . $apiVersion;
            $token = $account->token;
            $userId = $account->account_id;
            $apiUrl = $api . "/" . $userId . "/media";
            $fields = 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp,like_count,comments_count';

            $params = [
                'fields' => $fields,
                'access_token' => $token,
            ];

            $response = Http::get($apiUrl, $params);
            $apiResponse = $response->json();

            if (isset($apiResponse['error'])) {

                $this->disConnectAccount($account);
                return [
                    'status' => false,
                    'message' => $apiResponse['error']['message']
                ];
            }

            $apiResponse = $this->formatResponse($apiResponse);

            return [
                'status' => true,
                'response' => $apiResponse,
            ];


        } catch (\Exception $ex) {

            return [
                'status' => false,
                'message' => strip_tags($ex->getMessage())
            ];
        }

    }

    public function formatResponse(array $response): array
    {


        $responseData = Arr::get($response, 'data', []);

        if (count($responseData) > 0) {

            $formattedData = [];
            foreach ($responseData as $key => $value) {
                $formattedData[] = [
                    'status_type' => Arr::get($value, 'media_type', null),
                    'full_picture' => Arr::get($value, 'media_url', null),
                    'link' => Arr::get($value, 'permalink', null),
                    'created_time' => Arr::get($value, 'timestamp', null),
                    'reactions' => [
                        'summary' => [
                            'total_count' => Arr::get($value, 'like_count', 0), // Instagram likes
                        ],
                    ],
                    'comments' => [
                        'summary' => [
                            'total_count' => Arr::get($value, 'comments_count', 0), // Instagram comments
                        ],
                    ]
                ];
            }
            $response['data'] = $formattedData;
        }

        return $response;


    }


    public function send(SocialPost $post): array
    {


        $account = $post->account;
        if ($account->is_official == ConnectionType::OFFICIAL->value)
            return ($this->official($post));
    }


    public function official(SocialPost $post): array
    {

        try {

            $account = $post->account;
            $platform = @$post->account->platform;

            $accountConnection = $this->accountDetails($account);

            $isConnected = Arr::get($accountConnection, 'status', false);
            $message = translate("Text and url not supported in insta feed");
            $status = false;
            $token = $account->account_information->token;

            if ($post->file && $post->file->count() > 0) {

                $message = translate("Text and url not supported in insta feed");


                if ($post->file && $post->file->count() > 0) {

                    $message = translate("Posted Successfully");
                    $status = true;



                    #POST IN FEED
                    if ($post->post_type == PostType::FEED->value) {
                        $response = $this->postFeed($post, $platform);
                    }
                    #POST IN REELS
                    elseif ($post->post_type == PostType::REELS->value) {
                        $response = $this->postReel($post, $platform);
                    }
                    #POST IN STORY
                    elseif ($post->post_type == PostType::STORY->value) {
                        $response = $this->postStory($post, $platform);
                    }

                    $url = Arr::get($response, 'url');
                    $message = Arr::get($response, 'message', $message);
                    $status = Arr::get($response, 'status', $status);

                }

            }


        } catch (\Exception $ex) {
            $status = false;
            $message = strip_tags($ex->getMessage());
        }

        return [
            'status' => @$url ? true : false,
            'response' => @$message,
            'url' => @$url
        ];

    }



    /**
     * Summary of postFeed
     * @param \App\Models\SocialPost $post
     * @param mixed $ig
     * @return array
     */
    public function postFeed(SocialPost $post, MediaPlatform $platform): array
    {

        $account = $post->account;

        $token = $account->account_information->token;


        $media_ids = [];

        if ($post->file->count() == 0) {

            return [

                'status' => false,
                'message' => translate('Instagram doesn\'t support  only text posting'),
            ];

        }




        if ($post->file->count() == 0) {

            return [

                'status' => false,
                'message' => translate('Instagram doesn\'t support  only text posting'),
            ];

        }




        if ($post->file->count() > 10) {

            return [

                'status' => false,
                'message' => translate('Instagram doesn\'t support more than 10 media at a time'),
            ];

        }




        if ($post->file->count() > 1) {

            #MULTI FILE POST


            $accountId = $account->account_id;

            $configuration = $platform->configuration;

            $apiUrl = self::getApiUrl($accountId . '/media', [], $configuration);

            foreach ($post->file as $file) {

                $fileURL = imageURL($file, "post", true);

                $upload_params['is_carousel_item'] = true;
                $upload_params['caption'] = $post->content ?? "feed";


                if (!isValidVideoUrl($fileURL)) {

                    $upload_params['media_type'] = 'IMAGE';
                    $upload_params['image_url'] = $fileURL;

                } else {

                    $upload_params['media_type'] = 'VIDEO';
                    $upload_params['video_url'] = $fileURL;

                }


                $upload_response = Http::withToken($token)
                    ->asForm()
                    ->acceptJson()
                    ->post($apiUrl, $upload_params)
                    ->throw();


                $media_ids[] = @$upload_response->json('id');

            }

            $upload_params = [
                'media_type' => 'CAROUSEL',
                'children' => $media_ids,
                'caption' => $post->content ?? "feed"
            ];


            $publishCarouselResponse = Http::withToken($token)
                ->retry(3, 3000)
                ->post($apiUrl, $upload_params);




            $uploadResponse = $this->publishContainer(
                $accountId,
                $publishCarouselResponse->json('id'),
                $platform,
                $token
            );



            if (@$uploadResponse["id"]) {

                $shortcode = $this->getPost(@$uploadResponse["id"], $token, $platform);
                $url = "https://www.instagram.com/p/" . $shortcode;

            }


        } else {

            #SINGLE FILE POST

            $file = $post->file->first();

            $response = $this->publishSingleFile($file, $account, $platform, $post->content ?? "feed");


            if (@$response["id"]) {

                $shortcode = $this->getPost(@$response["id"], $token, $platform);

                $url = "https://www.instagram.com/p/" . $shortcode;
            }


        }

        return [
            'url' => @$url,
            'status' => @$url ? true : false,
            'message' => @$url ? translate('Posted successfully') : translate('Failed to post'),
        ];

    }



    /**
     * Summary of getPost
     * @param mixed $postId
     * @param mixed $token
     * @param mixed $platform
     */
    public function getPost($postId, $token, $platform)
    {

        $configuration = $platform->configuration;

        $response = Http::withToken($token)
            ->get(self::getApiUrl($postId . "?fields=shortcode", [], $configuration))->throw();



        return @$response->json('shortcode');



    }






    /**
     * Summary of publishSingleFile
     * @param \App\Models\Core\File $file
     * @param \App\Models\SocialAccount $account
     * @param \App\Models\MediaPlatform $platform
     * @param mixed $caption
     * @return array
     */
    public function publishSingleFile(File $file, SocialAccount $account, MediaPlatform $platform, $caption): array
    {


        $id = $account->account_id;

        $configuration = $platform->configuration;
        $token = $account->token;

        $fileURL = imageURL($file, "post", true);



        if (!isValidVideoUrl($fileURL)) {

            $postData = ['image_url' => imageURL($file, "post", true), 'caption' => $caption];
        } else {

            $postData = [
                'media_type' => "REELS",
                'video_url' => $fileURL,
                'share_to_feed' => true,
                'caption' => $caption
            ];

        }


        $apiUrl = self::getApiUrl("$id/media", [], $configuration);

        $response = Http::withToken($token)
            ->retry(3, sleepMilliseconds: 3000)
            ->post($apiUrl, $postData)->throw();


        $mediaId = $response->json('id');

        $isUploaded = $this->checkUploadStatus(
            mediaId: $mediaId,
            delayInSeconds: 3,
            maxAttempts: 10,
            platform: $platform,
            token: $token
        );



        if (!$isUploaded['is_ready']) {

            return [
                'status' => false,
            ];
        }


        $uploadResponse = $this->publishContainer($id, $mediaId, $platform, $token);


        return ['id' => $uploadResponse->json('id')];





    }





    /**
     * Summary of publishContainer
     * @param string $igId
     * @param string|int $mediaId
     * @param \App\Models\MediaPlatform $platform
     * @param string $token
     * @return \Illuminate\Http\Client\Response
     */
    protected function publishContainer(string $igId, string|int $mediaId, MediaPlatform $platform, string $token)
    {


        $configuration = $platform->configuration;

        $apiUrl = self::getApiUrl($igId . '/media_publish', [], $configuration);

        return Http::retry(3, 3000)
            ->withToken($token)
            ->post($apiUrl, [
                'creation_id' => (int) $mediaId,
            ]);
    }








    private function checkUploadStatus(
        string $mediaId,
        int $delayInSeconds = 3,
        int $maxAttempts = 10,
        MediaPlatform $platform,
        string $token
    ): array {
        $status = false;
        $attempted = 0;
        $isFinished = false;

        $configuration = $platform->configuration;

        while (!$isFinished && $attempted < $maxAttempts) {

            $videoStatus = Http::withToken($token)
                ->baseUrl(self::API_URL)
                ->retry(1, 3000)
                ->get(self::getApiUrl($mediaId, ['fields' => 'status_code,status'], $configuration))->throw();


            $status = $videoStatus->json('status_code');
            $isFinished = in_array(strtolower($status), ['finished', 'ok', 'completed', 'ready']);

            if ($isFinished) {
                break;
            }

            $isError = in_array(strtolower($status), ['error', 'failed']);
            if ($isError) {
                break;
            }

            $attempted++;
            sleep($delayInSeconds);
        }

        return [
            'is_ready' => $isFinished,
            'status_code' => $status,
            'status' => $videoStatus->json('status'),
        ];
    }





    /**
     * Summary of postReel
     * @param \App\Models\SocialPost $post
     * @param \App\Models\MediaPlatform $platform
     * @return array
     */
    public function postReel(SocialPost $post, MediaPlatform $platform): array
    {

        $account = $post->account;
        $token = $account->account_information->token;
        $file = $post->file->first();
        $fileURL = imageURL($file, "post", true);


        $id = $account->account_id;

        $configuration = $platform->configuration;
        $token = $account->token;


        if (isValidVideoUrl($fileURL)) {


            $postData = [
                'media_type' => "REELS",
                'video_url' => $fileURL,
                'share_to_feed' => true,
                'caption' => $post->content ?? "feed"
            ];



            $apiUrl = self::getApiUrl("$id/media", [], $configuration);

            $response = Http::withToken($token)
                ->retry(3, sleepMilliseconds: 3000)
                ->post($apiUrl, $postData)->throw();


            $mediaId = $response->json('id');

            $isUploaded = $this->checkUploadStatus(
                mediaId: $mediaId,
                delayInSeconds: 3,
                maxAttempts: 10,
                platform: $platform,
                token: $token
            );



            if (!$isUploaded['is_ready']) {

                return [
                    'status' => false,
                ];
            }


            $uploadResponse = $this->publishContainer($id, $mediaId, $platform, $token);


            if (@$uploadResponse["id"]) {

                $shortcode = $this->getPost(@$uploadResponse["id"], $token, $platform);

                $url = "https://www.instagram.com/p/" . $shortcode;

                return [
                    'url' => @$url,
                    'status' => true,
                    'message' => translate('Posted successfully')
                ];
            }
        }

        return [
            "status" => false,
            "message" => translate("Instagram reels doesnot support uploading images")
        ];


    }






    /**
     * Summary of postStory
     * @param \App\Models\SocialPost $post
     * @param \App\Models\MediaPlatform $platform
     * @return array
     */
    public function postStory(SocialPost $post, MediaPlatform $platform): array
    {

        $account = $post->account;
        $token = $account->account_information->token;

        $configuration = $platform->configuration;
        $file = $post->file->first();
        $fileURL = imageURL($file, "post", true);

        $id = $account->account_id;

        $postData['caption'] = $post->content ?? "feed";
        $postData['media_type'] = "STORIES";

        if (isValidVideoUrl($fileURL)) {
            $postData['video_url'] = $fileURL;
        } else {
            $postData['image_url'] = $fileURL;
        }



        $apiUrl = self::getApiUrl("$id/media", [], $configuration);

        $response = Http::withToken($token)
            ->retry(3, sleepMilliseconds: 3000)
            ->post($apiUrl, $postData)->throw();


        $mediaId = $response->json('id');

        $isUploaded = $this->checkUploadStatus(
            mediaId: $mediaId,
            delayInSeconds: 3,
            maxAttempts: 10,
            platform: $platform,
            token: $token
        );


        if (!$isUploaded['is_ready']) {

            return [
                'status' => false,
                'message' => translate('Failed to post')
            ];
        }

        $uploadResponse = $this->publishContainer($id, $mediaId, $platform, $token);



        if (@$uploadResponse["id"]) {

            $shortcode = $this->getPost(@$uploadResponse["id"], $token, $platform);

            $url = "https://www.instagram.com/p/" . $shortcode;
        }



        return [
            'url' => @$url,
            'status' => @$url ? true : false,
            'message' => @$url ? translate('Posted successfully') : translate('Failed to post'),
        ];




    }





}
