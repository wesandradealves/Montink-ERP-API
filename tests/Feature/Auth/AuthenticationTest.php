<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Modules\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_successfully(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
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
            ])
            ->assertJson([
                'data' => [
                    'tokenType' => 'Bearer',
                    'user' => [
                        'name' => 'Test User',
                        'email' => 'test@example.com'
                    ]
                ],
                'message' => 'Usuário registrado com sucesso'
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User'
        ]);
    }

    public function test_registration_fails_with_existing_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/auth/login', $credentials);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'accessToken',
                    'refreshToken',
                    'tokenType',
                    'expiresIn',
                    'user'
                ],
                'message'
            ])
            ->assertJson([
                'message' => 'Login realizado com sucesso'
            ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/auth/login', $credentials);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Credenciais inválidas'
            ]);
    }

    public function test_login_fails_with_inactive_user(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'active' => false
        ]);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $response = $this->postJson('/api/auth/login', $credentials);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Usuário inativo'
            ]);
    }

    public function test_authenticated_user_can_get_profile(): void
    {
        $user = User::factory()->create();
        
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $token = $loginResponse->json('data.accessToken');

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
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email
                ]
            ]);
    }

    public function test_unauthenticated_user_cannot_access_protected_route(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Token não fornecido'
            ]);
    }

    public function test_user_can_logout_successfully(): void
    {
        $user = User::factory()->create();
        
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $token = $loginResponse->json('data.accessToken');
        $refreshToken = $loginResponse->json('data.refreshToken');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/auth/logout', [
            'refresh_token' => $refreshToken
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout realizado com sucesso'
            ]);
    }

    public function test_user_can_refresh_token(): void
    {
        $user = User::factory()->create();
        
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $refreshToken = $loginResponse->json('data.refreshToken');

        $response = $this->postJson('/api/auth/refresh', [
            'refresh_token' => $refreshToken
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'accessToken',
                    'refreshToken',
                    'tokenType',
                    'expiresIn'
                ],
                'message'
            ])
            ->assertJson([
                'message' => 'Token atualizado com sucesso'
            ]);
    }

    public function test_registration_validation_rules(): void
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'a',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}