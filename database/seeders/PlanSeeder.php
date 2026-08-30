<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Seed paket default sistem.
     * - Free Plan: permanen (lifetime), 1 device, 100 pesan/hari, 3000 pesan/bulan.
     * - Admin Plan: permanen, semua unlimited, otomatis untuk role admin.
     */
    public function run(): void
    {
        $freePlan = Plan::updateOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free',
                'description' => 'Paket gratis permanen untuk mencoba layanan WhatsApp Gateway. Cocok untuk pengguna baru dan pengujian.',
                'price' => 0,
                'duration_days' => 36500, // ~100 tahun = permanen/lifetime
                'device_limit' => 1,
                'daily_message_limit' => 100,
                'monthly_message_limit' => 3000,
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 0,
            ]
        );

        $adminPlan = Plan::updateOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'Paket khusus admin sistem. Permanen dengan akses unlimited untuk semua fitur.',
                'price' => 0,
                'duration_days' => 36500, // ~100 tahun = permanen/lifetime
                'device_limit' => 0, // unlimited
                'daily_message_limit' => 0, // unlimited
                'monthly_message_limit' => 0, // unlimited
                'is_active' => true,
                'is_default' => false,
                'sort_order' => -1,
            ]
        );

        // Auto-assign plan Admin ke semua user dengan role admin yang belum punya
        User::where('role', 'admin')->whereNull('plan_id')->get()->each(function ($admin) use ($adminPlan) {
            $admin->assignPlan($adminPlan, 'system:seeder', 'Auto-assign plan Admin permanen (unlimited)');
        });
    }
}
