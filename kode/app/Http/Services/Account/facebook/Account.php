<?php

namespace App\Http\Services\Account\facebook;

use App\Traits\AccountManager;
use App\Enums\AccountType;
use App\Enums\ConnectionType;
use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Enums\StatusEnum;
use App\Models\Core\File;
use App\Models\MediaPlatform;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;


class Account
{
    

    use AccountManager;


    const BASE_URL = 'https://www.facebook.com';
    const MEDIUM = 'facebook';




       /** Account connection section */



       /**
        * Summary of getScopes
        * @return array
        */
       public static function getScopes(string $type = 'auth'): array{

        
             switch ($type) {

                case 'auth':
                    return [
                        'pages_manage_posts',
                        'pages_show_list',
                        'pages_read_user_content',
                        'pages_read_engagement',
                        'read_insights'
                    ];
                
                default:

                    return [
                        'pages_manage_posts'
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
       public static function getApiUrl(string $endpoint, array $params = [],mixed $configuration , bool $isBaseUrl = false): mixed{


         

           $apiUrl = $isBaseUrl ? self::BASE_URL: $configuration->graph_api_url;


   
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
           $apiUrl = self::getApiUrl('/oauth/access_token', [
               'code' => $code,
               'client_id' =>  $configuration->client_id,
               'client_secret' => $configuration->client_secret,
               'redirect_uri' => url('/account/facebook/callback?medium='.$mediaPlatform->slug),
           ],$configuration);
   
           return Http::post($apiUrl);
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


           $apiUrl =self::getApiUrl('/oauth/access_token', [
               'client_id' => $configuration->client_id,
               'client_secret' => $configuration->client_secret,
               'grant_type' => 'fb_exchange_token',
               'fb_exchange_token' => $token,
           ],$configuration);
   
           return Http::post($apiUrl);
           
       }
   




        /**
         * Summary of authRedirect
         * @param \App\Models\MediaPlatform $mediaPlatform
         * @return mixed
         */
        public static function authRedirect(MediaPlatform $mediaPlatform )
        {

            $scopes = collect(self::getScopes())->join(',');
            $configuration =  $mediaPlatform->configuration;

            return  self::getApiUrl('dialog/oauth', [
                'response_type' => 'code',
                'client_id' => $configuration->client_id,
                'redirect_uri' => url('/account/facebook/callback?medium='.$mediaPlatform->slug),
                'scope' => $scopes,
            ], $configuration , true);

        
        }





        /**
         * Summary of getPagesInfo
         * @param array $fields
         * @param \App\Models\MediaPlatform $mediaPlatform
         * @param string $token
         * @return \Illuminate\Http\Client\Response
         */
        public static function getPagesInfo(
                                         array $fields = ['name,username,picture,access_token'] , 
                                         MediaPlatform $mediaPlatform ,
                                         string $token): \Illuminate\Http\Client\Response
        {

            $configuration =  $mediaPlatform->configuration;

            $apiUrl = self::getApiUrl('/me/accounts', [
                'access_token' => $token,
                'fields' => collect($fields)->join(',')
            ],$configuration);
    
            return Http::get($apiUrl);
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
        public static function saveFbAccount(
                                               mixed $pages , 
                                               string $guard, 
                                               MediaPlatform $mediaPlatform , 
                                               string $account_type,
                                               string $is_official , 
                                               int | string  $dbId = null){


           $faceboook = new self();


           
            foreach ($pages as $page) {


                $accountInfo = [

                    'id'                => $page['id'],
                    'account_id'        => $page['id'],
                    'name'              => Arr::get($page,'name',null),
                    'avatar'            => data_get($page, 'picture.data.url'),
                    'email'             => data_get($page, 'attributes.email' , null),
                    'token'             => Arr::get($page,'access_token',null),
                
                    'access_token_expire_at'  => now()->addDay(),

                    'refresh_token'           => Arr::get($page,'access_token',null),

                    'refresh_token_expire_at' => now()->addDay(),

                ];

                $response  = $faceboook->saveAccount($guard,$mediaPlatform,$accountInfo,$account_type ,$is_official,$dbId);


            }

        }


    /**
     * Connet facebook account
     *
     * @param MediaPlatform $platform
     * @param array $request
     * @param string $guard
     * @return array
     */
    public function facebook(MediaPlatform $platform ,  array $request , string $guard = 'admin') :array {

        $type        = Arr::get($request,'account_type');
        
        $accountId   = Arr::get($request,'account_id', null);
        $token       = Arr::get($request,'access_token');
        $baseApi     = $platform->configuration->graph_api_url;
        $apiVersion  = $platform->configuration->app_version;
        $api         = $baseApi."/".$apiVersion;
        $pageId      = Arr::get($request,'page_id', null);
        $groupId     = Arr::get($request,'group_id', null);
        $response    = response_status(translate('Account Created'));

    
        try {

            $fields = 'id,name,picture,link';

            switch ($type) {
                case AccountType::PROFILE->value:
                    $api =   $api."/me";
                    $fields = 'id,name,email,picture,link';
                    break;
                case AccountType::PAGE->value:
                    $api =   $api."/".$pageId;
                    break;

                case AccountType::GROUP->value:
                    $api =   $api."/".$groupId;
                    break;
            }
            
            $apiResponse = Http::get( $api, [
                'access_token' =>   $token,
                'fields'       =>   $fields
            ]);

            $apiResponse       = $apiResponse->json();

            if(isset($apiResponse['error'])) {
                return response_status($apiResponse['error']['message'],'error');
            }

            if(isset($apiResponse['picture']['data'])){
                $avatar = $apiResponse['picture']['data']['url'];
            }

            switch ($type) {
                case AccountType::PROFILE->value:
                    $identification = Arr::get($apiResponse,'email',null);
                    break;
                case AccountType::PAGE->value || AccountType::GROUP->value:
                    $identification = Arr::get($apiResponse,'id',null);
                    $fields = 'id,name,picture,link';
                    $link   = $platform->configuration->group_url."/".$identification;
                    break;

            }
            
            $accountInfo = [
                'id'         => Arr::get($apiResponse,'id',null) ,
                'account_id' => $identification ,
                'name'       => Arr::get($apiResponse,'name',null),
                'link'       => Arr::get($apiResponse,'link',@$link),
                'email'      => Arr::get($apiResponse,'email',null),
                'token'      => $token,
                'avatar'     => @$avatar ,
            ];


            $this->saveAccount($guard ,$platform , $accountInfo ,$type ,ConnectionType::OFFICIAL->value ,$accountId);


        } catch (\Exception $ex) {
            $response  =   response_status(strip_tags($ex->getMessage()),'error');
        }
        

        return  $response ;
        
    }



    public function accountDetails(SocialAccount $account) : array {

        try {
          
            $baseApi     = $account->platform->configuration->graph_api_url;
            $apiVersion  = $account->platform->configuration->app_version;
            $api         = $baseApi."/".$apiVersion;
            $token       = $account->account_information->token;
            $insightData = [];
            $fields = 'id,full_picture,type,message,permalink_url,link,privacy,created_time,reactions.summary(true),comments.summary(true),shares';
            switch ($account->account_type) {
                case AccountType::PROFILE->value:

                    $api =   $api."/me/feed";
                    break;
                case AccountType::PAGE->value:
                    $fields = 'status_type,message,full_picture,created_time,permalink_url';
                    $api    =  $api."/".$account->account_id."/feed";
                    break;

                case AccountType::GROUP->value:
                    $api =   $api."/".$account->account_id."/feed";
                break;
            }


            $apiResponse = Http::get( $api, [
                'access_token' =>   $token,
                'fields'       =>   $fields
            
            ]);

            $apiResponse       = $apiResponse->json();

            if($account->account_type == AccountType::PAGE->value) {
                $since = strtotime('-1 month');
                $until = strtotime('now');
                $insightApi = Http::get($baseApi."/".$apiVersion."/".$account->account_id."/insights/page_post_engagements", [
                                'access_token' =>   $token,
                                'since' => $since,
                                'until' => $until,
                            ]);

                $insightApiResponse       = $insightApi->json();

                $insightData              = Arr::get($insightApiResponse,'data', []);

            }


            if(isset($apiResponse['error'])) {

                $this->disConnectAccount($account);
                return [
                    'status'  => false,
                    'message' => $apiResponse['error']['message']
                ];
            }

            return( [
                'status'        => true,
                'response'      => $apiResponse,
                'page_insights' => $insightData,
            ]);


        } catch (\Exception $ex) {
         
           return [
               'status'  => false,
               'message' => strip_tags($ex->getMessage())
           ];
        }
    
    }




    /**
     * Summary of send
     * @param \App\Models\SocialPost $post
     * @return array
     */
    public function send(SocialPost $post) :array{

         try {

            $account           = $post->account;

            $accountConnection = $this->accountDetails($post->account);


            $isConnected       = Arr::get($accountConnection,'status', false);
            $message           = translate("Gateway connection error");
            $status            = false;

            if($isConnected && $account->account_type != AccountType::PROFILE->value ){
                $message     = translate("Posted Successfully");
                $status      = true;

                $platform                     = $account->platform;


                #POST IN FEED
                if($post->post_type == PostType::FEED->value){

                    $gwResponse = $this->postFeed($post,$platform ,        $account );

                    if(isset($gwResponse['error'])) {
                        $status  = false;
                        $message = $gwResponse['error']['message'];
                    }

                    if(@$gwResponse['message'])  $message =  @$gwResponse['message'];

                    $postId       = Arr::get($gwResponse,'id');

                    $url =   $postId  ?  "https://fb.com/".$postId : null;

                }

                #POST IN REELS
                elseif($post->post_type == PostType::REELS->value){

                    $gwResponse = $this->postReels($post,$platform ,        $account);

                    if(!$gwResponse['status'])  $status  = false;
                    if(@$gwResponse['message'])  $message =  @$gwResponse['message'];

               
        
                    $postId       = Arr::get($gwResponse,'post_id');

                    $url =   $postId  ?  "https://www.facebook.com/reel/".$postId : null;

                }


            }

            
         } catch (\Exception $ex) {
            $status  = false;
            $message = strip_tags($ex->getMessage());
         }

         return [
            'status'   => $status,
            'response' => $message,
            'url'      => @$url
        ];

    }


    /**
     * Summary of postReels
     * @param \App\Models\SocialPost $post
     * @param array $params
     * @param string $token
     * @param string $baseApi
     * @param string|int|float $apiVersion
     * @return mixed
     */
    public function postReels(SocialPost $post ,MediaPlatform $platform , SocialAccount $account ) : array {


        $account           = $post->account;

        
        $configuration =  $platform->configuration;
        $token = $account->token;
        $pageid = $account->account_id;



        if($post->file && $post->file->count() > 0){


            $reelsapiUrl = self::getApiUrl($pageid . '/video_reels' ,[] ,$configuration );


            foreach ($post->file as $file) {
                
                $fileURL = imageURL($file,"post",true);

                if(isValidVideoUrl($fileURL)){
                   
                    $sessionParams = [
                        "upload_phase" => "start",
                        "access_token" => $token
                    ];

                    

                    $sessionResponse     = Http::retry(3, 3000)
                                                 ->post($reelsapiUrl, $sessionParams);

                    $sessionResponse     = $sessionResponse->json();


                    if(!isset($sessionResponse['video_id']) ){
                        return [
                            "status"  => false,
                            "message" => translate('Cannot create an upload session for uploading reels video to the Facebook page')
                        ];
                    }


                    $uploadResponse = Http::retry(3, 3000)->withHeaders([
                        'Authorization' => 'OAuth ' . $token,
                        'file_url'      => $fileURL
                    ])->post(@$sessionResponse['upload_url']);

                    $uploadResponse     = $uploadResponse->json();


                    if(isset($uploadResponse['success'])){

                        try {
            
                            $params = [
                                "video_id" => $sessionResponse['video_id'],
                                "video_state" => "PUBLISHED",
                                'upload_phase' => 'finish',
                                "description" => $post->content,
                                "access_token" => $token,
                            ];


                            $publishApiUrl = self::getApiUrl($pageid . '/video_reels' ,[] ,$configuration );


                            $response     = Http::retry(3, 3000)
                                                ->post(  $publishApiUrl, $params);
                            $response     = $response->json();


                            if($response['success']){
                                return [
                                    "status" => true,
                                    "post_id" => $sessionResponse['video_id'],
                                    "message" => translate('Video upload is processing now')
                                ];
                            }

                            return [
                                "status"  => false,
                                "message" => @$response['error']['message'] ?? translate("Unable to upload!! API error")
                            ];
                        

                    
                        } catch (\Exception $e) {
                            return [
                                "status" => false,
                                "message" => $e->getMessage(),
                            ];
                        }

           
                  
                    }

                    return [
                        "status"  => false,
                        "message" => @$uploadResponse['debug_info']['message'] ?? translate("Unable to upload!! API error")
                    ];

                   
                }
                
                return [
                    "status"  => false,
                    "message" => translate("Facebook reels doesnot support uploading images")
                ];
            }
        }

 
        return [
            "status"  => false,
            "message" => translate("No file found!! Facebook REELS doesnot support just upload links or text")
        ];



    }





    /**
     * Summary of postFeed
     * @param \App\Models\SocialPost $post
     * @param \App\Models\MediaPlatform $platform
     * @param \App\Models\SocialAccount $account
     * @return mixed
     */
    public function postFeed(SocialPost $post ,MediaPlatform $platform , SocialAccount $account ){


        $token = $account->token;
        $pageid = $account->account_id;

        $postData = [];

        if ($post->content)     $postData['message'] = $post->content;

        if ($post->link)    $postData['link']    = $post->link;

        $configuration =  $platform->configuration;


        if($post->file && $post->file->count() > 0){

            $mediaFiles = [];
            foreach ($post->file as $file) {

                $response = $this->uploadMedia($file, $token ,  $platform , $pageid );
                if (isset($response['id'])) {
                    $mediaFiles[] = ['media_fbid' => $response['id']];
                } 

            }

           $postData['attached_media'] =  $mediaFiles;

        }


        $apiUrl = self::getApiUrl($pageid . '/feed' ,[] ,$configuration );

        $response     = Http::retry(3, 3000)
                            ->withToken($token)
                            ->post($apiUrl,  $postData);

        return $response->json();

    }

    /**
     * Summary of uploadMedia
     * @param \App\Models\Core\File $file
     * @param string $token
     * @param string $baseApi
     * @param string|int|float $apiVersion
     * @return mixed
     */
    public function uploadMedia(File $file , string $token , MediaPlatform $platform , string | int  $pageid ): mixed {

        $fileURL = imageURL($file,"post",true);

        $apiString =  "/videos";

        $configuration =  $platform->configuration;
        

        if(!isValidVideoUrl($fileURL)){

            $apiString =  "/photos";

            $uploadParams = [
                'url'           => $fileURL,
                'published'     => false,
            ];

        }else{

            $uploadParams = [
                'file_url'  =>  $fileURL,
                'published' =>  false,
                'description'=> 'example caption',
                'access_token' => $token,
            ];
    
        }
  

        $apiUrl = self::getApiUrl($pageid . $apiString  ,[] ,$configuration );


       
        $uploadResponse = Http::retry(3, 3000)
                                ->withToken($token)
                                ->post($apiUrl , $uploadParams);

        return   $uploadResponse->json();

    }



}
