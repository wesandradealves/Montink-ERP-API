<?php

namespace App\Modules\Auth\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class JwtGuard implements Guard
{
    protected ?Authenticatable $user = null;
    protected UserProvider $provider;
    protected JwtService $jwtService;
    protected Request $request;

    public function __construct(
        UserProvider $provider,
        JwtService $jwtService,
        Request $request
    ) {
        $this->provider = $provider;
        $this->jwtService = $jwtService;
        $this->request = $request;
    }

    public function check(): bool
    {
        return !is_null($this->user());
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function hasUser(): bool
    {
        return !is_null($this->user);
    }

    public function user(): ?Authenticatable
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        $token = $this->getTokenFromRequest();
        
        if (!$token) {
            return null;
        }

        try {
            $payload = $this->jwtService->validateToken($token);
            $this->user = $this->provider->retrieveById($payload['sub']);
        } catch (\Exception $e) {
            $this->user = null;
        }

        return $this->user;
    }

    public function id()
    {
        if ($user = $this->user()) {
            return $user->getAuthIdentifier();
        }

        return null;
    }

    public function validate(array $credentials = []): bool
    {
        return $this->provider->retrieveByCredentials($credentials) !== null;
    }

    public function setUser(Authenticatable $user): void
    {
        $this->user = $user;
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;
        return $this;
    }

    protected function getTokenFromRequest(): ?string
    {
        $token = $this->request->bearerToken();
        
        if (!$token) {
            $token = $this->request->input('token');
        }

        return $token;
    }
}