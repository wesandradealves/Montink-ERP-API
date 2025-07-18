<?php

namespace App\Modules\Address\DTOs;

use App\Common\Base\BaseDTO;

class AddressDTO extends BaseDTO
{
    public function __construct(
        public readonly string $cep,
        public readonly string $logradouro,
        public readonly ?string $complemento,
        public readonly string $bairro,
        public readonly string $localidade,
        public readonly string $uf,
        public readonly ?string $ibge = null,
        public readonly ?string $gia = null,
        public readonly ?string $ddd = null,
        public readonly ?string $siafi = null,
    ) {}
}