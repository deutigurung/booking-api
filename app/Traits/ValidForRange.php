<?php
namespace App\Traits;   
trait ValidForRange
{
     // reset — Set the internal pointer of an array to its first element
    // end - moves the internal pointer to, and outputs, the last element in the array
    public function scopeValidForRange($query , array $range = [])
    {
        return $query->where(function($query) use ($range){
            return $query
            // Covers outer bounds
            ->where(function($q) use ($range){
                $q->where('start_date','>=', reset($range))->orWhere('end_date','<=', end($range));
            })
            // Covers left and right bound
            ->orWhere(function ($query) use ($range) {
                $query->whereBetween('start_date', $range)->orWhereBetween('end_date', $range);
            })
            // Covers inner bounds
            ->orWhere(function ($query) use ($range) {
                $query->where('start_date', '<=', reset($range))
                ->where('end_date', '>=', end($range));  
            })
            ;
        });
    }
}