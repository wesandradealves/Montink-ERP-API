<?php

namespace App\Modules\Orders\Models;

use App\Common\Base\BaseModel;
use App\Common\Enums\ResponseMessage;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends BaseModel
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email', 
        'customer_phone',
        'customer_cpf',
        'customer_cep',
        'customer_address',
        'customer_complement',
        'customer_neighborhood',
        'customer_city',
        'customer_state',
        'subtotal',
        'discount',
        'shipping_cost',
        'total',
        'status',
        'coupon_code',
        'coupon_id',
        'session_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByCustomerEmail($query, string $email)
    {
        return $query->where('customer_email', $email);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isShipped(): bool
    {
        return $this->status === 'shipped';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function cancel(): void
    {
        if (!$this->canBeCancelled()) {
            throw new \Exception(ResponseMessage::ORDER_CANNOT_CANCEL->get(['status' => $this->status]));
        }

        $this->status = 'cancelled';
        $this->save();
    }

    public function updateStatus(string $status): void
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            throw new \Exception(ResponseMessage::ORDER_INVALID_STATUS->get(['status' => $status]));
        }

        $this->status = $status;
        $this->save();
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = date('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$date}-{$random}";
    }
}