<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User (Unlimited Devices & Unlimited Messages)
        $admin = User::firstOrCreate(
            ['email' => 'admin@lapakotp.com'],
            [
                'name' => 'LapakOTP Master Admin',
                'phone_number' => '6281234567890',
                'role' => 'admin',
                'device_limit' => 0, // 0 = unlimited
                'daily_message_limit' => 0, // 0 = unlimited
                'password' => Hash::make('password123'),
                'is_active' => true,
                'last_login_at' => now(),
                'last_login_ip' => '127.0.0.1',
            ]
        );

        // 2. Default API Key for Admin User
        ApiKey::firstOrCreate(
            ['key_prefix' => 'lpk_admin_'],
            [
                'user_id' => $admin->id,
                'name' => 'Master Admin API Key',
                'key_hash' => hash('sha256', 'lpk_admin_prod_key_1234567890abcdef'),
                'permissions' => ['send_message', 'read_devices', 'read_logs'],
                'rate_limit_per_minute' => 0, // unlimited
                'is_active' => true,
                'last_used_at' => now(),
            ]
        );

        // 3. System Activity Log
        $admin->logActivity('auth.login', 'Admin otomatis di-seed dengan akses unlimited.');
    }
}
}
