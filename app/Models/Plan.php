<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration_days',
        'device_limit',
        'daily_message_limit',
        'monthly_message_limit',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'duration_days' => 'integer',
            'device_limit' => 'integer',
            'daily_message_limit' => 'integer',
            'monthly_message_limit' => 'integer',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function formatPrice(): string
    {
        if ($this->price <= 0) {
            return 'Gratis';
        }

        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function formatDeviceLimit(): string
    {
        return $this->device_limit > 0 ? $this->device_limit . ' Perangkat' : 'Unlimited';
    }

    public function formatMessageLimit(): string
    {
        return $this->daily_message_limit > 0 ? number_format($this->daily_message_limit, 0, ',', '.') . ' pesan/hari' : 'Unlimited';
    }

    public function formatMonthlyMessageLimit(): string
    {
        return $this->monthly_message_limit > 0 ? number_format($this->monthly_message_limit, 0, ',', '.') . ' pesan/bulan' : 'Unlimited';
    }
}
