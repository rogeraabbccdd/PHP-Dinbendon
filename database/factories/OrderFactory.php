<?php

namespace Database\Factories;

use App\Models\GroupOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_order_id' => GroupOrder::factory(),
            'user_id' => User::factory(),
            'total_price' => 0, // Will be calculated later
        ];
    }
}
