<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE messages MODIFY COLUMN message_type VARCHAR(30) NOT NULL DEFAULT 'text'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE messages MODIFY COLUMN message_type ENUM('text', 'image', 'document', 'video', 'audio', 'location', 'button') NOT NULL DEFAULT 'text'");
    }
};
