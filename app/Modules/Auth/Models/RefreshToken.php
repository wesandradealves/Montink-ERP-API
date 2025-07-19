<?php

namespace App\Modules\Auth\Models;

use App\Common\Base\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RefreshToken extends BaseModel
{
    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'revoked',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isRevoked();
    }

    public function revoke(): void
    {
        $this->update(['revoked' => true]);
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(40));
    }
}