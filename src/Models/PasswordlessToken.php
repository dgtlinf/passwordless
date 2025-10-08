<?php

namespace Dgtlinf\Passwordless\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class PasswordlessToken extends Model
{
    use HasFactory, MassPrunable;

    /**
     * Determine table name dynamically via config.
     */
    public function getTable()
    {
        return config('passwordless.models.table', parent::getTable());
    }

    protected $fillable = [
        'user_id',
        'token',
        'otp_code',
        'expires_at',
        'consumed_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];

    /**
     * Relationship to user model defined in config.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('passwordless.user_model'));
    }

    /**
     * Check if the token has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }

    /**
     * Check if the token has been consumed (used).
     */
    public function isConsumed(): bool
    {
        return ! is_null($this->consumed_at);
    }

    /**
     * Consume the token (mark as used).
     */
    public function consume(): void
    {
        $this->update(['consumed_at' => now()]);
    }

    /**
     * Determine if the token is valid for authentication.
     */
    public function isValid(): bool
    {
        return ! $this->isConsumed() && ! $this->isExpired();
    }

    /**
     * Scope: active tokens (not consumed and not expired).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('consumed_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope: expired tokens.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    /**
     * Static helper to purge expired or consumed tokens.
     */
    public static function purgeExpired(): int
    {
        return static::query()
            ->where(function ($q) {
                $q->whereNotNull('consumed_at')
                    ->orWhere('expires_at', '<=', now());
            })
            ->delete();
    }

    /**
     * Generate expiration timestamp based on config.
     */
    public static function generateExpiration(string $type = 'link'): Carbon
    {
        $minutes = config("passwordless.{$type}.expires_in", 30);
        return now()->addMinutes($minutes);
    }

    /**
     * Define which records should be pruned automatically (MassPrunable).
     */
    public function prunable(): Builder
    {
        if (! config('passwordless.prune.enabled', true)) {
            // If pruning is disabled, return empty query
            return static::query()->whereRaw('1 = 0');
        }

        $days = config('passwordless.prune.keep_days', 7);

        return static::query()
            ->where(function ($q) {
                $q->whereNotNull('consumed_at')
                    ->orWhere('expires_at', '<=', now());
            })
            ->orWhere('created_at', '<=', now()->subDays($days));
    }
}
