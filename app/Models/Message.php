<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'remote_jid',
        'message_type',
        'message_content',
        'media_url',
        'direction',
        'status',
        'wa_message_id',
        'error_message',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function getCleanPhoneAttribute(): string
    {
        return explode('@', $this->remote_jid)[0] ?? $this->remote_jid;
    }
}
