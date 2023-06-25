<?php

namespace App\Http\Controllers\Publics;

use App\Http\Controllers\Controller;
use App\Http\Resources\SearchResource;
use App\Models\Facility;
use App\Models\GeoObject;
use App\Models\Property;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    //for geolocation
    //https://inovector.com/blog/get-locations-nearest-the-user-location-with-mysql-php-in-laravel
    public function __invoke(Request $request)
    {
        $properties = Property::with(['city','apartments.apartment_type',
                        'apartments.rooms.beds.bed_type','facilities',
                        'apartments.prices' => function($query) use ($request){
                            $query->validForRange([
                                $request->start_date ?? now()->addDay()->toDateString(),
                                $request->end_date ?? now()->addDays(2)->toDateString(),
                            ]);
                        }])
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
                                    ->where('capacity_children','>=',$request->children)
                                    ->orderBy('capacity_adults') 
                                    ->orderBy('capacity_children')
                                    ->take(1);
                                    ;

                        });
                    })
                    ->when($request->facilities,function($q) use ($request){
                        $q->whereHas('facilities',function($query)  use ($request){
                            $query->whereIn('facilities.id',$request->facilities);
                        });
                    })
                    ->latest()->get();
                    
        $facilities = Facility::query()->withCount(['properties'=> function($q) use ($properties){
                            $q->whereIn('properties.id',$properties->pluck('id'));
                        }])->get()
                        ->where('properties_count','>',0)
                        ->sortByDesc('properties_count')
                        ->pluck('properties_count','name');
                        
        return response()->json([
            'properties' => SearchResource::collection($properties),
            'facilities' => $facilities
            // 'data' => $property
        ]);
                
    }
}
