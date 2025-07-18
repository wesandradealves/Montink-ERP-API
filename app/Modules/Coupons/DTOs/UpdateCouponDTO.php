<?php

namespace App\Modules\Coupons\DTOs;

use App\Common\Base\BaseDTO;

class UpdateCouponDTO extends BaseDTO
{
    public function __construct(
        public readonly ?string $description = null,
        public readonly ?string $type = null,
        public readonly ?float $value = null,
        public readonly ?float $minimum_value = null,
        public readonly ?int $usage_limit = null,
        public readonly ?string $valid_from = null,
        public readonly ?string $valid_until = null,
        public readonly ?bool $active = null
    ) {}
}