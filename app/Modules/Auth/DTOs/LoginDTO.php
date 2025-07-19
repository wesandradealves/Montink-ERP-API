<?php

namespace App\Modules\Auth\DTOs;

use App\Common\Base\BaseDTO;

class LoginDTO extends BaseDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $ipAddress = null,
        public readonly ?string $userAgent = null
    ) {}
}