<?php

namespace App\Modules\Auth\Api\Requests;

use App\Common\Base\BaseFormRequest;
use App\Common\Traits\EmailValidationTrait;
use App\Common\Traits\UnifiedValidationMessages;

class RegisterRequest extends BaseFormRequest
{
    use UnifiedValidationMessages;
    use EmailValidationTrait;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => $this->uniqueEmailRules(),
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}