<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Modules\Auth\Models\User;
use App\Common\Enums\ResponseMessage;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    private array $testUser = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        
        User::where('email', $this->testUser['email'])->delete();
    }

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => $this->testUser['name'],
            'email' => $this->testUser['email'],
            'password' => $this->testUser['password'],
            'password_confirmation' => $this->testUser['password']
        ]);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'original' => [
                        'data' => [
                            'accessToken',
                            'refreshToken',
                            'tokenType',
                            'expiresIn',
                            'user' => [
                                'id',
                                'name',
                                'email'
                            ]
                        ],
                        'message'
                    ]
                ],
                'message'
            ])
            ->assertJsonPath('data.original.data.tokenType', 'Bearer')
            ->assertJsonPath('data.original.data.user.email', $this->testUser['email'])
            ->assertJsonPath('data.original.message', ResponseMessage::AUTH_REGISTER_SUCCESS->get());
    }

    public function test_cannot_register_with_existing_email(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => $this->testUser['email'],
            'password' => Hash::make('password')
        ]);
        
        $response = $this->postJson('/api/auth/register', [
            'name' => $this->testUser['name'],
            'email' => $this->testUser['email'],
            'password' => $this->testUser['password'],
            'password_confirmation' => $this->testUser['password']
        ]);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_validation(): void
    {
        $response = $this->postJson('/api/auth/register', []);
        
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_user_can_login(): void
    {
        User::create([
            'name' => $this->testUser['name'],
            'email' => $this->testUser['email'],
            'password' => Hash::make($this->testUser['password'])
        ]);
        
        $response = $this->postJson('/api/auth/login', [
            'email' => $this->testUser['email'],
            'password' => $this->testUser['password']
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'original' => [
                        'data' => [
                            'accessToken',
                            'refreshToken',
                            'tokenType',
                            'expiresIn',
                            'user'
                        ],
                        'message'
                    ]
                ],
                'message'
            ])
            ->assertJsonPath('data.original.message', ResponseMessage::AUTH_LOGIN_SUCCESS->get());
    }

    public function test_cannot_login_with_invalid_credentials(): void
    {
        User::create([
            'name' => $this->testUser['name'],
            'email' => $this->testUser['email'],
            'password' => Hash::make($this->testUser['password'])
        ]);
        
        $response = $this->postJson('/api/auth/login', [
            'email' => $this->testUser['email'],
            'password' => 'wrongpassword'
        ]);
        
        $response->assertStatus(401)
            ->assertJson(['error' => ResponseMessage::AUTH_INVALID_CREDENTIALS->get()]);
    }

    public function test_cannot_login_with_inactive_user(): void
    {
        User::create([
            'name' => $this->testUser['name'],
            'email' => $this->testUser['email'],
            'password' => Hash::make($this->testUser['password']),
            'active' => false
        ]);
        
        $response = $this->postJson('/api/auth/login', [
            'email' => $this->testUser['email'],
            'password' => $this->testUser['password']
        ]);
        
        $response->assertStatus(401)
            ->assertJson(['error' => ResponseMessage::AUTH_USER_INACTIVE->get()]);
    }

    public function test_authenticated_user_can_access_protected_route(): void
    {
        $user = User::create([
            'name' => $this->testUser['name'],
            'email' => $this->testUser['email'],
            'password' => Hash::make($this->testUser['password'])
        ]);
        
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $this->testUser['email'],
            'password' => $this->testUser['password']
        ]);
        
        $token = $loginResponse->json('data.original.data.accessToken');
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/auth/me');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at'
                ]
            ])
            ->assertJsonPath('data.email', $this->testUser['email']);
    }

    public function test_cannot_access_protected_route_without_token(): void
    {
        $response = $this->getJson('/api/auth/me');
        
        $response->assertStatus(401)
            ->assertJson(['error' => ResponseMessage::AUTH_TOKEN_NOT_PROVIDED->get()]);
    }

    public function test_cannot_access_protected_route_with_invalid_token(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token-here'
        ])->getJson('/api/auth/me');
        
        $response->assertStatus(401);
    }

    public function test_user_can_logout(): void
    {
        $user = User::create([
            'name' => $this->testUser['name'],
            'email' => $this->testUser['email'],
            'password' => Hash::make($this->testUser['password'])
        ]);
        
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $this->testUser['email'],
            'password' => $this->testUser['password']
        ]);
        
        $token = $loginResponse->json('data.original.data.accessToken');
        $refreshToken = $loginResponse->json('data.original.data.refreshToken');
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/auth/logout', [
            'refresh_token' => $refreshToken
        ]);
        
        $response->assertStatus(200)
            ->assertJsonPath('data.original.message', ResponseMessage::AUTH_LOGOUT_SUCCESS->get());
    }

    public function test_user_can_refresh_token(): void
    {
        $user = User::create([
            'name' => $this->testUser['name'],
            'email' => $this->testUser['email'],
            'password' => Hash::make($this->testUser['password'])
        ]);
        
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $this->testUser['email'],
            'password' => $this->testUser['password']
        ]);
        
        $refreshToken = $loginResponse->json('data.original.data.refreshToken');
        
        $response = $this->postJson('/api/auth/refresh', [
            'refresh_token' => $refreshToken
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'original' => [
                        'data' => [
                            'accessToken',
                            'refreshToken',
                            'tokenType',
                            'expiresIn'
                        ],
                        'message'
                    ]
                ],
                'message'
            ])
            ->assertJsonPath('data.original.message', ResponseMessage::AUTH_TOKEN_REFRESHED->get());
    }

    protected function tearDown(): void
    {
        User::where('email', $this->testUser['email'])->delete();
        
        parent::tearDown();
    }
}