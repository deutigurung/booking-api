<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\City;
use App\Models\Country;
use App\Models\GeoObject;
use App\Models\Property;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PropertySearchTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_property_search_by_city(): void
    {
        $owner = User::factory()->create(['role_id'=> Role::OWNER_ROLE]);
        $cities = City::take(2)->pluck('id');
        $property1 = Property::factory()->create(['owner_id'=> $owner->id,'city_id'=> $cities[0]]);
        $property2 = Property::factory()->create(['owner_id'=> $owner->id,'city_id'=> $cities[1]]);
        $response = $this->getJson('/api/search?city='.$cities[0]);
        $response->assertStatus(200);
        $response->assertJsonCount(1);
         // assertJsonFragment() takes the fragment as an argument and
        //  returns true if the fragment is found in the response, and false if it is not found.
        $response->assertJsonFragment(['id'=>$property1->id]);

    }

    public function test_property_search_by_country(): void
    {
        $owner = User::factory()->create(['role_id'=> Role::OWNER_ROLE]);
        $countries = Country::with('cities')->take(2)->get();

        $property1 = Property::factory()->create([
            'owner_id'=> $owner->id,
            'city_id'=> $countries[0]->cities()->value('id')
        ]);
        $property2 = Property::factory()->create([
            'owner_id'=> $owner->id,
            'city_id'=> $countries[1]->cities()->value('id')
        ]);
        $response = $this->getJson('/api/search?country='.$countries[0]->id);
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id'=>$property1->id]);

    }

    public function test_property_search_by_geoobject(): void
    {
        $owner = User::factory()->create(['role_id'=> Role::OWNER_ROLE]);
        $city = City::value('id');
        $geo = GeoObject::first();

        $property1 = Property::factory()->create([
            'owner_id'=> $owner->id,
            'city_id'=>$city,
            'lat' => $geo->lat,
            'long' => $geo->long,
        ]);
        $property2 = Property::factory()->create([
            'owner_id'=> $owner->id,
            'city_id'=>$city,
            'lat' => $geo->lat + 10, //add 10km far latitude 
            'long' => $geo->long + 10,
        ]);
        $response = $this->getJson('/api/search?geoobject='.$geo->id);
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id'=>$property1->id]);

    }

    public function test_property_search_by_apartment_capacity(): void
    {
        $owner = User::factory()->create(['role_id'=> Role::OWNER_ROLE]);
        $city = City::value('id');
        $property1 = Property::factory()->create([
            'owner_id'=> $owner->id,
            'city_id' => $city
        ]);
        $property2 = Property::factory()->create([
            'owner_id'=> $owner->id,
            'city_id' => $city
        ]);

        //large apartment property
        $apartment1 = Apartment::factory()->create([
            'property_id'=> $property1->id,
            'capacity_adults' => 4,
            'capacity_children' => 2,
        ]);
        //small apartment property
        $apartment2 = Apartment::factory()->create([
            'property_id'=> $property2->id,
            'capacity_adults' => 2,
            'capacity_children' => 0,
        ]);
        $response = $this->getJson('/api/search?city='.$city.'&adults=2&children=1');
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['id'=>$property1->id]);

    }
}
