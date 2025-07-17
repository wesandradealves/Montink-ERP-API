<?php

namespace App\Modules\Coupons\DTOs;

use App\Common\Base\BaseDTO;

class CreateCouponDTO extends BaseDTO
{
    public function __construct(
        public readonly string $code,
        public readonly ?string $description,
        public readonly string $type,
        public readonly float $value,
        public readonly ?float $minimum_value,
        public readonly ?int $usage_limit,
        public readonly ?string $valid_from,
        public readonly ?string $valid_until,
        public readonly bool $active = true
    ) {}
}