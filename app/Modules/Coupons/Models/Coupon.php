<?php

namespace App\Modules\Coupons\Models;

use App\Common\Base\BaseModel;
use App\Common\Traits\MoneyFormatter;
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
            self::TYPE_FIXED => 'Valor Fixo',
            self::TYPE_PERCENTAGE => 'Porcentagem'
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

    public function isValid(): bool
    {
        if (!$this->active) {
            return false;
        }

        if ($this->valid_from && $this->valid_from->isFuture()) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function canBeUsedWithValue(float $value): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->minimum_value && $value < $this->minimum_value) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $value): float
    {
        if (!$this->canBeUsedWithValue($value)) {
            return 0;
        }

        if ($this->type === self::TYPE_FIXED) {
            return min($this->value, $value);
        }

        return $value * ($this->value / 100);
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    public function getFormattedValue(): string
    {
        if ($this->type === self::TYPE_FIXED) {
            return $this->formatMoney($this->value);
        }

        return $this->value . '%';
    }

    public function getValidationError(float $value = 0): ?string
    {
        if (!$this->active) {
            return 'Cupom inativo';
        }

        if ($this->valid_from && $this->valid_from->isFuture()) {
            return 'Cupom ainda não está válido';
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return 'Cupom expirado';
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return 'Cupom já atingiu o limite de uso';
        }

        if ($this->minimum_value && $value < $this->minimum_value) {
            return 'Valor mínimo de ' . $this->formatMoney($this->minimum_value) . ' não atingido';
        }

        return null;
    }
}