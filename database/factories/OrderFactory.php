<?php

namespace Database\Factories;

use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 50, 1000);
        $discount = $this->faker->randomFloat(2, 0, $subtotal * 0.3);
        $total = $subtotal - $discount;

        return [
            'order_number' => 'ORD-' . strtoupper($this->faker->unique()->bothify('????####')),
            'status' => $this->faker->randomElement([
                OrderStatus::PENDING->value,
                OrderStatus::PAID->value,
                OrderStatus::COMPLETED->value
            ]),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->email(),
            'customer_cpf' => $this->faker->numerify('###.###.###-##'),
            'customer_phone' => $this->faker->numerify('(##) #####-####'),
            'shipping_cep' => $this->faker->numerify('#####-###'),
            'shipping_address' => $this->faker->streetAddress(),
            'shipping_neighborhood' => $this->faker->word(),
            'shipping_city' => $this->faker->city(),
            'shipping_state' => $this->faker->stateAbbr(),
            'items' => []
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::PENDING->value,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::PAID->value,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::COMPLETED->value,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::CANCELLED->value,
        ]);
    }
}