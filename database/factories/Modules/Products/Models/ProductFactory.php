<?php

namespace Database\Factories\Modules\Products\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Modules\Products\Models\Product;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'sku' => strtoupper($this->faker->unique()->bothify('???-###')),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'description' => $this->faker->sentence(),
            'active' => $this->faker->boolean(80),
            'variations' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }

    public function withVariations(array $variations): static
    {
        return $this->state(fn (array $attributes) => [
            'variations' => $variations,
        ]);
    }
}