<?php

namespace App\Modules\Auth\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Traits\EmailValidationTrait;
use App\Common\Traits\UnifiedValidationMessages;

class LoginRequest extends BaseFormRequest
{
    use UnifiedValidationMessages;
    use EmailValidationTrait;

    public function rules(): array
    {
        return [
            'email' => $this->emailRules(),
            'password' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'ip_address' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);
    }
}