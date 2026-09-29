<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\MenuItem;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GroupOrder>
 */
class GroupOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'store_id' => Store::factory(),
            'user_id' => User::factory(),
            'is_public' => false,
            'status' => 'open',
            // 開團時的菜單快照，model 的 mutator 會負責 json_encode
            'menu_snapshot' => fn (array $attributes) => MenuItem::where('store_id', $attributes['store_id'])->get()->toArray(),
            'on_time' => null,
        ];
    }

    /**
     * 公開團購
     */
    public function public(): static
    {
        return $this->state(fn () => ['is_public' => true]);
    }

    /**
     * 已結單，並隨機設定是否準時送達
     */
    public function ordered(): static
    {
        return $this->state(fn () => [
            'status' => 'ordered',
            'on_time' => $this->faker->boolean(80),
        ]);
    }

    /**
     * 已關閉
     */
    public function closed(): static
    {
        return $this->state(fn () => ['status' => 'closed']);
    }
}
