<?php

namespace App\Traits;

use App\Enums\FileKey;
use App\Enums\PlanDuration;
use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Enums\StatusEnum;
use App\Jobs\SocialPostJob;
use App\Models\Admin;
use App\Models\Core\File;
use App\Models\MediaPlatform;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Traits\Fileable;
trait PostManager
{

    use Fileable;

    /**
     * Save a post
     *
     * @return array
     */
    protected function savePost(array $request , ? Admin $admin =  null  ,? User $user = null ) :array{

        $accounts     = SocialAccount::with(['platform'])
                                       ->whereIn('id',Arr::get($request,"account_id", []))
                                       ->get();

        $scheduleTime = Arr::get($request,"schedule_date", null);


        $files        = Arr::get($request,"files", []);
        $postTypes    = Arr::get($request,"post_type", []);

        DB::transaction(function() use ($request ,$admin ,$user ,$accounts ,$scheduleTime ,$files ,$postTypes ) {

            foreach($accounts as $account){

                $post                     = new SocialPost();
                $post->account_id         = $account->id;
                $post->platform_id        = $account->platform_id;
                $post->subscription_id    = $user ? $user->runningSubscription->id : null;
                $post->user_id            = $user ? $user->id : null;
                $post->admin_id           = $admin ? $admin->id : null;
                $post->content            = Arr::get($request,"text", []);
                $post->link               = Arr::get($request,"link", []);
                $post->is_scheduled       = $scheduleTime ? StatusEnum::true->status() : StatusEnum::false->status() ;
                $post->schedule_time      = $scheduleTime;
                $post->status             = strval($scheduleTime ? PostStatus::value('SCHEDULE',true): PostStatus::value('PENDING',true));
                $post->post_type          = Arr::get($postTypes ,@$account->platform->slug ,strval(PostType::value("FEED",true)));
                $post->save();


                try {
                    foreach($files as $file){
                        $this->saveFile($post ,$this->storeFile(
                            file        : $file,
                            location    : config("settings")['file_path']['post']['path'],
                         )
                         , FileKey::POST_FILE->value);

                    }
                } catch (\Throwable $th) {

                }


            }

            $totalPost  = count($accounts);
            if($totalPost > 0 && $user ){
                $this->generateCreditLog(
                    user        : $user,
                    trxType     : Transaction::$MINUS,
                    balance     : $totalPost,
                    postBalance : (int)$user->runningSubscription->remaining_post_balance,
                    details     : count($accounts) . ' social post created',
                    remark      : t2k("post_balance"),
                );
                if( (int)$user->runningSubscription->remaining_post_balance != PlanDuration::value('UNLIMITED')){
                    @$user->runningSubscription->decrement('remaining_post_balance',$totalPost);
                }
            }
        });


        return [
            'status'     => true,
            'message'    => translate('Successfully created posts. Refer to the logs for more information')
        ];


    }



    /**
     * publish a post
     *
     * @param SocialPost $post
     * @return void
     */
    public function publishPost(SocialPost $post) :void{

        $account = $post->account;

        if(!$account) return;

        $class        = 'App\\Http\\Services\\Account\\'.$account->platform->slug.'\\Account';
        $service      =  new  $class();

        $response     = $service->send($post);

        $is_success   = Arr::get($response,'status' ,false);
        $post->status =  strval($is_success ? PostStatus::value('SUCCESS') : PostStatus::value('FAILED'));
        $post->platform_response  = $response;
        $post->save();

        if(!$is_success
            && $post->user
            && (int)$post->user->runningSubscription->remaining_post_balance != PlanDuration::value('UNLIMITED')){
            $user = $post->user;
            $this->generateCreditLog(
                user        : $post->user,
                trxType     : Transaction::$PLUS,
                balance     : 1,
                postBalance : (int)$user->runningSubscription->remaining_post_balance,
                details     : 'Failed to post in '.$account->name." 1 Credit return to user post balance",
                remark      : t2k("post_balance"),
            );
            @$user->runningSubscription->increment('remaining_post_balance',1);
        }


    }

}
