<?php

namespace App\Http\Controllers\Publics;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApartmentDetailResource;
use App\Models\Apartment;
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    public function __invoke(Apartment $apartment)
    {
        $apartment->load('facilities.category');
        $apartment->setAttribute('facility_categories',
            $apartment->facilities->groupBy('category.name')
            ->mapWithKeys(function($items,$key){
                return [$key => $items->pluck('name')];
            })
        );

        return new ApartmentDetailResource($apartment);
    }
}
