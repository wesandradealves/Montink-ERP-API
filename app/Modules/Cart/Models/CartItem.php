<?php

namespace App\Modules\Cart\Models;

use App\Common\Base\BaseModel;
use App\Modules\Products\Models\Product;

class CartItem extends BaseModel
{
    protected $table = 'cart_items';
    
    protected $fillable = [
        'session_id',
        'product_id',
        'quantity',
        'price',
        'variations',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'variations' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotal(): float
    {
        return $this->price * $this->quantity;
    }
}