<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'google_id',
        'github_id',
        'phone_number',
        'role',
        'avatar',
        'password',
        'is_active',
        'plan_id',
        'plan_expires_at',
        'device_limit',
        'daily_message_limit',
        'monthly_message_limit',
        'messages_sent_today',
        'messages_sent_this_month',
        'last_limit_reset_at',
        'last_monthly_reset_at',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_limit_reset_at' => 'date',
            'plan_expires_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'device_limit' => 'integer',
            'daily_message_limit' => 'integer',
            'monthly_message_limit' => 'integer',
            'messages_sent_today' => 'integer',
            'messages_sent_this_month' => 'integer',
            'last_monthly_reset_at' => 'date',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Cek apakah subscription user masih aktif (belum expired).
     */
    public function hasActivePlan(): bool
    {
        if ($this->isAdmin() || $this->plan_id === null) {
            return true;
        }

        return $this->plan_expires_at === null || $this->plan_expires_at->isFuture();
    }

    /**
     * Assign plan ke user: batalkan subscription aktif lama, set plan baru + catat riwayat.
     */
    public function assignPlan(Plan $plan, ?string $assignedBy = null, ?string $note = null): void
    {
        $this->subscriptions()->where('status', 'active')->update(['status' => 'cancelled']);

        $this->update([
            'plan_id' => $plan->id,
            'plan_expires_at' => now()->addDays($plan->duration_days),
        ]);

        $this->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays($plan->duration_days),
            'assigned_by' => $assignedBy,
            'note' => $note,
        ]);
    }

    /**
     * Effective device limit: admin selalu unlimited, plan aktif override limit manual.
     */
    public function effectiveDeviceLimit(): int
    {
        if ($this->isAdmin()) {
            return 0; // unlimited
        }

        if ($this->plan_id && $this->hasActivePlan() && $this->plan) {
            return $this->plan->device_limit;
        }

        return $this->device_limit ?? 3;
    }

    /**
     * Effective daily message limit: admin selalu unlimited, plan aktif override limit manual.
     */
    public function effectiveDailyMessageLimit(): int
    {
        if ($this->isAdmin()) {
            return 0; // unlimited
        }

        if ($this->plan_id && $this->hasActivePlan() && $this->plan) {
            return $this->plan->daily_message_limit;
        }

        return $this->daily_message_limit ?? 500;
    }

    /**
     * Effective monthly message limit: admin selalu unlimited, plan aktif override limit manual.
     */
    public function effectiveMonthlyMessageLimit(): int
    {
        if ($this->isAdmin()) {
            return 0; // unlimited
        }

        if ($this->plan_id && $this->hasActivePlan() && $this->plan) {
            return $this->plan->monthly_message_limit;
        }

        return $this->monthly_message_limit ?? 0;
    }

    public function checkAndResetDailyLimit(): void
    {
        $today = now()->toDateString();
        if ($this->last_limit_reset_at?->toDateString() !== $today) {
            $this->update([
                'messages_sent_today' => 0,
                'last_limit_reset_at' => $today,
            ]);
        }
    }

    public function checkAndResetMonthlyLimit(): void
    {
        $thisMonth = now()->startOfMonth()->toDateString();
        if ($this->last_monthly_reset_at?->toDateString() !== $thisMonth) {
            $this->update([
                'messages_sent_this_month' => 0,
                'last_monthly_reset_at' => $thisMonth,
            ]);
        }
    }

    public function canCreateDevice(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $limit = $this->effectiveDeviceLimit();

        if ($limit <= 0) {
            return true; // 0 = unlimited
        }

        return $this->devices()->count() < $limit;
    }

    public function canSendMessage(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $this->checkAndResetDailyLimit();
        $this->checkAndResetMonthlyLimit();

        // Cek limit harian
        $dailyLimit = $this->effectiveDailyMessageLimit();
        if ($dailyLimit > 0 && $this->messages_sent_today >= $dailyLimit) {
            return false;
        }

        // Cek limit bulanan
        $monthlyLimit = $this->effectiveMonthlyMessageLimit();
        if ($monthlyLimit > 0 && $this->messages_sent_this_month >= $monthlyLimit) {
            return false;
        }

        return true;
    }

    public function incrementMessageCount(): void
    {
        $this->checkAndResetDailyLimit();
        $this->checkAndResetMonthlyLimit();
        $this->increment('messages_sent_today');
        $this->increment('messages_sent_this_month');
    }

    /**
     * Alasan kenapa user tidak bisa kirim pesan (untuk pesan error yang jelas).
     */
    public function sendMessageBlockReason(): ?string
    {
        if ($this->isAdmin()) {
            return null;
        }

        $this->checkAndResetDailyLimit();
        $this->checkAndResetMonthlyLimit();

        $dailyLimit = $this->effectiveDailyMessageLimit();
        if ($dailyLimit > 0 && $this->messages_sent_today >= $dailyLimit) {
            return "Limit harian tercapai ({$dailyLimit} pesan/hari). Limit akan direset besok pagi.";
        }

        $monthlyLimit = $this->effectiveMonthlyMessageLimit();
        if ($monthlyLimit > 0 && $this->messages_sent_this_month >= $monthlyLimit) {
            return "Limit bulanan tercapai (".number_format($monthlyLimit, 0, ',', '.')." pesan/bulan). Limit akan direset awal bulan depan atau upgrade paket Anda.";
        }

        return null;
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function messageTemplates(): HasMany
    {
        return $this->hasMany(MessageTemplate::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function logActivity(string $event, string $description, ?array $context = null): ActivityLog
    {
        return $this->activityLogs()->create([
            'event' => $event,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context_data' => $context,
        ]);
    }
}
