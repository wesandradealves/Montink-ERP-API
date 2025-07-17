<?php

namespace App\Modules\Orders\DTOs;

use App\Common\Base\BaseDTO;

class CreateOrderDTO extends BaseDTO
{
    public function __construct(
        public readonly string $customerName,
        public readonly string $customerEmail,
        public readonly ?string $customerPhone,
        public readonly ?string $customerCpf,
        public readonly string $customerCep,
        public readonly string $customerAddress,
        public readonly ?string $customerComplement,
        public readonly string $customerNeighborhood,
        public readonly string $customerCity,
        public readonly string $customerState,
        public readonly ?string $couponCode = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            customerName: $data['customer_name'],
            customerEmail: $data['customer_email'],
            customerPhone: $data['customer_phone'] ?? null,
            customerCpf: $data['customer_cpf'] ?? null,
            customerCep: $data['customer_cep'],
            customerAddress: $data['customer_address'],
            customerComplement: $data['customer_complement'] ?? null,
            customerNeighborhood: $data['customer_neighborhood'],
            customerCity: $data['customer_city'],
            customerState: $data['customer_state'],
            couponCode: $data['coupon_code'] ?? null,
        );
    }
}