<?php

namespace App\Modules\Webhooks\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Enums\ResponseMessage;
use App\Common\Traits\UnifiedValidationMessages;
use App\Domain\Commons\Enums\OrderStatus;

class OrderStatusWebhookRequest extends BaseFormRequest
{
    use UnifiedValidationMessages;

    public function rules(): array
    {
        $validStatuses = implode(',', array_map(
            fn($case) => $case->value,
            OrderStatus::cases()
        ));

        return [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'status' => ['required', 'string', "in:$validStatuses"],
            'timestamp' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}