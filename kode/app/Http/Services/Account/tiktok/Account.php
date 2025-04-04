<?php

namespace App\Http\Services\Account\tiktok;

use App\Enums\ConnectionType;
use App\Traits\AccountManager;
use App\Enums\AccountType;
use App\Models\MediaPlatform;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use Illuminate\Support\Arr;
use Coderjerk\BirdElephant\BirdElephant;
use Exception;
use Illuminate\Support\Facades\Http;
use App\Http\Services\Account\tiktok\Account as TiktokAccount;



use Illuminate\Support\Facades\File;

class Account
{


    use AccountManager;

    public $ttUrl, $params;


    const BASE_URL = 'https://www.tiktok.com';
    const API_URL = 'https://open.tiktokapis.com';




    public function __construct()
    {
        $this->ttUrl = "https://www.tiktok.com";

        $this->params = [
            'fields' => 'open_id,union_id,avatar_url,display_name',
        ];

    }





    /**
     * Summary of authRedirect
     * @param \App\Models\MediaPlatform $mediaPlatform
     * @return string
     */
    public static function authRedirect(MediaPlatform $mediaPlatform)
    {

        $configuration = $mediaPlatform->configuration;


        $client_key = $configuration->client_key;
        $redirect_uri = url('/account/tiktok/callback');
        $scope = 'user.info.basic,video.publish,video.list';
        $state = bin2hex(random_bytes(16));
        $code_verifier = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $code_challenge = rtrim(strtr(base64_encode(hash('sha256', $code_verifier, true)), '+/', '-_'), '=');
        $code_challenge_method = 'S256';

        session(['tiktok_code_verifier' => $code_verifier]);

        $auth_url = "https://www.tiktok.com/v2/auth/authorize/" .
            "?client_key=$client_key" .
            "&response_type=code" .
            "&redirect_uri=" . urlencode($redirect_uri) .
            "&scope=" . urlencode($scope) .
            "&state=" . urlencode($state) .
            "&code_challenge=" . urlencode($code_challenge) .
            "&code_challenge_method=" . urlencode($code_challenge_method);

        return $auth_url;

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

        $apiUrl = $isBaseUrl ? self::BASE_URL : self::API_URL;

        if (str_starts_with($endpoint, '/'))
            $endpoint = substr($endpoint, 1);

        $v = $configuration->app_version ?? 'v2';

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

        $client_key = $configuration->client_key;
        $client_secret = $configuration->client_secret;
        $redirect_uri = url('/account/tiktok/callback');
        $code_verifier = session('tiktok_code_verifier');

        $params = [
            'client_key' => $client_key,
            'client_secret' => $client_secret,
            'code' => urldecode($code),
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirect_uri,
            'code_verifier' => $code_verifier,
        ];

        $apiUrl = self::getApiUrl('oauth/token/', [], $configuration);

        $response = Http::asForm()->post($apiUrl, $params);

        if ($response->successful()) {
            return $response;
        } else {
            throw new Exception('Failed to get access token: ' . $response->body());
        }


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
        $client_key = $configuration->client_key;
        $client_secret = $configuration->client_secret;


        $params = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $token,
            'client_key' => $client_key,
            'client_secret' => $client_secret,
        ];

        $apiUrl = self::getApiUrl('oauth/token/', [], $configuration);

        return Http::asForm()->post($apiUrl, $params);
    }





    /**
     * Summary of getAcccount
     * @return \Illuminate\Http\Client\Response
     */
    public function getAccount(string $token, MediaPlatform $mediaPlatform): \Illuminate\Http\Client\Response
    {

        $configuration = $mediaPlatform->configuration;

        $apiUrl = self::getApiUrl('user/info/', [
            'fields' => 'open_id,union_id,avatar_url,display_name',
        ], $configuration);


        return Http::withToken($token)->get($apiUrl);

    }





    /**
     * Summary of saveTwAccount
     * @param mixed $pages
     * @param string $guard
     * @param \App\Models\MediaPlatform $mediaPlatform
     * @param string $account_type
     * @param string $is_official
     * @param int|string $dbId
     *
     */
    public static function saveTtAccount(
        mixed $responseData,
        string $guard,
        MediaPlatform $mediaPlatform,
        string $account_type,
        string $is_official,
        int|string $dbId = null
    ) {
        $tt = new self();

        $responseData = $responseData->json();

        $expireIn = Arr::get($responseData, 'expires_in');
        $token = Arr::get($responseData, 'access_token');
        $refresh_token = Arr::get($responseData, 'refresh_token');

        $response = $tt->getAccount($token, $mediaPlatform)->throw();

        $user = $response->json('data.user');

        $accountInfo = [
            'id' => $user['open_id'],
            'account_id' => $user['open_id'],
            'name' => Arr::get($user, 'display_name', null),
            'avatar' => Arr::get($user, 'avatar_url'),
            'email' => null,
            'token' => $token,
            'access_token_expire_at' => now()->addSeconds($expireIn ?: 86400),
            'refresh_token' => $refresh_token,
            'refresh_token_expire_at' => now()->addYear(),
        ];

        $response = $tt->saveAccount($guard, $mediaPlatform, $accountInfo, $account_type, $is_official, $dbId);

        return $response;
    }






    /**
     * Summary of getPost
     * @param string $tweetId
     * @param string $token
     * @param \App\Models\MediaPlatform $mediaPlatform
     * @return \Illuminate\Http\Client\Response
     */
    public static function getPost(string $videoId, string $token, MediaPlatform $mediaPlatform): \Illuminate\Http\Client\Response
    {
        $configuration = $mediaPlatform->configuration;

        $apiUrl = self::getApiUrl('video/query/', [
            'fields' => 'id,title,create_time,view_count,like_count,comment_count',
            'filters' => json_encode(['video_ids' => [$videoId]]),
        ], $configuration);

        return Http::withToken($token)->get($apiUrl);
    }



    /**
     * Instagram account connecton
     *
     * @param MediaPlatform $platform
     * @param array $request
     * @param string $guard
     * @return array
     */
    public function tiktok(MediaPlatform $platform, array $request, string $guard = 'admin'): array
    {
        $responseStatus = response_status(translate('Authentication failed incorrect keys'), 'error');

        try {
            $accountId = Arr::get($request, 'account_id', null);

            $responseStatus = response_status(translate('Api error'), 'error');
            $client_key = Arr::get($request, 'client_key', $platform->configuration->client_id);
            $client_secret = Arr::get($request, 'client_secret', $platform->configuration->client_secret);
            $access_token = Arr::get($request, 'access_token', null);
            $refresh_token = Arr::get($request, 'refresh_token', null);

            if (!$access_token) {
                throw new \Exception('No access token provided');
            }

            $config = [
                'client_key' => $client_key,
                'client_secret' => $client_secret,
                'access_token' => $access_token,
                'refresh_token' => $refresh_token,
            ];


            $tiktokClient = new TikTokAccount();
            $response = $tiktokClient->getAccount($access_token, $platform);

            if ($response->successful()) {
                $userData = $response->json()['data'];

                if ($userData && isset($userData['open_id'])) {
                    $responseStatus = response_status(translate('Account Created'));
                    $config = array_merge($config, $userData);


                    $config['link'] = "https://www.tiktok.com/@" . Arr::get($config, 'display_name');
                    $config['avatar'] = Arr::get($config, 'avatar_url');
                    $config['account_id'] = Arr::get($config, 'open_id');

                    $response = $this->saveAccount(
                        $guard,
                        $platform,
                        $config,
                        AccountType::PROFILE->value,
                        ConnectionType::OFFICIAL->value,
                        $accountId
                    );
                }
            } else {
                throw new \Exception('API request failed: ' . $response->body());
            }

        } catch (\Exception $ex) {

        }

        return $responseStatus;
    }




    public function send(SocialPost $post): array
    {
        try {
            $status = false;
            $message = 'Failed to post to TikTok!!! Configuration error';

            $account = $post->account;
            $accountToken = $account->token;
            $platform = @$account?->platform;

            if (!$platform) {
                throw new \Exception('No platform associated with account');
            }

            $configuration = $platform->configuration;
            $postDescription = $post->content ?: 'Test Video ' . time();

            if ($post->link) {
            $postDescription .= ' ' . $post->link;
            }
            if (strlen($postDescription) > 2200) {
                $postDescription = substr($postDescription, 0, 2197) . '...';
            }

            if (!$post->file || $post->file->count() === 0) {
                return [
                    'status' => false,
                    'response' => 'TikTok requires a video file'
                ];
            }

            $file = $post->file->first();
            $fileURL = imageURL($file, "post", true);


            if (!str_starts_with($fileURL, 'https://')) {
                throw new \Exception('TikTok requires an HTTPS URL for PULL_FROM_URL');
            }
            $urlCheck = @file_get_contents($fileURL, false, null, 0, 1);
            if ($urlCheck === false) {
                throw new \Exception('Video URL is not publicly accessible: ' . $fileURL);
            }

            $apiUrl = self::getApiUrl('post/publish/video/init/', [], $configuration);

            $payload = [
                'post_info' => [
                    'title' => $postDescription,
                    'privacy_level' => 'PUBLIC_TO_EVERYONE',  //for audited account , use SELF_ONLY for unaudited account
                    'disable_comment' => false,
                    'disable_duet' => false,
                    'disable_stitch' => false,
                    'video_cover_timestamp_ms' => 1000,
                ],
                'source_info' => [
                    'source' => 'PULL_FROM_URL',
                    'video_url' => $fileURL,
                ]
            ];


            $response = Http::withToken($accountToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($apiUrl, $payload);

            $responseJson = $response->json();


            if ($response->successful() && isset($responseJson['data']['publish_id'])) {
                return [
                    'status' => true,
                    'response' => translate("Video posting initiated successfully"),
                    'publish_id' => $responseJson['data']['publish_id']
                ];
            }

            return [
                'status' => false,
                'response' => @$responseJson['error']['message'] ?? 'Failed to publish video'
            ];
        } catch (\Exception $ex) {
            $status = false;
            $message = strip_tags($ex->getMessage());
        }

        return [
            'status' => $status,
            'response' => $message,
            'url' => null
        ];
    }


    public function accountDetails(SocialAccount $account): array
    {
        try {

            $token  = $account->token;
            $fields = 'id,title,video_description,cover_image_url,embed_link,create_time,view_count,like_count,comment_count';

            $queryParams = [
                'fields' => $fields,
            ];

            $apiUrl = self::getApiUrl('/video/list/', $queryParams, $account->platform->configuration);

            $body = [
                'max_count' => 20,
                'cursor' => '0',
            ];

            $response = Http::withToken($token)
                            ->withHeaders(['Content-Type' => 'application/json'])
                            ->post($apiUrl, $body);

            $apiResponse = $response->json();

            $formattedResponse  = $this->formatResponse($apiResponse);

            if (isset($apiResponse['error']) && $apiResponse['error']['code'] !== 'ok') {
                $this->disConnectAccount($account);
                return [
                    'status' => false,
                    'message' => $apiResponse['error']['message']
                ];
            }

            return [
                'status' => true,
                'response' => $formattedResponse,
            ];
        } catch (\Exception $ex) {
            return [
                'status' => false,
                'message' => strip_tags($ex->getMessage())
            ];
        }
    }

    public function formatResponse(array $response)
    {
        if (!isset($response['data']['videos']) || !is_array($response['data']['videos'])) {
            return [
                'data' => [],
            ];
        }

        $formattedData = array_map(function ($video) {
            return [
                'full_picture' => $video['cover_image_url'] ?? get_default_img(),
                'message' => $video['video_description'] ?? $video['title'] ?? '',
                'created_time' => $video['create_time'] ?? \Carbon\Carbon::now()->timestamp,
                'reactions' => [
                    'summary' => [
                        'total_count' => $video['like_count'] ?? 0,
                    ],
                ],
                'comments' => [
                    'summary' => [
                        'total_count' => $video['comment_count'] ?? 0,
                    ],
                ],
                'shares' => [
                    'count' => 0,
                ],
                'permalink_url' => isset($video['id']) ? "https://www.tiktok.com/@username/video/{$video['id']}" : $video['embed_link'] ?? '',
                'privacy' => [
                    'value' => 'EVERYONE',
                ],
                'type' => 'video',
            ];
        }, $response['data']['videos']);


        return [
            'data' => $formattedData,
        ];

    }


}
