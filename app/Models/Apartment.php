<?php

namespace App\Models;

use Carbon\Carbon;
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

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class);
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

    public function prices()
    {
        return $this->hasMany(ApartmentPrice::class);
    }

   public function calculatePriceForDates($startDate, $endDate)
   {
        $cost = 0;
        // Convert to Carbon if not already
        if(!$startDate instanceof Carbon)
        {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }

        if(!$endDate instanceof Carbon)
        {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }
        //startdate is less than equal to enddate
        while($startDate->lte($endDate))
        {
            $cost += $this->prices->where(function( ApartmentPrice $price ) use ($startDate) {
                return $price->start_date->lte($startDate) && $price->end_date->gte($startDate);
            })->value('price');
            $startDate->addDay();
        }
        return $cost;
   }
}
