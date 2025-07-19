<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Models\RefreshToken;
use App\Modules\Auth\Models\User;
use Carbon\Carbon;

class RefreshTokenService
{
    private const REFRESH_TOKEN_TTL_DAYS = 30;

    public function create(User $user, ?string $ipAddress = null, ?string $userAgent = null): RefreshToken
    {
        $this->revokeUserTokens($user->id);

        return RefreshToken::create([
            'user_id' => $user->id,
            'token' => RefreshToken::generateToken(),
            'expires_at' => Carbon::now()->addDays(self::REFRESH_TOKEN_TTL_DAYS),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    public function findByToken(string $token): ?RefreshToken
    {
        return RefreshToken::where('token', $token)->first();
    }

    public function validateToken(string $token): bool
    {
        $refreshToken = $this->findByToken($token);
        
        if (!$refreshToken) {
            return false;
        }

        return $refreshToken->isValid();
    }

    public function revokeToken(string $token): bool
    {
        $refreshToken = $this->findByToken($token);
        
        if (!$refreshToken) {
            return false;
        }

        $refreshToken->revoke();
        return true;
    }

    public function revokeUserTokens(int $userId): void
    {
        RefreshToken::where('user_id', $userId)
            ->where('revoked', false)
            ->update(['revoked' => true]);
    }

    public function cleanupExpiredTokens(): int
    {
        return RefreshToken::where('expires_at', '<', Carbon::now())
            ->orWhere('revoked', true)
            ->delete();
    }
}