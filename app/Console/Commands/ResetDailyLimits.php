<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResetDailyLimits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway:reset-daily-limits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset daily message counters for all users at 00:05';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = now()->toDateString();

        $affected = User::query()->update([
            'messages_sent_today' => 0,
            'last_limit_reset_at' => $today,
        ]);

        $this->info("Successfully reset daily message limits for {$affected} users at {$today} 00:05.");
        Log::info("gateway:reset-daily-limits executed. Reset {$affected} users.");

        return Command::SUCCESS;
    }
}
