<?php

namespace App\Modules\Products\Models;

use App\Common\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'sku',
        'active',
        'variations',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
        'variations' => 'array',
    ];

    protected $hidden = [];

    public function isActive(): bool
    {
        return $this->active;
    }
}