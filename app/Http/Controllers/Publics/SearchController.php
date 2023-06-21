<?php

namespace App\Http\Controllers\Publics;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $property = Property::with('city')
                    ->when($request->city,function($query) use ($request){
                        $query->where('city_id',$request->city);
                    })
                    ->when($request->country,function($query) use ($request){
                        $query->whereHas('city', fn($q) =>
                          $q->where('country_id',$request->country)
                        );
                    })
                    ->latest()->get();

        return response()->json([
            'data' => $property
        ]);
                
    }
}
