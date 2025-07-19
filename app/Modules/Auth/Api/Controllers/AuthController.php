<?php

namespace App\Modules\Auth\Api\Controllers;

use App\Common\Base\BaseApiController;
use App\Common\Enums\ResponseMessage;
use App\Modules\Auth\Api\Requests\LoginRequest;
use App\Modules\Auth\Api\Requests\RegisterRequest;
use App\Modules\Auth\Api\Requests\RefreshTokenRequest;
use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\DTOs\RegisterDTO;
use App\Modules\Auth\UseCases\AuthUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="Endpoints de autenticação"
 * )
 */
class AuthController extends BaseApiController
{
    public function __construct(
        private readonly AuthUseCase $authUseCase
    ) {}

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     operationId="login",
     *     tags={"Auth"},
     *     summary="Fazer login",
     *     description="Autentica um usuário e retorna tokens de acesso",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login realizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas"
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        return $this->handleUseCaseExecution(function () use ($request) {
            $dto = new LoginDTO(
                email: $request->input('email'),
                password: $request->input('password'),
                ipAddress: $request->input('ip_address'),
                userAgent: $request->input('user_agent')
            );

            $response = $this->authUseCase->login($dto);

            return $this->successResponse(
                data: $response->toArray(),
                message: ResponseMessage::AUTH_LOGIN_SUCCESS->get()
            );
        });
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     operationId="register",
     *     tags={"Auth"},
     *     summary="Registrar novo usuário",
     *     description="Cria um novo usuário e retorna tokens de acesso",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegisterRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário registrado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->handleUseCaseCreation(function () use ($request) {
            $dto = new RegisterDTO(
                name: $request->input('name'),
                email: $request->input('email'),
                password: $request->input('password')
            );

            $response = $this->authUseCase->register($dto);

            return $this->successResponse(
                data: $response->toArray(),
                message: ResponseMessage::AUTH_REGISTER_SUCCESS->get()
            );
        });
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     operationId="refreshToken",
     *     tags={"Auth"},
     *     summary="Atualizar token de acesso",
     *     description="Usa o refresh token para obter um novo access token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RefreshTokenRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token atualizado com sucesso",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
     *     )
     * )
     */
    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        return $this->handleUseCaseExecution(function () use ($request) {
            $response = $this->authUseCase->refreshToken($request->input('refresh_token'));

            return $this->successResponse(
                data: $response->toArray(),
                message: ResponseMessage::AUTH_TOKEN_REFRESHED->get()
            );
        });
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     operationId="logout",
     *     tags={"Auth"},
     *     summary="Fazer logout",
     *     description="Invalida os tokens do usuário",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"refresh_token"},
     *             @OA\Property(property="refresh_token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logout realizado com sucesso"
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        return $this->handleUseCaseExecution(function () use ($request) {
            $accessToken = $request->bearerToken() ?? '';
            $refreshToken = $request->input('refresh_token', '');

            $this->authUseCase->logout($accessToken, $refreshToken);

            return $this->successResponse(
                message: ResponseMessage::AUTH_LOGOUT_SUCCESS->get()
            );
        });
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     operationId="me",
     *     tags={"Auth"},
     *     summary="Obter dados do usuário autenticado",
     *     description="Retorna os dados do usuário atual",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dados do usuário",
     *         @OA\JsonContent(ref="#/components/schemas/UserResponse")
     *     )
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            data: $request->user()->toArray()
        );
    }
}