<?php

namespace Database\Seeders\Performance;

use App\Models\Apartment;
use App\Models\Property;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(int $count = 100): void
    {
       $properties = Property::pluck('id');
       $apartments = [];
       for($i = 0; $i < $count; $i++){
            $apartments[] = [
                'property_id' => $properties->random(),
                'name' => 'Apartment ' . $i,
                'capacity_adults' => rand(1, 5),
                'capacity_children' => rand(1, 5),
            ];
       }
       foreach(array_chunk($apartments,500) as $apartment){
            Apartment::insert($apartment);
       }
    }
}
