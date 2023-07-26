<?php

namespace Database\Seeders\Performance;

use App\Models\GeoObject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GeoObjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(int $count = 100): void
    {
        GeoObject::factory($count)->create();
    }
}
