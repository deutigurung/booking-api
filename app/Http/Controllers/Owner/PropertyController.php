<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyRequest;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::all();
    }
    public function store(StorePropertyRequest $request)
    {
        $this->authorize('properties-manage');
 
        return Property::create($request->validated());
    }
}
