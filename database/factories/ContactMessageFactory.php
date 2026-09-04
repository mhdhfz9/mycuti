<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => sprintf('01%d-%07d', fake()->randomElement([0, 1, 2, 3, 4, 6, 7, 8, 9]), fake()->numberBetween(0, 9999999)),
            'email' => fake()->safeEmail(),
            'message' => fake()->realText(200),
        ];
    }
}
