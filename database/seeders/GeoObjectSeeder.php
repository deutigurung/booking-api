<?php

namespace Database\Seeders;

use App\Models\GeoObject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeoObjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GeoObject::create([
            'city_id' => 1,
            'name' => 'TIA',
            'lat' => 28.3974,
            'long' =>  84.1258
        ]);

        GeoObject::create([
            'city_id' => 2,
            'name' => 'Statue of Liberty',
            'lat' => 40.689247,
            'long' => -74.044502
        ]);
 
        GeoObject::create([
            'city_id' => 3,
            'name' => 'Big Ben',
            'lat' => 51.500729,
            'long' => -0.124625
        ]);
    }
}
