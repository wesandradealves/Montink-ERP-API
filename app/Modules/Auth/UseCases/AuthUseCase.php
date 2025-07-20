<?php

namespace App\Modules\Auth\UseCases;

use App\Common\Enums\ResponseMessage;
use App\Common\Exceptions\AuthenticationException;
use App\Modules\Auth\DTOs\AuthResponseDTO;
use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\DTOs\RegisterDTO;
use App\Modules\Auth\DTOs\UserDTO;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Services\JwtService;
use App\Modules\Auth\Services\RefreshTokenService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthUseCase
{
    public function __construct(
        private readonly JwtService $jwtService,
        private readonly RefreshTokenService $refreshTokenService
    ) {}

    public function login(LoginDTO $dto): AuthResponseDTO
    {
        $user = User::where('email', $dto->email)->first();

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw new AuthenticationException(ResponseMessage::AUTH_INVALID_CREDENTIALS->get());
        }

        if (!$user->isActive()) {
            throw new AuthenticationException(ResponseMessage::AUTH_USER_INACTIVE->get());
        }

        return $this->generateAuthResponse($user, $dto->ipAddress, $dto->userAgent);
    }

    public function register(RegisterDTO $dto): AuthResponseDTO
    {
        return DB::transaction(function () use ($dto) {
            $existingUser = User::where('email', $dto->email)->first();
            
            if ($existingUser) {
                throw new \InvalidArgumentException(ResponseMessage::AUTH_EMAIL_ALREADY_EXISTS->get());
            }

            $user = User::create([
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => Hash::make($dto->password),
                'active' => true,
            ]);

            return $this->generateAuthResponse($user);
        });
    }

    public function refreshToken(string $refreshToken): AuthResponseDTO
    {
        $token = $this->refreshTokenService->findByToken($refreshToken);

        if (!$token || !$token->isValid()) {
            throw new AuthenticationException(ResponseMessage::AUTH_TOKEN_INVALID->get());
        }

        $user = $token->user;

        if (!$user->isActive()) {
            throw new AuthenticationException(ResponseMessage::AUTH_USER_INACTIVE->get());
        }

        $this->refreshTokenService->revokeToken($refreshToken);

        return $this->generateAuthResponse($user, $token->ip_address, $token->user_agent);
    }

    public function logout(string $accessToken, string $refreshToken): void
    {
        $this->jwtService->invalidateToken($accessToken);
        $this->refreshTokenService->revokeToken($refreshToken);
    }

    public function logoutAllDevices(User $user): void
    {
        $this->refreshTokenService->revokeUserTokens($user->id);
    }

    private function generateAuthResponse(User $user, ?string $ipAddress = null, ?string $userAgent = null): AuthResponseDTO
    {
        $accessToken = $this->jwtService->generateToken($user);
        $refreshToken = $this->refreshTokenService->create($user, $ipAddress, $userAgent);

        return new AuthResponseDTO(
            accessToken: $accessToken,
            refreshToken: $refreshToken->token,
            tokenType: 'Bearer',
            expiresIn: $this->jwtService->getTokenExpiration(),
            user: UserDTO::fromModel($user)
        );
    }
}