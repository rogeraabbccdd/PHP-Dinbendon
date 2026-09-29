<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . '便當店',
            'address' => $this->faker->address(),
            'google_map' => 'https://maps.google.com/maps',
            'facebook' => 'https://fb.com/',
            'instagram' => 'https://instagram.com/',
            'business_hours' => $this->faker->time() . ' - ' . $this->faker->time(),
            'delivery_conditions' => $this->faker->sentence(),
            'image' => 'https://picsum.photos/id/' . $this->faker->numberBetween(1, 1000) . '/800/600',
            'phone' => $this->faker->phoneNumber(),
            'is_closed' => $this->faker->boolean(10),
        ];
    }
}
