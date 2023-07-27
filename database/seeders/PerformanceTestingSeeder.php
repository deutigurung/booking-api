<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Laravel\Telescope\Telescope;

class PerformanceTestingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Telescope::stopRecording();
        
        $this->call([
            //RoleSeeder::class,
            //AdminUserSeeder::class,
        ]);

        //callWith is used to pass parameters in seeder
        // $this->callWith(Performance\UserSeeder::class,[
        //     'owners' => 500,
        //     'users' => 1000
        // ]);

        // $this->callWith(Performance\CountrySeeder::class,[
        //     'count' => 100
        // ]);

        // $this->callWith(Performance\CitySeeder::class,[
        //     'count' => 1000
        // ]);

        // $this->callWith(Performance\GeoObjectSeeder::class,[
        //     'count' => 10000
        // ]);

        // $this->callWith(Performance\PropertySeeder::class,[
        //     'count' => 1000
        // ]);

        // $this->callWith(Performance\ApartmentSeeder::class,[
        //     'count' => 5000
        // ]);

        // $this->callWith(Performance\BookingSeeder::class,[
        //     'withRatings' => 20000,
        //     'withoutRatings' => 10000
        // ]);
        
    }
}
