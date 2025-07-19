<?php

namespace App\Http\Schemas;

/**
 * @OA\Schema(
 *     schema="AuthResponse",
 *     title="Auth Response",
 *     description="Resposta de autenticação com tokens",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="accessToken", type="string", description="Token JWT para autenticação"),
 *         @OA\Property(property="refreshToken", type="string", description="Token para renovar o access token"),
 *         @OA\Property(property="tokenType", type="string", example="Bearer"),
 *         @OA\Property(property="expiresIn", type="integer", example=3600, description="Tempo de expiração em segundos"),
 *         @OA\Property(
 *             property="user",
 *             type="object",
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="active", type="boolean"),
 *             @OA\Property(property="emailVerifiedAt", type="string", format="date-time", nullable=true),
 *             @OA\Property(property="createdAt", type="string", format="date-time"),
 *             @OA\Property(property="updatedAt", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Property(property="message", type="string", example="Login realizado com sucesso")
 * )
 * 
 * @OA\Schema(
 *     schema="UserResponse",
 *     title="User Response",
 *     description="Dados do usuário",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="email", type="string", format="email"),
 *         @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *         @OA\Property(property="active", type="boolean"),
 *         @OA\Property(property="created_at", type="string", format="date-time"),
 *         @OA\Property(property="updated_at", type="string", format="date-time")
 *     ),
 *     @OA\Property(property="message", type="string", example="Success")
 * )
 * 
 * @OA\Schema(
 *     schema="LoginRequest",
 *     title="Login Request",
 *     required={"email", "password"},
 *     @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123")
 * )
 * 
 * @OA\Schema(
 *     schema="RegisterRequest",
 *     title="Register Request",
 *     required={"name", "email", "password", "password_confirmation"},
 *     @OA\Property(property="name", type="string", example="João Silva"),
 *     @OA\Property(property="email", type="string", format="email", example="joao@example.com"),
 *     @OA\Property(property="password", type="string", format="password", example="password123", minLength=6),
 *     @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
 * )
 * 
 * @OA\Schema(
 *     schema="RefreshTokenRequest",
 *     title="Refresh Token Request",
 *     required={"refresh_token"},
 *     @OA\Property(property="refresh_token", type="string", description="Token de refresh obtido no login")
 * )
 */
class AuthSchemas
{
    // Esta classe existe apenas para documentação Swagger
}