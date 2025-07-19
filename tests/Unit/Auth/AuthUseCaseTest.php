<?php

namespace Tests\Unit\Auth;

use Tests\TestCase;
use App\Modules\Auth\UseCases\AuthUseCase;
use App\Modules\Auth\Repositories\UserRepositoryInterface;
use App\Services\Auth\JwtService;
use App\Modules\Auth\Models\User;
use App\Common\Exceptions\BusinessException;
use Illuminate\Support\Facades\Hash;
use Mockery;

class AuthUseCaseTest extends TestCase
{
    private AuthUseCase $authUseCase;
    private $userRepositoryMock;
    private $jwtServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $this->jwtServiceMock = Mockery::mock(JwtService::class);
        
        $this->authUseCase = new AuthUseCase(
            $this->userRepositoryMock,
            $this->jwtServiceMock
        );
    }

    public function test_register_user_successfully(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $user = new User($userData);
        $user->id = 1;
        $user->active = true;

        $tokens = [
            'access_token' => 'fake_access_token',
            'refresh_token' => 'fake_refresh_token',
            'token_type' => 'Bearer',
            'expires_in' => 3600
        ];

        $this->userRepositoryMock
            ->shouldReceive('findByEmail')
            ->with('test@example.com')
            ->once()
            ->andReturn(null);

        $this->userRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->andReturn($user);

        $this->jwtServiceMock
            ->shouldReceive('generateTokenPair')
            ->with($user)
            ->once()
            ->andReturn($tokens);

        $result = $this->authUseCase->register($userData);

        $this->assertEquals($tokens, $result['tokens']);
        $this->assertEquals($user->toArray(), $result['user']);
    }

    public function test_register_with_existing_email_fails(): void
    {
        $this->expectException(BusinessException::class);

        $userData = [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123'
        ];

        $existingUser = new User(['email' => 'existing@example.com']);

        $this->userRepositoryMock
            ->shouldReceive('findByEmail')
            ->with('existing@example.com')
            ->once()
            ->andReturn($existingUser);

        $this->authUseCase->register($userData);
    }

    public function test_login_successfully(): void
    {
        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $user = new User([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'active' => true
        ]);
        $user->id = 1;

        $tokens = [
            'access_token' => 'fake_access_token',
            'refresh_token' => 'fake_refresh_token',
            'token_type' => 'Bearer',
            'expires_in' => 3600
        ];

        $this->userRepositoryMock
            ->shouldReceive('findByEmail')
            ->with('test@example.com')
            ->once()
            ->andReturn($user);

        $this->jwtServiceMock
            ->shouldReceive('generateTokenPair')
            ->with($user)
            ->once()
            ->andReturn($tokens);

        $result = $this->authUseCase->login($credentials);

        $this->assertEquals($tokens, $result['tokens']);
        $this->assertEquals($user->toArray(), $result['user']);
    }

    public function test_login_with_invalid_credentials_fails(): void
    {
        $this->expectException(BusinessException::class);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        $user = new User([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'active' => true
        ]);

        $this->userRepositoryMock
            ->shouldReceive('findByEmail')
            ->with('test@example.com')
            ->once()
            ->andReturn($user);

        $this->authUseCase->login($credentials);
    }

    public function test_login_with_inactive_user_fails(): void
    {
        $this->expectException(BusinessException::class);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $user = new User([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'active' => false
        ]);

        $this->userRepositoryMock
            ->shouldReceive('findByEmail')
            ->with('test@example.com')
            ->once()
            ->andReturn($user);

        $this->authUseCase->login($credentials);
    }

    public function test_logout_successfully(): void
    {
        $refreshToken = 'fake_refresh_token';

        $this->jwtServiceMock
            ->shouldReceive('invalidateRefreshToken')
            ->with($refreshToken)
            ->once();

        $this->authUseCase->logout($refreshToken);

        $this->assertTrue(true);
    }

    public function test_refresh_token_successfully(): void
    {
        $refreshToken = 'fake_refresh_token';
        $userId = 1;

        $user = new User(['id' => $userId, 'email' => 'test@example.com']);

        $newTokens = [
            'access_token' => 'new_access_token',
            'refresh_token' => 'new_refresh_token',
            'token_type' => 'Bearer',
            'expires_in' => 3600
        ];

        $this->jwtServiceMock
            ->shouldReceive('validateRefreshToken')
            ->with($refreshToken, $userId)
            ->once()
            ->andReturn(true);

        $this->userRepositoryMock
            ->shouldReceive('find')
            ->with($userId)
            ->once()
            ->andReturn($user);

        $this->jwtServiceMock
            ->shouldReceive('invalidateRefreshToken')
            ->with($refreshToken)
            ->once();

        $this->jwtServiceMock
            ->shouldReceive('generateTokenPair')
            ->with($user)
            ->once()
            ->andReturn($newTokens);

        $result = $this->authUseCase->refreshToken($refreshToken, $userId);

        $this->assertEquals($newTokens, $result);
    }

    public function test_get_me_returns_user_data(): void
    {
        $userId = 1;
        $user = new User([
            'id' => $userId,
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);

        $this->userRepositoryMock
            ->shouldReceive('find')
            ->with($userId)
            ->once()
            ->andReturn($user);

        $result = $this->authUseCase->getMe($userId);

        $this->assertEquals($user->toArray(), $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}