<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $item = Item::factory();

        return [
            'order_id' => Order::factory(),
            'item_id' => $item,
            'name' => fake()->words(2, true),
            'quantity' => fake()->numberBetween(1, 5),
        ];
    }
}
