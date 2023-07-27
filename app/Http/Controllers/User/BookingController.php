<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Jobs\UpdatePropertyRatingJob;
use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('bookings-manage');
        $bookings = auth()->user()->bookings()    
                ->with('apartment.property')
                ->withTrashed()
                ->orderBy('start_date')
                ->get();
        return BookingResource::collection($bookings);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $this->authorize('bookings-manage');
        $booking = auth()->user()->bookings()->create($request->validated());
        return new BookingResource($booking);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('bookings-manage');
        $booking = Booking::where('id', $id)->withTrashed()->first();
        if ($booking->user_id != auth()->id()) {
            abort(403);
        }
        return new BookingResource($booking);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Booking $booking, UpdateBookingRequest $request)
    {
        $this->authorize('bookings-manage');

        if($booking->user_id != auth()->id()){
            abort(403);
        }
        $booking->update($request->validated());

        dispatch(new UpdatePropertyRatingJob($booking));
        
        return new BookingResource($booking);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $this->authorize('bookings-manage');
 
        if ($booking->user_id != auth()->id()) {
            abort(403);
        }
        $booking->delete();
        return response()->noContent();
    }
}
