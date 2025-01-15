<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shelter>
 */
class ShelterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(nb: 4, asText: true),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => null,
            'postcode' => fake()->postcode(),
            'city' => fake()->city(),
            'country' => 'FRA',
            'phone_number' => fake()->e164PhoneNumber(),
        ];
    }
}
