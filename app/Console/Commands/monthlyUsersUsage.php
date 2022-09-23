<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class monthlyUsersUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usersUsage:monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset and create a new cache for whatsapp usage every month';

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
        $monthlyUsage = Cache::get('dailyUsersResult');
        Cache::forever('monthlyUsersResult', $monthlyUsage);
        Cache::forget('dailyUsersResult');
        Log::info("Successfully accumulate user monthly usage to result");
    }
}
