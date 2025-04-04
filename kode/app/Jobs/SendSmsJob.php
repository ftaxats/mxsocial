<?php

namespace App\Jobs;

use App\Http\Utility\SendSMS;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $user,$template,$code;
    /**
     * Create a new job instance.
     */
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
        SendSMS::smsNotification($this->template,$this->code ,$this->user);
    }
}
