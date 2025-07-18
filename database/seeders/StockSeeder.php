<?php

namespace Database\Seeders;

use App\Modules\Products\Models\Product;
use App\Modules\Stock\Models\Stock;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        
        foreach ($products as $product) {
            if ($product->stock > 0) {
                Stock::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'variations' => null
                    ],
                    [
                        'quantity' => $product->stock,
                        'reserved' => 0
                    ]
                );
            }
        }
    }
}