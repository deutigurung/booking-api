<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::create([
            'country_id' => 1,
            'name' => 'Kathmandu',
            'lat' => 27.70169000,
            'long' => 85.32060000,
        ]);

        City::create([
            'country_id' => 2,
            'name' => 'New York',
            'lat' => 40.712776,
            'long' => -74.005974,
        ]);
 
        City::create([
            'country_id' => 3,
            'name' => 'London',
            'lat' => 51.507351,
            'long' => -0.127758,
        ]);
    }
}
