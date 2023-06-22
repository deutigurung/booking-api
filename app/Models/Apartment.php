<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Apartment extends Model
{
    use HasFactory;
    protected $fillable = ['apartment_type_id','property_id', 'name', 'capacity_adults', 'capacity_children','size','bathrooms'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function apartment_type()
    {
        return $this->belongsTo(ApartmentType::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function beds()
    {
        return $this->hasManyThrough(Bed::class,Room::class);
    }

    public function bedLists(): Attribute
    {
        $allBeds = $this->beds;
        $bedTypes = $allBeds->groupBy('bed_type.name');
        $bedsList = '';
        $totalBeds = $allBeds->count();
        if($bedTypes->count() == 1){
            $bedsList = $totalBeds.' '.str($bedTypes->keys()[0])->plural($totalBeds);
        }else if($bedTypes->count() > 1){
            $bedsList = $totalBeds.' '.str('bed')->plural($totalBeds);
            $bedsListArray = [];
            foreach($bedTypes as $bedType => $bed){
                $bedsListArray[] = $bed->count().' '.str($bedType)->plural($bed->count());
            }
            $bedsList .='(' .implode(', ', $bedsListArray) .')';
        }
        return Attribute::make(
            get: fn () => $bedsList,
        );
        
    }
}
