<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PropertiesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_owner_can_add_property()
    {
        $owner = User::factory()->create(['role_id'=> Role::OWNER_ROLE]);
        $this->actingAs($owner);
        $response = $this->post('api/owner/property',[
            'name' => 'Property Name',
            'city_id' => City::value('id'),
            'address_street' => 'Street Address 1',
            'address_postcode' => '12345',
            'lat' => 23.00000,
            'long' => 28.000000,
        ]);
        $response->assertStatus(201);
        $response->assertJsonFragment(['name'=>'Property Name']);
    }
}
