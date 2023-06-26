<?php

namespace App\Rules;

use App\Models\Apartment;
use App\Models\Booking;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class ApartmentAvailableRule implements ValidationRule, DataAwareRule 
{
    protected $data = [];

    /* to access other fields in addition to the apartment_id,
     we use the $this->data array. For that to work, we need to add two things:
        Class should implement DataAwareRule and add it in the use section
        Class should have $data property and setData() method,
    */
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $apartment = Apartment::find($value);
        if(!$apartment) {
            $fail('The :attribute must be uppercase.');
            // $fail('Apartment not found');
        }

        // if($apartment->capacity_adults < $this->data['guest_adults'] || 
        //     $apartment->capacity_children < $this->data['guest_children']){
        //     $fail('This apartment does not fit your requirements');
        // }

        // if(Booking::where('apartment_id', $value)->validForRange([$this->data['start_date'],$this->data['end_date']])->exists())
        // {
        //     $fail('This apartment is not available for those dates');
        // }
    }

    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }
}
