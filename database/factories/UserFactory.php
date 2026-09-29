<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => $this->faker->unique()->numerify('11####'),
            // 預設密碼與學號相同
            'password' => fn (array $attributes) => Hash::make($attributes['student_id']),
            'name' => $this->faker->name(),
            'course_id' => Course::factory(),
            'seat_number' => $this->faker->numberBetween(1, 50),
            'enabled' => true,
        ];
    }

    /**
     * 停用的帳號
     */
    public function disabled(): static
    {
        return $this->state(fn () => ['enabled' => false]);
    }
}
