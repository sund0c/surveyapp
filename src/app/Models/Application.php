<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'api_key',
        'api_secret',
        'is_active',
    ];

    protected $casts = [
        // Laravel's built-in encrypted cast (AES-256-GCM via APP_KEY).
        // NOT bcrypt - we need the plaintext back to recompute the HMAC on each request.
        'api_secret' => 'encrypted',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    // Never accidentally leak the secret in API responses / Filament tables by default
    protected $hidden = [
        'api_secret',
    ];

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Generate a fresh API key + secret pair.
     * Returns the plaintext secret ONCE - caller must display it to the admin
     * immediately and never persist/log the plaintext value anywhere else.
     */
    public static function generateCredentials(): array
    {
        return [
            'api_key' => 'sk_' . Str::random(48),
            'api_secret' => Str::random(64),
        ];
    }
}
