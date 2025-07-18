<?php

namespace App\Modules\Stock\Models;

use App\Common\Base\BaseModel;
use App\Modules\Products\Models\Product;

class Stock extends BaseModel
{
    protected $table = 'stock';
    
    protected $fillable = [
        'product_id',
        'quantity',
        'reserved',
        'variations',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved' => 'integer',
        'variations' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getAvailableQuantityAttribute(): int
    {
        return $this->quantity - $this->reserved;
    }
}