<?php

namespace Database\Seeders\Performance;

use App\Models\City;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(int $count = 100): void
    {
        $users = User::where('role_id', Role::OWNER_ROLE)->inRandomOrder()->pluck('id');
        $cities = City::pluck('id');
 
        for ($i = 1; $i <= $count; $i++) {
            Property::factory()->create([
                'owner_id' => $users->random(),
                'city_id' => $cities->random(),
            ]);
        }
    }
}
