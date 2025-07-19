<?php

namespace App\Modules\Coupons\Models;

use App\Common\Base\BaseModel;
use App\Common\Traits\MoneyFormatter;
use App\Common\Enums\ResponseMessage;
use Illuminate\Database\Eloquent\Builder;

class Coupon extends BaseModel
{
    use MoneyFormatter;
    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'minimum_value',
        'usage_limit',
        'used_count',
        'valid_from',
        'valid_until',
        'active'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_value' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'active' => 'boolean'
    ];

    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENTAGE = 'percentage';

    public static function getTypes(): array
    {
        return [
            self::TYPE_FIXED => ResponseMessage::COUPON_TYPE_FIXED->get(),
            self::TYPE_PERCENTAGE => ResponseMessage::COUPON_TYPE_PERCENTAGE->get()
        ];
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->where('active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')
                    ->orWhereDate('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhereDate('valid_until', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }

    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }

    // Métodos simplificados para acesso a dados apenas
    
    public function isActive(): bool
    {
        return $this->active;
    }

    public function isWithinValidityPeriod(): bool
    {
        $now = now();
        
        if ($this->valid_from && $this->valid_from->isAfter($now)) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isBefore($now)) {
            return false;
        }

        return true;
    }

    public function hasReachedUsageLimit(): bool
    {
        return $this->usage_limit && $this->used_count >= $this->usage_limit;
    }

    public function hasMinimumValue(): bool
    {
        return $this->minimum_value > 0;
    }

    public function isFixedType(): bool
    {
        return $this->type === self::TYPE_FIXED;
    }

    public function isPercentageType(): bool
    {
        return $this->type === self::TYPE_PERCENTAGE;
    }
}