<?php

namespace App\Modules\Orders\Models;

use App\Common\Base\BaseModel;
use App\Modules\Products\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends BaseModel
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'quantity',
        'price',
        'subtotal',
        'variations',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'variations' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function calculateSubtotal(): float
    {
        return $this->price * $this->quantity;
    }

    public function getFormattedVariations(): string
    {
        if (empty($this->variations)) {
            return '';
        }

        $formatted = [];
        foreach ($this->variations as $key => $value) {
            $formatted[] = ucfirst($key) . ': ' . $value;
        }

        return implode(', ', $formatted);
    }
}