<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class yearlyUsersUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usersUsage:yearly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset and create a new cache for whatsapp usage every year';

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
        $yearUsage = Cache::get('monthlyUsersResult');
        Cache::forever('yearlyUsersResult', $yearUsage);
        Cache::forget('monthlyUsersResult');
        Log::info("Successfully accumulate user yearly usage to result");
        $this->info('Successfully accumulate user yearly usage to result');
    }
}
