<?php

namespace App\Modules\Auth\DTOs;

use App\Common\Base\BaseDTO;

class AuthResponseDTO extends BaseDTO
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $refreshToken,
        public readonly string $tokenType = 'Bearer',
        public readonly int $expiresIn = 900, // 15 minutes
        public readonly ?UserDTO $user = null
    ) {}
}