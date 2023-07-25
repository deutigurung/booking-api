<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Booking;
use App\Models\City;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookingsTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function create_apartment(){
        $owner = User::factory()->create(['role_id' => Role::OWNER_ROLE]);
        $city = City::value('id');
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $city,
        ]);
        return Apartment::factory()->create([
            'name' => 'Apartment',
            'property_id'=> $property->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);
    }

    public function test_user_can_book_apartment_but_not_twice(){
        $user1 = User::factory()->create(['role_id' => Role::USER_ROLE]);
        $apartment = $this->create_apartment();
        $bookingData = [
            'apartment_id' => $apartment->id,
            'user_id' => $user1->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'guest_adults' => 1,
            'guest_children' => 0,
        ];
        //case 1: store user booking information twice with same data
        $response = $this->actingAs($user1)->postJson('/api/user/bookings/',$bookingData);
        $response->assertStatus(201);

        $response = $this->actingAs($user1)->postJson('/api/user/bookings/',$bookingData);
        $response->assertStatus(422);

        //case 2: store same apartment booking  twice even though date is different
        $bookingData['start_date'] = now()->addDays(3);
        $bookingData['end_date'] = now()->addDays(4);
        $bookingData['guests_adults'] = 5;
        $response = $this->actingAs($user1)->postJson('/api/user/bookings/',$bookingData);
        $response->assertStatus(201);

    }

    public function test_user_can_get_only_their_bookings_data(){
        $user1 = User::factory()->create(['role_id' => Role::USER_ROLE]);
        $user2 = User::factory()->create(['role_id' => Role::USER_ROLE]);

        $apartment = $this->create_apartment();
        $booking1 = Booking::create([
            'apartment_id' => $apartment->id,
            'user_id' => $user1->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'guest_adults' => 3,
            'guest_children' => 1,
        ]);

        $booking2 = Booking::create([
            'apartment_id' => $apartment->id,
            'user_id' => $user2->id,
            'start_date' => now()->addDay(3),
            'end_date' => now()->addDays(4),
            'guest_adults' => 2,
            'guest_children' => 0,
        ]);

        //case 1: user can only view their own booking list
        $response = $this->actingAs($user1)->getJson('/api/user/bookings/');
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['guest_adults' => 3]);

        //case 2: user cannot view other user bookings
        $response = $this->actingAs($user1)->getJson('/api/user/bookings/' . $booking1->id);
        $response->assertStatus(200);
        $response->assertJsonFragment(['guest_adults' => 3]);
 
        $response = $this->actingAs($user1)->getJson('/api/user/bookings/'.$booking2->id);
        $response->assertStatus(403);
    }

    public function test_user_can_cancel_their_booking_but_still_can_view(){
        $user1 = User::factory()->create(['role_id' => Role::USER_ROLE]);
        $user2 = User::factory()->create(['role_id' => Role::USER_ROLE]);

        $apartment = $this->create_apartment();
        $booking1 = Booking::create([
            'apartment_id' => $apartment->id,
            'user_id' => $user1->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'guest_adults' => 3,
            'guest_children' => 1,
        ]);

        //case 1: user can cancel their own booking list
        $response = $this->actingAs($user2)->deleteJson('/api/user/bookings/'.$booking1->id);
        $response->assertStatus(403); 
 
        $response = $this->actingAs($user1)->deleteJson('/api/user/bookings/'.$booking1->id);
        $response->assertStatus(204); 

        //case 2: today cancel bookings
        $response = $this->actingAs($user1)->getJson('/api/user/bookings/');
        $response->assertStatus(200); 
        $response->assertJsonFragment(['cancelled_at' => now()->toDateString()]);

        //case 3: view cancel bookings
        $response = $this->actingAs($user1)->getJson('/api/user/bookings/'.$booking1->id);
        $response->assertStatus(200); 
        $response->assertJsonFragment(['cancelled_at' => now()->toDateString()]);

       
    }

    public function test_user_can_post_rating_for_their_booking(){
        $user1 = User::factory()->create(['role_id' => Role::USER_ROLE]);
        $user2 = User::factory()->create(['role_id' => Role::USER_ROLE]);
        $apartment = $this->create_apartment();
        $booking = Booking::create([
            'apartment_id' => $apartment->id,
            'user_id' => $user1->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'guest_adults' => 1,
            'guest_children' => 0,
        ]);

        //case 1: unauthorized access to user2
        $response = $this->actingAs($user2)->putJson('/api/user/bookings/'.$booking->id,[]);
        $response->assertStatus(403);

        //case 2: return bad request error ie.rating is between 1-10 and comment is min:20 
        $response = $this->actingAs($user1)->putJson('/api/user/bookings/'.$booking->id,[
            "rating" => 50
        ]);
        $response->assertStatus(422);

        $response = $this->actingAs($user1)->putJson('/api/user/bookings/' . $booking->id, [
            'rating' => 10,
            'review_comment' => 'Too short comment.'
        ]);
        $response->assertStatus(422);

        //case 3: right data
        $data = [
            'rating' => 10,
            'review_comment' => 'Very good apartment with best services and features.'
        ];
        $response = $this->actingAs($user1)->putJson('/api/user/bookings/' . $booking->id,$data);
        $response->assertStatus(200);
        $response->assertJsonFragment($data);

    }
}
