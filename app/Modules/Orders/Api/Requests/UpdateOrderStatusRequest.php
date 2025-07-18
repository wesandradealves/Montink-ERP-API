<?php

namespace App\Modules\Orders\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Traits\ValidationMessagesTrait;

class UpdateOrderStatusRequest extends BaseFormRequest
{
    use ValidationMessagesTrait;

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:pending,processing,shipped,delivered,cancelled'],
        ];
    }

    protected array $customMessages = [
        'status.required' => 'O status é obrigatório',
        'status.in' => 'Status inválido. Valores aceitos: pending, processing, shipped, delivered, cancelled',
    ];
}