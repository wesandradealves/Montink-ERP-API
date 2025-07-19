<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Models\User;

class JwtService
{
    private string $secret;
    private string $algo = 'HS256';
    private int $ttl = 3600; // 1 hour

    public function __construct()
    {
        $this->secret = config('jwt.secret', env('JWT_SECRET'));
    }

    public function generateToken(User $user): string
    {
        $header = $this->base64UrlEncode(json_encode([
            'typ' => 'JWT',
            'alg' => $this->algo
        ]));

        $payload = $this->base64UrlEncode(json_encode([
            'iss' => config('app.url'),
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + $this->ttl,
            'name' => $user->name,
            'email' => $user->email,
        ]));

        $signature = $this->base64UrlEncode(
            hash_hmac('sha256', $header . '.' . $payload, $this->secret, true)
        );

        return $header . '.' . $payload . '.' . $signature;
    }

    public function validateToken(string $token): array
    {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            throw new \InvalidArgumentException('Token inválido');
        }

        list($header, $payload, $signature) = $parts;

        $validSignature = $this->base64UrlEncode(
            hash_hmac('sha256', $header . '.' . $payload, $this->secret, true)
        );

        if ($signature !== $validSignature) {
            throw new \InvalidArgumentException('Assinatura inválida');
        }

        $payloadData = json_decode($this->base64UrlDecode($payload), true);

        if (!$payloadData) {
            throw new \InvalidArgumentException('Payload inválido');
        }

        if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
            throw new \InvalidArgumentException('Token expirado');
        }

        return $payloadData;
    }

    public function getTokenExpiration(): int
    {
        return $this->ttl;
    }

    public function invalidateToken(string $token): void
    {
        // Em uma implementação real, adicionaria o token a uma blacklist
        // Por enquanto, apenas valida se o token é válido
        $this->validateToken($token);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}