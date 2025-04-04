<?php

namespace App\Console\Commands;

use App\Http\Controllers\CoreController;
use Illuminate\Console\Command;

class RunCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $coreController = new CoreController();
        $coreController->cron();

        $this->info('Cron run successfully!');
    }
}
