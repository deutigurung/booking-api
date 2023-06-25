<?php

namespace Database\Factories;

use App\Models\Apartment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApartmentPrice>
 */
class ApartmentPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'apartment_id' => Apartment::value('id'),
            'start_date' => now()->subDays(7),
            'end_date' => now()->addMonth(),
            'price' => rand(100, 500),
        ];
    }
}
