<?php

namespace App\Common\Enums;

enum MessagesEnum: string
{
    // Success Messages
    case SUCCESS = 'messages.success';
    case CREATED = 'messages.created';
    case UPDATED = 'messages.updated';
    case DELETED = 'messages.deleted';
    case OPERATION_SUCCESS = 'messages.operation_success';
    
    // Error Messages
    case ERROR = 'messages.error';
    case NOT_FOUND = 'messages.not_found';
    case UNAUTHORIZED = 'messages.unauthorized';
    case FORBIDDEN = 'messages.forbidden';
    case VALIDATION_ERROR = 'messages.validation_error';
    
    // Validation Messages
    case FIELD_REQUIRED = 'validation.required';
    case INVALID_EMAIL = 'validation.email';
    case FIELD_NUMERIC = 'validation.numeric';
    case FIELD_INTEGER = 'validation.integer';
    case FIELD_MIN_NUMERIC = 'validation.min.numeric';
    case FIELD_MIN_STRING = 'validation.min.string';
    case FIELD_MAX_NUMERIC = 'validation.max.numeric';
    case FIELD_MAX_STRING = 'validation.max.string';
    case FIELD_UNIQUE = 'validation.unique';
    case FIELD_EXISTS = 'validation.exists';
    case FIELD_DATE = 'validation.date';
    case FIELD_AFTER = 'validation.after';
    case FIELD_SIZE_STRING = 'validation.size.string';
    case FIELD_GT_ZERO = 'validation.gt.numeric';
    
    // Business Messages
    case INSUFFICIENT_STOCK = 'messages.insufficient_stock';
    case INVALID_COUPON = 'messages.invalid_coupon';
    case COUPON_EXPIRED = 'messages.coupon_expired';
    case MIN_VALUE_NOT_REACHED = 'messages.min_value_not_reached';
    case ORDER_CONFIRMED = 'messages.order_confirmed';
    case ORDER_CANCELLED = 'messages.order_cancelled';
    case EMAIL_SENT = 'messages.email_sent';
    
    public function get(): string
    {
        return __($this->value);
    }
}