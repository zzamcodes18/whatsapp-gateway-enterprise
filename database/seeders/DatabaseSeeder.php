<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed paket default (Free Plan permanen untuk user baru)
        $this->call([
            PlanSeeder::class,
        ]);

        // Akun Master Admin dibuat secara eksplisit saat instalasi CLI via Artisan Command
    }
}
