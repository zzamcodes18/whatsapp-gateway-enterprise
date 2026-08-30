<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateMasterAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {--name=} {--email=} {--password=} {--phone=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update a master admin account with unlimited limits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name');
        $email = $this->option('email');
        $password = $this->option('password');
        $phone = $this->option('phone');

        // Interaktif jika parameter tidak diberikan di command
        if (empty($email)) {
            $email = $this->ask('Masukkan Email Admin', 'admin@wagateway.com');
        }

        if (empty($name)) {
            $name = $this->ask('Masukkan Nama Admin', 'Master Admin');
        }

        if (empty($password)) {
            $password = $this->secret('Masukkan Kata Sandi Admin (tekan Enter untuk default password123)') ?: 'password123';
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'phone_number' => $phone,
                'role' => 'admin',
                'device_limit' => 0, // unlimited
                'daily_message_limit' => 0, // unlimited
                'password' => Hash::make($password),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Admin otomatis dapat plan Admin permanen (unlimited)
        $adminPlan = \App\Models\Plan::where('slug', 'admin')->where('is_active', true)->first();
        if ($adminPlan && $user->plan_id !== $adminPlan->id) {
            $user->assignPlan($adminPlan, 'system:make-admin', 'Auto-assign plan Admin permanen (unlimited)');
        }

        $this->info("=================================================");
        $this->info(" 🎉 Master Admin Berhasil Dibuat / Diperbarui!");
        $this->info("=================================================");
        $this->info(" Email    : {$email}");
        $this->info(" Role     : Admin (Unlimited Limits)");
        $this->info(" Plan     : Admin (Permanent, Unlimited)");
        $this->info("=================================================");
        return 0;
    }
}
