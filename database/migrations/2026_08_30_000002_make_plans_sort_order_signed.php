<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah kolom sort_order menjadi signed integer agar plan sistem
     * (Admin, sort_order = -1) bisa disimpan. Memperbaiki tabel yang
     * sudah terlanjur dibuat dengan unsigned integer.
     */
    public function up(): void
    {
        if (Schema::hasTable('plans') && Schema::hasColumn('plans', 'sort_order')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->integer('sort_order')->default(0)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('plans') && Schema::hasColumn('plans', 'sort_order')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->change();
            });
        }
    }
};
