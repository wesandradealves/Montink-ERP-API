<?php

namespace App\Modules\Auth\DTOs;

use App\Common\Base\BaseDTO;
use App\Modules\Auth\Models\User;

class UserDTO extends BaseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly bool $active,
        public readonly ?string $emailVerifiedAt,
        public readonly string $createdAt,
        public readonly string $updatedAt
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            active: $user->active,
            emailVerifiedAt: $user->email_verified_at?->toISOString(),
            createdAt: $user->created_at->toISOString(),
            updatedAt: $user->updated_at->toISOString()
        );
    }
}