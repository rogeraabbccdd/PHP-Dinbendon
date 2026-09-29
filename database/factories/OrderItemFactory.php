<?php

namespace Database\Factories;

use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
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
        return [
            'order_id' => Order::factory(),
            'menu_item_id' => MenuItem::factory(),
            // 品項名稱與單價為下單時的快照
            'name' => fn (array $attributes) => MenuItem::find($attributes['menu_item_id'])->name,
            'price' => fn (array $attributes) => MenuItem::find($attributes['menu_item_id'])->price,
            'quantity' => $this->faker->numberBetween(1, 3),
            'comment' => $this->faker->optional(0.3)->randomElement(['不要辣', '飯少', '飯多', '不要香菜', '加蛋', '菜多一點']),
        ];
    }
}
