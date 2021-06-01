<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class dailyUsersUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usersUsage:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset and create a new cache for whatsapp usage every day';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $usersUsage = Cache::get('dailyUsersUsage');
        Cache::forever('dailyUsersResult', $usersUsage);
        Cache::forget('dailyUsersUsage');
        Log::info("Successfully accumulate user daily usage to result");
        $this->info('Successfully accumulate user daily usage to result');
    }
}
