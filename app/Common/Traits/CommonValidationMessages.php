<?php

namespace App\Common\Traits;

use App\Common\Enums\MessagesEnum;

trait CommonValidationMessages
{
    protected function getCommonMessages(): array
    {
        $commonMessages = [
            'required' => MessagesEnum::FIELD_REQUIRED,
            'email' => MessagesEnum::INVALID_EMAIL,
            'numeric' => MessagesEnum::FIELD_NUMERIC,
            'integer' => MessagesEnum::FIELD_INTEGER,
            'min' => [
                'numeric' => MessagesEnum::FIELD_MIN_NUMERIC,
                'string' => MessagesEnum::FIELD_MIN_STRING,
            ],
            'max' => [
                'numeric' => MessagesEnum::FIELD_MAX_NUMERIC,
                'string' => MessagesEnum::FIELD_MAX_STRING,
            ],
            'unique' => MessagesEnum::FIELD_UNIQUE,
            'exists' => MessagesEnum::FIELD_EXISTS,
            'date' => MessagesEnum::FIELD_DATE,
            'after' => MessagesEnum::FIELD_AFTER,
            'size' => [
                'string' => MessagesEnum::FIELD_SIZE_STRING,
            ],
            'gt' => [
                'numeric' => MessagesEnum::FIELD_GT_ZERO,
            ],
        ];
        
        return array_merge($commonMessages, $this->customMessages ?? []);
    }
}