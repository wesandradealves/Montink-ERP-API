<?php

namespace App\Modules\Coupons\UseCases;

use App\Common\Enums\ResponseMessage;
use App\Common\Exceptions\ResourceNotFoundException;
use App\Common\Traits\MoneyFormatter;
use App\Modules\Coupons\DTOs\CouponDTO;
use App\Modules\Coupons\DTOs\CreateCouponDTO;
use App\Modules\Coupons\DTOs\UpdateCouponDTO;
use App\Modules\Coupons\DTOs\ValidateCouponDTO;
use App\Modules\Coupons\Models\Coupon;
use Illuminate\Support\Facades\DB;

class CouponsUseCase
{
    use MoneyFormatter;

    public function createCoupon(CreateCouponDTO $dto): CouponDTO
    {
        $existingCoupon = Coupon::where('code', $dto->code)->first();
        if ($existingCoupon) {
            throw new \InvalidArgumentException(ResponseMessage::COUPON_ALREADY_EXISTS->get());
        }

        $couponData = $dto->toArray();
        $couponData['used_count'] = 0;
        
        $coupon = Coupon::create($couponData);

        return $this->toCouponDTO($coupon);
    }

    public function updateCoupon(int $id, UpdateCouponDTO $dto): CouponDTO
    {
        $coupon = Coupon::find($id);
        
        if (!$coupon) {
            throw new ResourceNotFoundException(ResponseMessage::COUPON_NOT_FOUND->get());
        }

        $updateData = array_filter($dto->toArray(), fn($value) => $value !== null);
        
        if (!empty($updateData)) {
            $coupon->update($updateData);
        }

        return $this->toCouponDTO($coupon);
    }

    public function deleteCoupon(int $id): void
    {
        $coupon = Coupon::find($id);
        
        if (!$coupon) {
            throw new ResourceNotFoundException(ResponseMessage::COUPON_NOT_FOUND->get());
        }

        $coupon->delete();
    }

    public function getCouponById(int $id): CouponDTO
    {
        $coupon = Coupon::find($id);
        
        if (!$coupon) {
            throw new ResourceNotFoundException(ResponseMessage::COUPON_NOT_FOUND->get());
        }

        return $this->toCouponDTO($coupon);
    }

    public function getCouponByCode(string $code): CouponDTO
    {
        $coupon = Coupon::where('code', $code)->first();
        
        if (!$coupon) {
            throw new ResourceNotFoundException(ResponseMessage::COUPON_NOT_FOUND->get());
        }

        return $this->toCouponDTO($coupon);
    }

    public function listCoupons(array $filters = []): array
    {
        $query = Coupon::query();

        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        if (isset($filters['valid'])) {
            $query->valid();
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $coupons = $query->orderBy('created_at', 'desc')->get();

        return [
            'items' => $coupons->map(fn($coupon) => $this->toCouponDTO($coupon))->toArray(),
            'total' => $coupons->count()
        ];
    }

    public function validateCoupon(ValidateCouponDTO $dto): array
    {
        $coupon = Coupon::where('code', $dto->code)->first();
        
        if (!$coupon) {
            throw new ResourceNotFoundException(ResponseMessage::COUPON_NOT_FOUND->get());
        }

        $error = $coupon->getValidationError($dto->value);
        
        if ($error) {
            throw new \InvalidArgumentException($error);
        }

        $discount = $coupon->calculateDiscount($dto->value);

        return [
            'valid' => true,
            'coupon' => $this->toCouponDTO($coupon),
            'discount' => $discount,
            'formatted_discount' => $this->formatMoney($discount),
            'final_value' => $dto->value - $discount,
            'formatted_final_value' => $this->formatMoney($dto->value - $discount)
        ];
    }

    public function applyCoupon(string $code, float $value): array
    {
        return DB::transaction(function () use ($code, $value) {
            $coupon = Coupon::where('code', $code)->lockForUpdate()->first();
            
            if (!$coupon) {
                throw new ResourceNotFoundException(ResponseMessage::COUPON_NOT_FOUND->get());
            }

            $error = $coupon->getValidationError($value);
            if ($error) {
                throw new \InvalidArgumentException($error);
            }

            $discount = $coupon->calculateDiscount($value);
            $coupon->incrementUsage();

            return [
                'coupon_id' => $coupon->id,
                'discount' => $discount,
                'formatted_discount' => $this->formatMoney($discount),
                'coupon_code' => $coupon->code,
                'coupon_type' => $coupon->type,
                'coupon_value' => $coupon->value
            ];
        });
    }

    private function toCouponDTO(Coupon $coupon): CouponDTO
    {
        return new CouponDTO(
            id: $coupon->id,
            code: $coupon->code,
            description: $coupon->description,
            type: $coupon->type,
            value: $coupon->value,
            minimum_value: $coupon->minimum_value,
            usage_limit: $coupon->usage_limit,
            used_count: $coupon->used_count ?? 0,
            valid_from: $coupon->valid_from?->format('Y-m-d'),
            valid_until: $coupon->valid_until?->format('Y-m-d'),
            active: $coupon->active,
            formatted_value: $coupon->getFormattedValue(),
            is_valid: $coupon->isValid(),
            created_at: $coupon->created_at?->format('Y-m-d H:i:s'),
            updated_at: $coupon->updated_at?->format('Y-m-d H:i:s')
        );
    }
}