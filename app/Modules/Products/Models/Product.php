<?php

namespace App\Modules\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [];

    public function isActive(): bool
    {
        return $this->active;
    }
}