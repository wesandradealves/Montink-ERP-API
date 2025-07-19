<?php

namespace App\Modules\Auth\Api\Middleware;

use App\Common\Enums\ResponseMessage;
use App\Common\Traits\ApiResponseTrait;
use App\Modules\Auth\Services\JwtService;
use App\Modules\Auth\Models\User;
use Closure;
use Illuminate\Http\Request;

class JwtAuthMiddleware
{
    use ApiResponseTrait;

    public function __construct(
        private readonly JwtService $jwtService
    ) {}

    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->bearerToken();
            
            if (!$token) {
                return $this->errorResponse(
                    message: ResponseMessage::AUTH_TOKEN_NOT_PROVIDED->get(),
                    statusCode: 401
                );
            }

            $payload = $this->jwtService->validateToken($token);
            $user = User::find($payload['sub']);
            
            if (!$user) {
                return $this->errorResponse(
                    message: ResponseMessage::AUTH_USER_NOT_FOUND->get(),
                    statusCode: 401
                );
            }

            if (!$user->isActive()) {
                return $this->errorResponse(
                    message: ResponseMessage::AUTH_USER_INACTIVE->get(),
                    statusCode: 401
                );
            }

            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            return $next($request);
            
        } catch (\InvalidArgumentException $e) {
            if (str_contains($e->getMessage(), 'expirado')) {
                return $this->errorResponse(
                    message: ResponseMessage::AUTH_TOKEN_EXPIRED->get(),
                    statusCode: 401
                );
            }
            
            return $this->errorResponse(
                message: ResponseMessage::AUTH_TOKEN_INVALID->get(),
                statusCode: 401
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                message: ResponseMessage::AUTH_UNAUTHORIZED->get(),
                statusCode: 401
            );
        }
    }
}