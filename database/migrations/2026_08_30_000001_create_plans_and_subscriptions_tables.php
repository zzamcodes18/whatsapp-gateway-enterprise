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
        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->string('slug', 100)->unique();
                $table->text('description')->nullable();
                $table->unsignedInteger('price')->default(0); // IDR per bulan
                $table->unsignedInteger('duration_days')->default(30); // masa aktif subscription
                $table->unsignedInteger('device_limit')->default(3); // 0 = unlimited
                $table->unsignedInteger('daily_message_limit')->default(500); // 0 = unlimited
                $table->unsignedInteger('monthly_message_limit')->default(0); // 0 = unlimited
                $table->boolean('is_active')->default(true);
                $table->boolean('is_default')->default(false); // plan default untuk user baru
                $table->integer('sort_order')->default(0); // signed: nilai negatif untuk plan sistem (admin)
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
                $table->string('status', 20)->default('active'); // active | expired | cancelled
                $table->dateTime('starts_at')->nullable();
                $table->dateTime('ends_at')->nullable();
                $table->string('assigned_by')->nullable(); // email admin yang assign
                $table->text('note')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'status']);
                $table->index('ends_at');
            });
        }

        if (!Schema::hasColumn('users', 'plan_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('plan_id')->nullable()->after('is_active')->constrained('plans')->nullOnDelete();
                $table->dateTime('plan_expires_at')->nullable()->after('plan_id'); // dateTime: support tanggal permanen > 2038
            });
        }

        if (!Schema::hasColumn('users', 'monthly_message_limit')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('monthly_message_limit')->default(0)->after('daily_message_limit'); // 0 = unlimited
                $table->unsignedInteger('messages_sent_this_month')->default(0)->after('monthly_message_limit');
                $table->date('last_monthly_reset_at')->nullable()->after('messages_sent_this_month');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'plan_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('plan_id');
                $table->dropColumn('plan_expires_at');
            });
        }

        if (Schema::hasColumn('users', 'monthly_message_limit')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn([
                    'monthly_message_limit',
                    'messages_sent_this_month',
                    'last_monthly_reset_at',
                ]);
            });
        }

        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
