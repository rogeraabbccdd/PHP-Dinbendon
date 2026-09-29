<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MenuItem>
 */
class MenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' => Store::factory(),
            'name' => $this->faker->randomElement([
                '排骨', '雞腿', '控肉', '魚排', '燒肉', '三杯雞', '宮保雞丁', '糖醋里肌',
                '鯖魚', '蔥爆牛肉', '咖哩雞', '滷雞腿', '烤鴨', '豬排', '素食', '蝦捲',
            ]) . '便當',
            'price' => $this->faker->numberBetween(8, 15) * 10,
            'is_available' => $this->faker->boolean(90),
        ];
    }
}
