<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\City;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PropertyShowTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_property_show_loads_correctly()
    {
        $owner = User::factory()->create(['role_id'=> Role::OWNER_ROLE]);
        $city = City::value('id');
        $property1 = Property::factory()->create([
            'owner_id'=> $owner->id,
            'city_id' => $city
        ]);

        //large apartment property
        $apartment1 = Apartment::factory()->create([
            'name' => 'Large apartment',
            'property_id'=> $property1->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);

        //small apartment property
        $apartment2 = Apartment::factory()->create([
            'name' => 'Small apartment',
            'property_id'=> $property1->id,
            'capacity_adults' => 1,
            'capacity_children' => 0,
        ]);

        //mid apartment property
        $apartment3 = Apartment::factory()->create([
            'name' => 'mid apartment',
            'property_id'=> $property1->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);

        $cate = FacilityCategory::create([
            'name' => 'new facility category'
        ]);
        $facility = Facility::create([
            'category_id' => $cate->id,
            'name' => 'Some facility'
        ]);
        $apartment3->facilities()->sync($facility->id);

        //checking property apartments = 3 or not
        // $response = $this->getJson('/api/properties/'.$property1->id);
        // $response->assertStatus(200);
        // $response->assertJsonCount(3, 'apartments');
        // $response->assertJsonPath('name', $property1->name);
       
        //checking property with adults capacity = 2 and children = 1 ,and it return total 2 data
        // $response = $this->getJson('/api/properties/'.$property1->id.'?adults=2&children=1');
        // $response->assertStatus(200);
        // $response->assertJsonCount(2, 'apartments');
        // $response->assertJsonPath('name', $property1->name);
        // $response->assertJsonPath('apartments.0.facilities.0.name', $facility->name);
        // $response->assertJsonCount(0,'apartments.1.facilities');

        //checking properties with facility properties
        $response = $this->getJson('/api/search?city=' . $city . '&adults=2&children=1');
        $response->assertStatus(200);
        $response->assertJsonPath('0.apartments.0.facilities', NULL);

    }
}
