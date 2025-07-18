<?php

namespace App\Modules\Cart\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Rules\QuantityRule;
use App\Common\Traits\ValidationMessagesTrait;

class UpdateCartItemRequest extends BaseFormRequest
{
    use ValidationMessagesTrait;
    public function rules(): array
    {
        return [
            'quantity' => ['required', new QuantityRule],
        ];
    }

    protected array $customMessages = [];
}