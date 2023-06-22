<?php

namespace App\Http\Controllers\Publics;

use App\Http\Controllers\Controller;
use App\Models\GeoObject;
use App\Models\Property;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    //for geolocation
    //https://inovector.com/blog/get-locations-nearest-the-user-location-with-mysql-php-in-laravel
    public function __invoke(Request $request)
    {
        $property = Property::with('city','apartments.apartment_type')
                    ->when($request->city,function($query) use ($request){
                        $query->where('city_id',$request->city);
                    })
                    ->when($request->country,function($query) use ($request){
                        $query->whereHas('city', fn($q) =>
                          $q->where('country_id',$request->country)
                        );
                    })
                    ->when($request->geoobject,function($query) use ($request){
                        $geo = GeoObject::find($request->geoobject);
                        if($geo){
                            $query->whereRaw("6371 * acos( 
                                cos(radians(".$geo->lat.")) * cos(radians(`lat`)) * cos(radians(`long`) - 
                                radians(".$geo->long.")) + sin(radians(".$geo->lat.")) * sin(radians(`lat`)))  < 10");
                        }
                    })
                    ->when($request->adults && $request->children,function($query) use ($request){
                        //withWhereHas is combine of with and whereHas 
                        $query->withWhereHas('apartments',function($query) use ($request){
                            $query->where('capacity_adults','>=',$request->adults)
                                    ->where('capacity_children','>=',$request->children);
                        });
                    })
                    ->latest()->get();

        return response()->json([
            'data' => $property
        ]);
                
    }
}
