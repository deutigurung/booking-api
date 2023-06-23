<?php

namespace App\Http\Controllers\Publics;

use App\Http\Controllers\Controller;
use App\Http\Resources\SearchResource;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function __invoke(Property $property,Request $request)
    {   
        $property->load('apartments.facilities');

        if($request->adults && $request->children)
        {
            $property->load(['apartments' => function($q) use ($request){
                $q->where('capacity_adults','>=',$request->adults)
                    ->where('capacity_children','>=',$request->children)
                    ->orderBy('capacity_adults') 
                    ->orderBy('capacity_children');
            },'apartments.facilities']);
        }
        return new SearchResource($property);
    }
}
