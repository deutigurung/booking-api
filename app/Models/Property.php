<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'city_id',
        'address_street',
        'address_postcode',
        'lat',
        'long',
    ];
    /*
        In this method, the Property model has a owner_id field that is automatically populated 
        with the ID of the authenticated user when the Property model is created. 
        This is done by the protected static function boot() method,
        which is called when the Property model is booted. 
        The boot() method contains a creating() closure that is called when a new Property model is created.
        The creating() closure takes the Property model as an argument and sets the owner_id field
        to the ID of the authenticated user.
    */
    protected static function boot()
    {
        parent::boot();
        if(auth()->check()){
            static::creating(function($property){
                $property->owner_id = auth()->user()->id;
            });
        }
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }
}
