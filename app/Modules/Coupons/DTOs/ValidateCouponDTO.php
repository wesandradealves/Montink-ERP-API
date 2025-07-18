<?php

namespace App\Modules\Coupons\DTOs;

use App\Common\Base\BaseDTO;

class ValidateCouponDTO extends BaseDTO
{
    public function __construct(
        public readonly string $code,
        public readonly float $value
    ) {}
}