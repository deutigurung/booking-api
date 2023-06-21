<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Owner\PropertyController;
use App\Http\Controllers\Publics\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('auth/register',[RegisterController::class,'register']);
Route::post('auth/login',LoginController::class);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $data['user'] = $request->user();
    $data['role'] = $request->user()->role;
    $data['permissions'] = $request->user()->role->permissions;
    return $data;
});

Route::middleware('auth:sanctum')->group(function(){
    Route::prefix('owner/')->group(function(){
        Route::get('all-properties',[PropertyController::class,'index']);
        Route::post('property',[PropertyController::class,'store']);

    });
});


Route::get('search',SearchController::class);

