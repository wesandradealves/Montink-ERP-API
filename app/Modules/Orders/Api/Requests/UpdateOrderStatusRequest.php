<?php

namespace App\Modules\Orders\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Traits\UnifiedValidationMessages;

class UpdateOrderStatusRequest extends BaseFormRequest
{
    use UnifiedValidationMessages;

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:pending,processing,shipped,delivered,cancelled'],
        ];
    }

    protected array $customMessages = [];
}