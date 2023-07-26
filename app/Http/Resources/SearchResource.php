<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address, //getting mutators
            'lat' => $this->lat,
            'long' => $this->long,
            'apartments' => ApartmentSearchResource::collection($this->apartments),
            'photos' => $this->photos,
            'avg_rating' => $this->bookings_avg_rating,
        ];
    }
}
