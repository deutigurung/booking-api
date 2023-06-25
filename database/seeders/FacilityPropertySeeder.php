<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilityPropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('facility_property')->insert([
            ['facility_id' => 1, 'property_id' => 1],
            ['facility_id' => 1, 'property_id' => 2],
            ['facility_id' => 1, 'property_id' => 3],
            ['facility_id' => 1, 'property_id' => 4],
            ['facility_id' => 1, 'property_id' => 5],
            ['facility_id' => 2, 'property_id' => 1],
            ['facility_id' => 2, 'property_id' => 1],
            ['facility_id' => 2, 'property_id' => 1],
            ['facility_id' => 3, 'property_id' => 1], 
            ['facility_id' => 4, 'property_id' => 1],
            ['facility_id' => 5, 'property_id' => 1],

        ]);
    }
}
