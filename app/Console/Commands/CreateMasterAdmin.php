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
    protected $signature = 'make:admin {--name=Master Admin} {--email=admin@example.com} {--password=password123} {--phone=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a master admin account with unlimited limits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name');
        $email = $this->option('email');
        $password = $this->option('password');
        $phone = $this->option('phone');

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

        $this->info("Master admin account created successfully for {$email}");
        return 0;
    }
}
