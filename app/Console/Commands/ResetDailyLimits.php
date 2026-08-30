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
        $startOfMonth = now()->startOfMonth()->toDateString();

        $affected = User::query()->update([
            'messages_sent_today' => 0,
            'last_limit_reset_at' => $today,
        ]);

        // Reset counter bulanan hanya saat pergantian bulan (hari pertama bulan)
        $monthlyAffected = 0;
        if (now()->day === 1) {
            $monthlyAffected = User::query()
                ->where(function ($q) use ($startOfMonth) {
                    $q->whereNull('last_monthly_reset_at')
                        ->orWhere('last_monthly_reset_at', '!=', $startOfMonth);
                })
                ->update([
                    'messages_sent_this_month' => 0,
                    'last_monthly_reset_at' => $startOfMonth,
                ]);
        }

        $this->info("Successfully reset daily message limits for {$affected} users at {$today} 00:05.");
        if ($monthlyAffected > 0) {
            $this->info("Successfully reset monthly message limits for {$monthlyAffected} users (new month: {$startOfMonth}).");
        }

        Log::info("gateway:reset-daily-limits executed. Reset daily: {$affected} users, monthly: {$monthlyAffected} users.");

        return Command::SUCCESS;
    }
}
