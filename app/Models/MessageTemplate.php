<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'title',
        'content',
        'footer',
        'buttons',
        'is_active',
    ];

    protected $casts = [
        'buttons' => 'array',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Replace placeholders like {otp}, {name}, {code} with actual variables.
     */
    public static function renderPlaceholders(?string $text, array $variables = []): ?string
    {
        if (empty($text)) {
            return $text;
        }

        foreach ($variables as $key => $val) {
            if (is_scalar($val)) {
                $text = str_replace('{' . $key . '}', (string) $val, $text);
            }
        }

        return $text;
    }
}
