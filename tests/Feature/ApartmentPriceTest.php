<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\ApartmentPrice;
use App\Models\City;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApartmentPriceTest extends TestCase
{
  
    public function create_apartment()
    {
        $owner = User::factory()->create(['role_id' => Role::OWNER_ROLE]);
        $cityId = City::value('id');
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityId,
        ]);
 
        return Apartment::create([
            'name' => 'Apartment',
            'property_id' => $property->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);
    }

    public function test_calculate_apartment_price_1_day()
    {
        $apartment = $this->create_apartment();
        ApartmentPrice::create([
            'apartment_id' => $apartment->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'price' => 100
        ]);
        $total_price = $apartment->calculatePriceForDates(
            now()->toDateString(),
            now()->toDateString()
        );
        $this->assertEquals(100,$total_price);
    }

    public function test_calculate_apartment_price_2_day()
    {
        $apartment = $this->create_apartment();
        ApartmentPrice::create([
            'apartment_id' => $apartment->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'price' => 100
        ]);
        $total_price = $apartment->calculatePriceForDates(
            now()->toDateString(),
            now()->addDay()->toDateString()
        );
        $this->assertEquals(200,$total_price);
    }

    public function test_calculate_apartment_price_multiple_date_ranges_day()
    {
        $apartment = $this->create_apartment();
        $price1 = ApartmentPrice::create([
            'apartment_id' => $apartment->id,
            'start_date' => now()->toDateString(), //today .ie 2023-06-26
            'end_date' => now()->addDays(2)->toDateString(), // today + 2 days .ie 2023-06-28
            'price' => 100
        ]);
        $price2 =  ApartmentPrice::create([
            'apartment_id' => $apartment->id,
            'start_date' => now()->addDays(3)->toDateString(), // today + 3 days .ie 2023-06-29
            'end_date' => now()->addDays(7)->toDateString(), // today + 2 days .ie 2023-07-03
            'price' => 150
        ]);
        // $total_price = $apartment->calculatePriceForDates(
        //     '2023-06-28','2023-07-01' 
        // );
        $total_price = $apartment->calculatePriceForDates(
            now()->addDays(2)->toDateString(),now()->addDays(5)->toDateString()
        );
        $this->assertEquals(1*100+3*150,$total_price);
    }
}
