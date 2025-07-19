<?php

namespace App\Modules\Auth\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Traits\UnifiedValidationMessages;

class RefreshTokenRequest extends BaseFormRequest
{
    use UnifiedValidationMessages;

    public function rules(): array
    {
        return [
            'refresh_token' => ['required', 'string'],
        ];
    }
}