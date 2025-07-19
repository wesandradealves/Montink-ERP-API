<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\Auth\JwtService;
use App\Modules\Auth\Models\User;
use App\Common\Exceptions\TokenException;
use Illuminate\Support\Facades\Cache;

class JwtServiceTest extends TestCase
{
    private JwtService $jwtService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->jwtService = new JwtService();
        
        $this->user = new User();
        $this->user->id = 1;
        $this->user->email = 'test@example.com';
        $this->user->name = 'Test User';
    }

    public function test_can_generate_access_token(): void
    {
        $token = $this->jwtService->generateAccessToken($this->user);
        
        $this->assertNotEmpty($token);
        $this->assertIsString($token);
        
        $parts = explode('.', $token);
        $this->assertCount(3, $parts);
    }

    public function test_can_generate_token_pair(): void
    {
        $tokens = $this->jwtService->generateTokenPair($this->user);
        
        $this->assertArrayHasKey('access_token', $tokens);
        $this->assertArrayHasKey('refresh_token', $tokens);
        $this->assertArrayHasKey('token_type', $tokens);
        $this->assertArrayHasKey('expires_in', $tokens);
        
        $this->assertEquals('Bearer', $tokens['token_type']);
        $this->assertIsInt($tokens['expires_in']);
        $this->assertGreaterThan(0, $tokens['expires_in']);
    }

    public function test_can_validate_valid_token(): void
    {
        $token = $this->jwtService->generateAccessToken($this->user);
        
        $decoded = $this->jwtService->validateToken($token);
        
        $this->assertIsObject($decoded);
        $this->assertEquals($this->user->id, $decoded->sub);
        $this->assertEquals($this->user->email, $decoded->email);
    }

    public function test_throws_exception_for_invalid_token_format(): void
    {
        $this->expectException(TokenException::class);
        
        $this->jwtService->validateToken('invalid.token');
    }

    public function test_throws_exception_for_token_with_invalid_signature(): void
    {
        $this->expectException(TokenException::class);
        
        $validToken = $this->jwtService->generateAccessToken($this->user);
        $parts = explode('.', $validToken);
        $invalidToken = $parts[0] . '.' . $parts[1] . '.invalidsignature';
        
        $this->jwtService->validateToken($invalidToken);
    }

    public function test_throws_exception_for_expired_token(): void
    {
        $this->expectException(TokenException::class);
        
        $expiredPayload = [
            'sub' => $this->user->id,
            'email' => $this->user->email,
            'iat' => time() - 7200,
            'exp' => time() - 3600
        ];
        
        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = base64_encode(json_encode($expiredPayload));
        $signature = hash_hmac('sha256', $header . '.' . $payload, config('auth.jwt.secret'), true);
        $signature = base64_encode($signature);
        
        $expiredToken = $header . '.' . $payload . '.' . $signature;
        
        $this->jwtService->validateToken($expiredToken);
    }

    public function test_can_validate_refresh_token(): void
    {
        $tokens = $this->jwtService->generateTokenPair($this->user);
        $refreshToken = $tokens['refresh_token'];
        
        $isValid = $this->jwtService->validateRefreshToken($refreshToken, $this->user->id);
        
        $this->assertTrue($isValid);
    }

    public function test_can_invalidate_refresh_token(): void
    {
        $tokens = $this->jwtService->generateTokenPair($this->user);
        $refreshToken = $tokens['refresh_token'];
        
        $this->jwtService->invalidateRefreshToken($refreshToken);
        
        $isValid = $this->jwtService->validateRefreshToken($refreshToken, $this->user->id);
        
        $this->assertFalse($isValid);
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }
}