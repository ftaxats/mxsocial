<?php

namespace App\Http\Services\Account\twitter;

use App\Enums\ConnectionType;
use App\Traits\AccountManager;
use App\Enums\AccountType;
use App\Models\MediaPlatform;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use Illuminate\Support\Arr;
use Coderjerk\BirdElephant\BirdElephant;
use Illuminate\Support\Facades\Http;
use Abraham\TwitterOAuth\TwitterOAuth;
use Coderjerk\BirdElephant\Compose\Tweet;

use Illuminate\Support\Facades\File;

class Account
{
    

    use AccountManager;

    public $twUrl ,$params ;


    const BASE_URL = 'https://twitter.com';
    const API_URL  = 'https://api.x.com';



    
    public function __construct(){
        $this->twUrl = "https://twitter.com/";

        $this->params = [
            'expansions' => 'pinned_tweet_id',
            'user.fields' => 'id,name,url,verified,username,profile_image_url'
        ];

    }





    /**
     * Summary of authRedirect
     * @param \App\Models\MediaPlatform $mediaPlatform
     * @return string
     */
    public static function authRedirect(MediaPlatform $mediaPlatform )
    {

        $configuration =  $mediaPlatform->configuration;



        $client_id = $configuration->client_id;
        $redirect_uri = url('/account/twitter/callback?medium='.$mediaPlatform->slug);

        $scope         = 'tweet.read tweet.write users.read offline.access';
        $codeChallenge = 'challenge';
        $state = 'state';
        
        return "https://twitter.com/i/oauth2/authorize?response_type=code&client_id=$client_id&redirect_uri=$redirect_uri&scope=$scope&state=$state&code_challenge=$codeChallenge&code_challenge_method=plain";

    }



           /**
        * Summary of getApiUrl
        * @param string $endpoint
        * @param array $params
        * @param mixed $configuration
        * @param bool $isBaseUrl
        * @return mixed
        */
        public static function getApiUrl(string $endpoint, array $params = [],mixed $configuration , bool $isBaseUrl = false): mixed{


    
            $apiUrl = $isBaseUrl ? self::BASE_URL: self::API_URL;
 

            if (str_starts_with($endpoint, '/')) $endpoint = substr($endpoint, 1);
    
            $v = $configuration->app_version;
 
            $versionedUrlWithEndpoint = $apiUrl . '/' . ($v ? ($v . '/') : '') . $endpoint;
    
            if (count($params)) $versionedUrlWithEndpoint .= '?' . http_build_query($params);
 
            return $versionedUrlWithEndpoint;


        }





        /**
         * Summary of getAccessToken
         * @param string $code
         * @param \App\Models\MediaPlatform $mediaPlatform
         * @return \Illuminate\Http\Client\Response
         */
        public static function getAccessToken(string $code , MediaPlatform $mediaPlatform)
        {

            $configuration =  $mediaPlatform->configuration;

            $client_id = $configuration->client_id;
            $client_secret = $configuration->client_secret;

            $apiUrl = self::getApiUrl('oauth2/token', [
                'code' => $code,
                'grant_type' => 'authorization_code',
                'client_id' =>$client_id,
                'redirect_uri' => url('/account/twitter/callback?medium='.$mediaPlatform->slug),
                'code_verifier' => 'challenge',
            ],$configuration);

            $basicAuthCredential = base64_encode($client_id . ':' .$client_secret);

            return Http::withHeaders([
                'Authorization' => "Basic $basicAuthCredential",
                'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
            ])->post($apiUrl);


        }



        /**
         * Summary of refreshAccessToken
         * @param \App\Models\MediaPlatform $mediaPlatform
         * @param string $token
         * @return \Illuminate\Http\Client\Response
         */
        public static function refreshAccessToken(MediaPlatform $mediaPlatform , string $token): \Illuminate\Http\Client\Response
        {

            $configuration =  $mediaPlatform->configuration;

            $client_id = $configuration->client_id;
            $client_secret = $configuration->client_secret;
            $basicAuthCredential = base64_encode($client_id . ':' .$client_secret);


            $apiUrl = self::getApiUrl('oauth2/token', [
                'refresh_token' => $token,
                'grant_type' => 'refresh_token',
                'client_id' => $client_id,
            ],    $configuration );
            
            return Http::withHeaders([
                'Authorization' => "Basic $basicAuthCredential",
                'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
            ])->asForm()->post($apiUrl);
        }




        
        /**
         * Summary of getAcccount
         * @return \Illuminate\Http\Client\Response
         */
        public function getAcccount(string $token ,  MediaPlatform $mediaPlatform): \Illuminate\Http\Client\Response
        {

            $configuration =  $mediaPlatform->configuration;

            $apiUrl = self::getApiUrl('users/me', [
                'user.fields' => 'name,profile_image_url,username',
            ],$configuration);
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
         * @return void
         */
        public static function saveTwAccount(
            mixed $responseData , 
            string $guard, 
            MediaPlatform $mediaPlatform , 
            string $account_type,
            string $is_official , 
            int | string  $dbId = null){


                    $tw = new self();

                 

                    $responseData = $responseData->json();

                    $expireIn =  Arr::get($responseData,'expires_in' );
                    $token    = Arr::get($responseData,'access_token' );
                    $refresh_token    = Arr::get($responseData,'refresh_token' );

                
                    $response = $tw->getAcccount($token , $mediaPlatform)->throw();



                    $user = $response->json('data');



                    $accountInfo = [

                        'id'                => $user['id'],
                        'account_id'        => $user['id'],
                        'name'              => Arr::get($user,'name',null),
                        'avatar'            => Arr::get($user, 'profile_image_url'),
                        'email'             => Arr::get($user, 'email'),
                        'token'             => $token ,
                    
                        'access_token_expire_at'  => now()->addMonths(2),
    
                        'refresh_token'           => $refresh_token,
    
                        'refresh_token_expire_at' => now()->addMonths(2),
                    ];
    

                    $response  = $tw->saveAccount($guard,$mediaPlatform,$accountInfo,$account_type ,$is_official,$dbId);

        }






    /**
     * Summary of getPost
     * @param string $tweetId
     * @param string $token
     * @param \App\Models\MediaPlatform $mediaPlatform
     * @return \Illuminate\Http\Client\Response
     */
    public static function getPost(string $tweetId  , string $token , MediaPlatform $mediaPlatform ): \Illuminate\Http\Client\Response
    {

        $configuration =  $mediaPlatform->configuration;

        $apiUrl = self::getApiUrl("tweets/{$tweetId}", [
            'tweet.fields' => 'public_metrics,organic_metrics,non_public_metrics'
        ],$configuration);

        return Http::withToken($token)->post($apiUrl);
    }

















    
    /**
     * Instagram account connecton
     *
     * @param MediaPlatform $platform
     * @param array $request
     * @param string $guard
     * @return array
     */
    public function twitter(MediaPlatform $platform ,  array $request , string $guard = 'admin') :array{


        $responseStatus   = response_status(translate('Authentication failed incorrect keys'),'error');

        try {

                $accountId   = Arr::get($request,'account_id', null);

                $responseStatus  = response_status(translate('Api error'),'error');
                $consumer_key    = Arr::get($request,'consumer_key',null);
                $consumer_secret = Arr::get($request,'consumer_secret',null);
                $access_token    = Arr::get($request,'access_token',null);
                $token_secret    = Arr::get($request,'token_secret',null);
                $bearer_token    = Arr::get($request,'bearer_token',null);
            
                $config = array(
                    'consumer_key'      => $consumer_key,
                    'consumer_secret'   => $consumer_secret,
                    'bearer_token'      => $bearer_token,
                    'token_identifier'  => $access_token,
                    'token_secret'      => $token_secret  
                );

    
                $twitter = new BirdElephant($config);

                $response = $twitter->me()->myself([
                    'expansions'  => 'pinned_tweet_id',
                    'user.fields' => 'id,name,url,verified,username,profile_image_url'
                ]);
                
                if($response->data && $response->data->id){
                    $responseStatus       = response_status(translate('Account Created'));
                    $config               = array_merge($config , (array)$response->data);
    
                    $config['link']       =  $this->twUrl.Arr::get($config,'username');
                    $config['avatar']     = Arr::get($config,'profile_image_url');

                    $config['account_id'] =  Arr::get($config,'id');

                    $response         = $this->saveAccount($guard,$platform,$config,AccountType::PROFILE->value ,ConnectionType::OFFICIAL->value ,$accountId);
                }
              


        } catch (\Exception $ex) {
           
        }

      
        return     $responseStatus ;


    }



    public function send(SocialPost $post) :array{





        try {

            $status        = false;
            $message       = 'Failed to tweet!!! Configuration error';


            $account           = $post->account;
            $accountToken           = $account->token;
            $platform          = @$account?->platform;

            $configuration =  $platform->configuration;


            
            $tweetFeed = '';
            if ($post->content) $tweetFeed  .= $post->content;
            if ($post->link)  $tweetFeed  .= $post->link;

            $mediaIds = [];

            if($post->file && $post->file->count() > 0){

 
                $consumerKey = $configuration->api_key;
                $consumerSecret = $configuration->api_secret;
                $access_token =  $configuration->access_token;
                $access_token_secret = $configuration->access_token_secret;
        
                $twitter = new TwitterOAuth($consumerKey, $consumerSecret, $access_token, $access_token_secret);
                $twitter->setApiVersion(1.1);
                $twitter->setTimeouts(15, 15);
                $twitter->setRetries(5, 2);


                foreach ($post->file as $key => $file) {
                    
                   $fileURL = imageURL($file,"post",true);

                   if(isValidVideoUrl($fileURL) && $key == 1){
                    continue;
                   }
                   else if(isValidVideoUrl($fileURL)){

                    $mediaMimeType = File::mimeType($fileURL);
                    $parameters = [
                        'media' => $fileURL,
                        'media_type' => $mediaMimeType,
                        'media_category' => 'tweet_video',
                    ];
                    $media = $twitter->upload('media/upload', $parameters, ['chunkedUpload' => true]);

                   }else{

                    $media = $twitter->upload('media/upload', ['media' => $fileURL]);


                   }


                    if (isset($media->media_id_string)) {
                        $mediaIds[] = $media->media_id_string;
                    } 
                
        
                }

                if(count($mediaIds) > 0){


                    $twitter->setApiVersion(2);

                    $parameters = [
                        'text'  => $tweetFeed,
                        'media' => ['media_ids' => $mediaIds]
                    ];
            
                    sleep(2);


    
            
                    $response =  $twitter->post('tweets', $parameters);


                    if (!($response instanceof \Illuminate\Http\Client\Response)) {

                            $response = json_decode(json_encode($response));

                            if(isset($response->data->id)) {
                                return [
                                    'status'   => true,
                                    'response' => translate("Posted Successfully"),
                                    'url'      => "https://twitter.com/tweet/status/".$response->data->id
                                ];
                            }
                    }

                }


                return [
                    'status'   => false,
                    'response' => 'Failed to post '
                ];
    


                
                
            }

            else{

                $apiUrl = self::getApiUrl('tweets',[],$configuration);

                $response =  Http::withToken($accountToken )
                                ->post($apiUrl, [
                                    'text' => $tweetFeed 
                                ]);

                $responseJson =  $response->json();


                if(isset( $responseJson['data'])  &&  isset( $responseJson['data']['id']) ){
                    return [
                        'status'   => true,
                        'response' => translate("Posted Successfully"),
                        'url'      => "https://twitter.com/tweet/status/".$responseJson['data']['id']
                    ];
                }


                return [
                    'status'   => false,
                    'response' => @$responseJson['detail'] ?? 'Unauthorized'
                ];

            }


          
        } catch (\Exception $ex) {
           $status  = false;
           $message = strip_tags($ex->getMessage());
        }

        return [
           'status'   => $status,
           'response' => $message,
           'url'      => null
       ];


   }



    

}
