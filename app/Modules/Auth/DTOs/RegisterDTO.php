<?php

namespace App\Modules\Auth\DTOs;

use App\Common\Base\BaseDTO;

class RegisterDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password
    ) {}
}