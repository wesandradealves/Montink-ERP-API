<?php

namespace App\Modules\Email\DTOs;

use App\Common\Base\BaseDTO;

class OrderConfirmationEmailDTO extends BaseDTO
{
    public function __construct(
        public readonly string $orderNumber,
        public readonly string $customerName,
        public readonly string $customerEmail,
        public readonly string $customerPhone,
        public readonly string $customerCpf,
        public readonly string $customerCep,
        public readonly string $customerAddress,
        public readonly ?string $customerComplement,
        public readonly string $customerNeighborhood,
        public readonly string $customerCity,
        public readonly string $customerState,
        public readonly array $items,
        public readonly float $subtotal,
        public readonly float $discount,
        public readonly ?string $couponCode,
        public readonly float $shippingCost,
        public readonly float $total,
        public readonly \DateTimeInterface $createdAt
    ) {}
}