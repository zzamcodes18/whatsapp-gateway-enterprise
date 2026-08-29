<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'key_hash',
        'key_prefix',
        'permissions',
        'rate_limit_per_minute',
        'last_used_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'rate_limit_per_minute' => 'integer',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generate(User $user, string $name, array $permissions = ['send_message', 'read_status'], int $rateLimit = 60): array
    {
        $rawKey = 'lpk_'.Str::random(40);
        $prefix = substr($rawKey, 0, 10);
        $hash = hash('sha256', $rawKey);

        $apiKey = self::create([
            'user_id' => $user->id,
            'name' => $name,
            'key_hash' => $hash,
            'key_prefix' => $prefix,
            'permissions' => $permissions,
            'rate_limit_per_minute' => $rateLimit,
            'is_active' => true,
        ]);

        return [
            'model' => $apiKey,
            'plain_text_token' => $rawKey,
        ];
    }
}
