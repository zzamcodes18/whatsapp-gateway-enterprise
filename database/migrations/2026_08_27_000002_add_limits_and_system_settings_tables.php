<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'device_limit')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('device_limit')->default(3)->after('is_active');
                $table->unsignedInteger('daily_message_limit')->default(500)->after('device_limit');
                $table->unsignedInteger('messages_sent_today')->default(0)->after('daily_message_limit');
                $table->date('last_limit_reset_at')->nullable()->after('messages_sent_today');
            });
        }

        if (!Schema::hasColumn('devices', 'is_system_bot')) {
            Schema::table('devices', function (Blueprint $table) {
                $table->boolean('is_system_bot')->default(false)->after('status');
            });
        }

        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key', 100)->unique();
                $table->text('value')->nullable();
                $table->string('type', 50)->default('string');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');

        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('is_system_bot');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'device_limit',
                'daily_message_limit',
                'messages_sent_today',
                'last_limit_reset_at',
            ]);
        });
    }
};
