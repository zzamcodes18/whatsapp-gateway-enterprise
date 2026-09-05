<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom fitur device (always online, typing indicator,
     * auto read, block calls) + status "stopped" untuk stop/start sesi.
     */
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->boolean('always_online')->default(false)->after('is_system_bot');
            $table->boolean('typing_indicator')->default(false)->after('always_online');
            $table->boolean('auto_read')->default(false)->after('typing_indicator');
            $table->boolean('block_calls')->default(false)->after('auto_read');
        });

        // Tambah nilai enum "stopped" untuk sesi yang dihentikan manual (kredensial tetap tersimpan)
        Schema::table('devices', function (Blueprint $table) {
            $table->enum('status', ['disconnected', 'qr_ready', 'pairing_ready', 'connecting', 'connected', 'stopped'])->default('disconnected')->change();
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['always_online', 'typing_indicator', 'auto_read', 'block_calls']);
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->enum('status', ['disconnected', 'qr_ready', 'pairing_ready', 'connecting', 'connected'])->default('disconnected')->change();
        });
    }
};
