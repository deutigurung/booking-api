<?php

use App\Models\Property;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



// Route::get('/property-image-upload', function () {
//     $properties = Property::all();
//     foreach($properties as $p){
//         $p->addMediaFromUrl(fake()->imageUrl())
//         ->toMediaCollection('media');
//     }
//     return 'image uploaded';
// });
