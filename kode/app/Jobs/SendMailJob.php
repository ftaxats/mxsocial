<?php

namespace App\Jobs;

use App\Http\Utility\SendMail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;



class SendMailJob implements ShouldQueue
{


    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $user,$template,$code;
    public function __construct(mixed $user,string $template ,array $code )
    {
        $this->user = $user;
        $this->template = $template;
        $this->code = $code;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        SendMail::mailNotifications($this->template,$this->code ,$this->user);
    }
}
