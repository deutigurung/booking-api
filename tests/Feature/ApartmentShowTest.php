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

class ApartmentShowTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_apartments_show_loads_with_facilities()
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

        $cate = FacilityCategory::create([
            'name' => 'new facility category'
        ]);

        $cate2 = FacilityCategory::create([
            'name' => 'second facility category'
        ]);

        $facility = Facility::create([
            'category_id' => $cate->id,
            'name' => 'Some facility 1'
        ]);
        $facility2 = Facility::create([
            'category_id' => $cate->id,
            'name' => 'Some facility 2'
        ]); 
        $facility3 = Facility::create([
            'category_id' => $cate->id,
            'name' => 'Some facility 3'
        ]);

        $facility4 = Facility::create([
            'category_id' => $cate2->id,
            'name' => 'Some facility 4'
        ]);
        $facility5 = Facility::create([
            'category_id' => $cate2->id,
            'name' => 'Some facility 5'
        ]);
        $apartment1->facilities()->sync([$facility->id,$facility2->id,$facility3->id,$facility4->id,$facility5->id]);

        $response = $this->getJson('/api/apartments/'.$apartment1->id);
        $response->assertStatus(200);
        $response->assertJsonPath('name', $apartment1->name);
        //checking apartments facility category count
        $response->assertJsonCount(3, 'facility_categories.'.$cate->name);
        
        $expectedFacilityArray = [
            $cate->name => [
                $facility->name, $facility2->name, $facility3->name
            ],
            $cate2->name => [
                $facility4->name, $facility5->name
            ]
        ];
        //checking facility category match as per expectedFacilityArray or not
        $response->assertJsonFragment($expectedFacilityArray,'facility_categories');

    }
}
