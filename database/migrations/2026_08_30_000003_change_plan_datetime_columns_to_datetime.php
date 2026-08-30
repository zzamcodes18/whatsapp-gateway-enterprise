<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah kolom tanggal dari TIMESTAMP ke DATETIME.
     *
     * TIMESTAMP MySQL hanya mendukung sampai 2038-01-19 (Unix epoch limit),
     * sedangkan plan permanen (36500 hari) menghasilkan tanggal ~2126.
     * Kolom yang diubah:
     * - users.plan_expires_at
     * - subscriptions.starts_at
     * - subscriptions.ends_at
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'plan_expires_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dateTime('plan_expires_at')->nullable()->change();
            });
        }

        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('subscriptions', 'starts_at')) {
                    $table->dateTime('starts_at')->nullable()->change();
                }
                if (Schema::hasColumn('subscriptions', 'ends_at')) {
                    $table->dateTime('ends_at')->nullable()->change();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'plan_expires_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('plan_expires_at')->nullable()->change();
            });
        }

        if (Schema::hasTable('subscriptions')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                if (Schema::hasColumn('subscriptions', 'starts_at')) {
                    $table->timestamp('starts_at')->nullable()->change();
                }
                if (Schema::hasColumn('subscriptions', 'ends_at')) {
                    $table->timestamp('ends_at')->nullable()->change();
                }
            });
        }
    }
};
