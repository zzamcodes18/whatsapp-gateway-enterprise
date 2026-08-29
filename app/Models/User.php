<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'phone_number',
        'role',
        'avatar',
        'password',
        'is_active',
        'device_limit',
        'daily_message_limit',
        'messages_sent_today',
        'last_limit_reset_at',
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
            'password' => 'hashed',
            'is_active' => 'boolean',
            'device_limit' => 'integer',
            'daily_message_limit' => 'integer',
            'messages_sent_today' => 'integer',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
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

    public function canCreateDevice(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (($this->device_limit ?? 3) <= 0) {
            return true; // 0 = unlimited
        }

        return $this->devices()->count() < ($this->device_limit ?? 3);
    }

    public function canSendMessage(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $this->checkAndResetDailyLimit();

        if ($this->daily_message_limit <= 0) {
            return true; // unlimited
        }

        return $this->messages_sent_today < $this->daily_message_limit;
    }

    public function incrementMessageCount(): void
    {
        $this->checkAndResetDailyLimit();
        $this->increment('messages_sent_today');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
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
