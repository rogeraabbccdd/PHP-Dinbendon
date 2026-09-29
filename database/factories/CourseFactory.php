<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['數位系統設計', '作業系統', '計算機組織', '軟體工程', '編譯器設計']),
            'year' => now()->year,
            'term' => $this->faker->numberBetween(1, 2),
            'enabled' => true,
        ];
    }

    /**
     * 停用的班級
     */
    public function disabled(): static
    {
        return $this->state(fn () => ['enabled' => false]);
    }
}
