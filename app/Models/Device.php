<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'name',
        'phone_number',
        'connection_type',
        'status',
        'is_system_bot',
        'always_online',
        'typing_indicator',
        'auto_read',
        'block_calls',
        'qr_code',
        'pairing_code',
        'metadata',
        'connected_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_system_bot' => 'boolean',
            'always_online' => 'boolean',
            'typing_indicator' => 'boolean',
            'auto_read' => 'boolean',
            'block_calls' => 'boolean',
            'connected_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }

    public function isConnected(): bool
    {
        return $this->status === 'connected';
    }

    public function isStopped(): bool
    {
        return $this->status === 'stopped';
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'connected' => 'app-tag-emerald',
            'connecting' => 'app-tag-amber',
            'qr_ready' => 'app-tag-blue',
            'pairing_ready' => 'app-tag-amber',
            'stopped' => 'app-tag-slate',
            default => 'app-tag-rose',
        };
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'connected' => 'Terhubung',
            'connecting' => 'Menghubungkan',
            'qr_ready' => 'Menunggu QR Scan',
            'pairing_ready' => 'Menunggu Pairing Code',
            'stopped' => 'Dihentikan',
            'disconnected' => 'Terputus',
            default => 'Tidak Diketahui',
        };
    }
}
