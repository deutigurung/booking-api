<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Property extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

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

    //defining accessor methods
    protected function address(): Attribute
    {
        return Attribute::make(
                get: fn () => 
                $this->address_street.','.
                $this->address_postcode.','.
                $this->city->name,
        );
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

    public function facilities()
    {
        return $this->belongsToMany(Facility::class);
    }

    protected function photos(): Attribute
    {
        $photos = [];
        $property = Property::find($this->id);
        $medias = $property->getMedia('*');
        foreach($medias as $m){
            $photos[] = $m->getUrl();
        }
        return Attribute::make(  get: fn () =>  $photos);
    }
}
