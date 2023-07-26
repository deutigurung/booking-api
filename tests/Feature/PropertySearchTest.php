<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Bed;
use App\Models\BedType;
use App\Models\Booking;
use App\Models\City;
use App\Models\Country;
use App\Models\Facility;
use App\Models\GeoObject;
use App\Models\Property;
use App\Models\User;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomType;
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
        $response->assertJsonCount(1,'properties');
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
        $response->assertJsonCount(1,'properties');
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
        $response->assertJsonCount(1,'properties');
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
        $response->assertJsonCount(1,'properties');
        $response->assertJsonFragment(['id'=>$property1->id]);

    }

    public function test_property_search_by_apartment_capacity_returns_only_apartments(): void
    {
        $owner = User::factory()->create(['role_id'=> Role::OWNER_ROLE]);
        $city = City::value('id');
        $property1 = Property::factory()->create([
            'owner_id'=> $owner->id,
            'city_id' => $city
        ]);

         //small apartment property
         $apartment2 = Apartment::factory()->create([
            'name' => 'Small apartment',
            'property_id'=> $property1->id,
            'capacity_adults' => 1,
            'capacity_children' => 0,
        ]);

        //large apartment property
        $apartment1 = Apartment::factory()->create([
            'name' => 'Large apartment',
            'property_id'=> $property1->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);
       
        $response = $this->getJson('/api/search?city='.$city.'&adults=2&children=1');
        $response->assertStatus(200);
        $response->assertJsonCount(1,'properties'); //match count property values
        $response->assertJsonCount(1, "data.0.apartments"); //0.apartment is the property first apartment data
        $response->assertJsonPath('data.0.apartments.0.name', $apartment1->name);

    }

    public function test_property_search_by_beds_list(): void
    {
        $owner = User::factory()->create(['role_id'=> Role::OWNER_ROLE]);
        $city = City::value('id');
        $roomTypes = RoomType::all();
        $bedTypes = BedType::all();

        $property1 = Property::factory()->create([
            'owner_id'=> $owner->id,
            'city_id' => $city
        ]);

        $apartment = Apartment::factory()->create([
            'name' => 'Small apartment',
            'property_id'=> $property1->id,
            'capacity_adults' => 1,
            'capacity_children' => 0,
        ]);

        $room = Room::factory()->create([
            'apartment_id'=> $apartment->id,
            'room_type_id'=> $roomTypes[0]->id,
            'name' => 'Bedroom',
        ]);

        // $room2 = Room::factory()->create([
        //     'apartment_id'=> $apartment->id,
        //     'room_type_id'=> $roomTypes[0]->id,
        //     'name' => 'Bedroom 2',
        // ]);

        $bed = Bed::factory()->create([
            'room_id'=> $room->id,
            'bed_type_id'=> $bedTypes[0]->id,
        ]);

        $bed2 = Bed::factory()->create([
            'room_id'=> $room->id,
            'bed_type_id'=> $bedTypes[0]->id,
        ]);

        $bed3 = Bed::factory()->create([
            'room_id'=> $room->id,
            'bed_type_id'=> $bedTypes[0]->id,
        ]);

        $bed4 = Bed::factory()->create([
            'room_id'=> $room->id,
            'bed_type_id'=> $bedTypes[1]->id,
        ]);

        $bed5 = Bed::factory()->create([
            'room_id'=> $room->id,
            'bed_type_id'=> $bedTypes[1]->id,
        ]);

        $response = $this->getJson('/api/search?city='.$city);
        $response->assertStatus(200);
        $response->assertJsonCount(1,'properties');
        $response->assertJsonCount(1,"properties.0.apartments");
        // check that bed list if empty if no beds
        // $response->assertJsonPath('data.0.apartments.0.bed_lists','');
        
        // check that bed list of 1 room with 1 bed
        // $response->assertJsonPath('data.0.apartments.0.bed_lists','1 '.$bedTypes[0]->name);
        
        // check that bed list of 1 room with 2 bed
        // $response->assertJsonPath('data.0.apartments.0.bed_lists','2 '.str($bedTypes[0]->name)->plural());

        // add one bed to that second room
        // $response->assertJsonPath('data.0.apartments.0.bed_lists','3 '.str($bedTypes[0]->name)->plural());

        //add another bed with a different type to that second room
        $response->assertJsonPath('properties.0.apartments.0.bed_lists', '5 beds(3 ' .str($bedTypes[0]->name)->plural() .', 2 '.str($bedTypes[1]->name)->plural().')');
    }

    public function test_property_search_returns_one_best_apartment_per_property(): void
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

        $response = $this->getJson('/api/search?city='.$city.'&adults=2&children=1');
        // dd($response->json());
        $response->assertStatus(200);
        $response->assertJsonCount(1,'properties'); 
        $response->assertJsonCount(1, 'properties.0.apartments');
        $response->assertJsonPath('properties.0.apartments.0.name', $apartment3->name);

    }
    
    public function test_property_search_filters_by_facilities()
    {
        $owner = User::factory()->create(['role_id'=> Role::OWNER_ROLE]);
        $city = City::value('id');
        $property1 = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city,
        ]);
        $apartment1 = Apartment::factory()->create([
            'name' => 'Mid size apartment',
            'property_id' => $property1->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);
        $property2 = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city,
        ]);
        $apartment2 = Apartment::factory()->create([
            'name' => 'Mid size apartment',
            'property_id' => $property2->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);

        //first -> no facilities exits
        // $response = $this->getJson('/api/search?city='.$city.'&adults=2&children=1');
        // $response->assertStatus(200);
        // $response->assertJsonCount(2,'properties'); 

        // second -> filter by facility, 0 properties returned
        $facility = Facility::create(['name'=>'new facility']);
        // $response = $this->getJson('/api/search?city='.$city.'&adults=2&children=1&facilities[]='.$facility->id);
        // $response->assertStatus(200);
        // $response->assertJsonCount(0,'properties'); 

        // third -> attach facility to property, filter by facility, 1 property returned
        $property1->facilities()->attach($facility->id);
        // $response = $this->getJson('/api/search?city='.$city.'&adults=2&children=1&facilities[]='.$facility->id);
        // $response->assertStatus(200);
        // $response->assertJsonCount(1,'properties'); 

        // fourth -> attach facility to a DIFFERENT property, filter by facility, 2 properties returned
        $property2->facilities()->attach($facility->id);
        $response = $this->getJson('/api/search?city='.$city.'&adults=2&children=1&facilities[]='.$facility->id);
        $response->assertStatus(200);
        $response->assertJsonCount(2,'properties'); 
    }

    public function test_property_search_filter_by_price()
    {
        $owner = User::factory()->create(['role_id'=> Role::OWNER_ROLE]);
        $city = City::value('id');
        $property1 = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city,
        ]);
        $apartment1 = Apartment::factory()->create([
            'name' => 'first floor apartment',
            'property_id' => $property1->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);
        $apartment1->prices()->create([
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'price' => 80,
        ]);

        $property2 = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city,
        ]);
        $apartment2 = Apartment::factory()->create([
            'name' => 'top floor apartment',
            'property_id' => $property2->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);
        $apartment2->prices()->create([
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'price' => 150,
        ]);
        // first case - no price range: both apartment price returned
        $response = $this->getJson('/api/search?city=' . $city . '&adults=2&children=1');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'properties');

        // second case - min price set: 1 apartment price returned
        $response = $this->getJson('/api/search?city=' . $city . '&adults=2&children=1&price_from=100');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'properties');

         // third case - max price set: 2 apartment price returned
         $response = $this->getJson('/api/search?city=' . $city . '&adults=2&children=1&price_to=200');
         $response->assertStatus(200);
         $response->assertJsonCount(2, 'properties');

          // fourth case - both price set: 0 apartment price returned
          $response = $this->getJson('/api/search?city=' . $city . '&adults=2&children=1&price_from=90&price_to=120');
          $response->assertStatus(200);
          $response->assertJsonCount(0, 'properties');
    
    }

    public function test_properties_show_correct_rating_and_order_by_it()
    {
        $owner = User::factory()->create(['role_id'=> Role::OWNER_ROLE]);
        $city = City::value('id');
        $property1 = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city,
        ]);
        $apartment1 = Apartment::factory()->create([
            'name' => 'first floor apartment',
            'property_id' => $property1->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);

        $property2 = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city,
        ]);
        $apartment2 = Apartment::factory()->create([
            'name' => 'top floor apartment',
            'property_id' => $property2->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);

        $user1 = User::factory()->create(['role_id'=> Role::USER_ROLE]);
        $user2 = User::factory()->create(['role_id'=> Role::USER_ROLE]);

        $booking1 = Booking::create([
            'apartment_id' => $apartment1->id,
            'user_id' => $user1->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'guest_adults' => 3,
            'guest_children' => 1,
            'rating' => 9,
            'review_comment' => 'Excellent !, I love it very much and have wonderful time.'
        ]);

        $booking2 = Booking::create([
            'apartment_id' => $apartment2->id,
            'user_id' => $user2->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'guest_adults' => 3,
            'guest_children' => 1,
            'rating' => 2,
            'review_comment' => 'Worst , I am very much disappoint. No wifi not very good services'
        ]);

        $booking3 = Booking::create([
            'apartment_id' => $apartment2->id,
            'user_id' => $user1->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'guest_adults' => 3,
            'guest_children' => 1,
            'rating' => 5,
            'review_comment' => 'Not so much good as expected but not soo bad also. Its an average'
        ]);

        $response = $this->getJson('/api/search?city=' . $city . '&adults=2&children=1');
        $response->assertStatus(200);
        $response->assertJsonCount(2,'properties');
        //booking rating of property 1
        $this->assertEquals(9.0000,$response->json('properties')[0]['avg_rating']);
        //booking rating of property 2
        $this->assertEquals(3.5000,$response->json('properties')[1]['avg_rating']);

    }
}
