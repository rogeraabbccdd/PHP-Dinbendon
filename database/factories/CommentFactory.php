<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'store_id' => Store::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'content' => $this->faker->optional(0.7)->randomElement([
                '好吃，份量很夠', '有點鹹', '送來的時候已經涼了', 'CP 值很高', '配菜很普通', '主菜很香，推薦',
            ]),
        ];
    }
}
