<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\ApiController;
use App\Http\Requests\SeatBookingRequest;
use App\Models\Admin\Zone;
use App\Models\City;
use App\Models\SeatPrice;
use App\Transformers\CityTransformer;

use App\Models\Admin\SeatBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
/**
 * @resource Cities
 *
 * Get cities
 */
class CityController extends ApiController
{
    /**
     * Get all the cities.
     *@hideFromAPIDocumentation
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $citiesQuery = City::active();

        $cities = filter($citiesQuery, new CityTransformer)->defaultSort('name')->get();

        return $this->respondOk($cities);
    }
    public function getZone()
    {

        try {
            $zones = Zone::whereNotNull('city')->orderby('city','asc')->get(['id', 'name', 'city']);

            if ($zones->isEmpty()) {
                return response()->json('Currently this city is not available!', 200);
            }

            return $this->respondOk($zones);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }

    }

    /**
     * Get all cities by state
     *
     *@hideFromAPIDocumentation
     * @return \Illuminate\Http\JsonResponse
     */
    public function byState($state_id)
    {
        $citiesQuery = City::where('state_id', $state_id);

        $cities = filter($citiesQuery, new CityTransformer)->defaultSort('name')->get();

        return $this->respondOk($cities);
    }
    /**
     * Get a city by its id.
     *
     *@hideFromAPIDocumentation
     * @param City $city
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(City $city)
    {
        $city = filter()->transformWith(new CityTransformer)->loadIncludes($city);

        return $this->respondOk($city);
    }
    public function getActiveSetPricesByCity()
    {
        $setPrices = SeatPrice::where('status', 'active')
            ->get();

        return response()->json($setPrices, 200);
    }
    public function checkSeatAvailability()
    {
        $currentTime = Carbon::now();
        $thresholdTime = $currentTime->subMinutes(30);

        $seatBookings = SeatBooking::where('front_seat', '>=', 0)
            ->orWhere('back_left', '>=', 0)
            ->orWhere('back_center', '>=', 0)
            ->orWhere('back_right', '>=', 0)
            ->orWhere('traveling_date', '>=', $currentTime->toDateString())
            ->where('moving_time', '>=', $thresholdTime->toTimeString())
            ->where('ride_status', '=', 'schedule')
            ->get();

        if ($seatBookings->isEmpty()) {
            return response()->json(['message' => 'No available seats'], 404);
        }

        return response()->json($seatBookings, 200);
    }


    public function seatBooking(SeatBookingRequest $request)
    {
        try {
            $request->validated();
            $seatBooking = SeatBooking::create($request->all());
            $seats = SeatBooking::find($seatBooking->id);
            return response()->json([$seatBooking, $seats], 201);
        } catch (\Exception $e) {

            return response()->json(['error' => 'Seat booking failed'], 500);
        }
    }
    public function userArrived($seatBookingId, $passengerNumber)
    {
        $seatBooking = SeatBooking::where('id', $seatBookingId)
            ->where('ride_status', '=', 'schedule')
            ->whereDate('traveling_date', Carbon::now()->toDateString())
            ->firstOrFail();

        $pStatusColumn = "p{$passengerNumber}_status";
        if (!Schema::hasColumn('seat_bookings', $pStatusColumn)) {
            return response()->json(['message' => 'Invalid passenger number'], 400);
        }
        $seatBooking->$pStatusColumn = 2;
        $seatBooking->save();

        return response()->json(['message' => 'Passenger status updated successfully'], 200);
    }

    public function updateRideStatus($seatBookingId, $status)
    {

        $seatBooking = SeatBooking::findOrFail($seatBookingId);
        $seatBooking->ride_status = $status;
        $seatBooking->save();

        $message = ($status === 'started') ? 'Ride started successfully' : 'Ride completed successfully';

        return response()->json(['message' => $message], 200);
    }
    public function cancelRide($rideId)
    {
        $ride = SeatBooking::findOrFail($rideId);

        if ($ride->ride_status !== 'schedule') {
            return response()->json(['message' => 'Cannot cancel a ride that is not in schedule status'], 400);
        }

        $ride->ride_status = 'cancelled';
        $ride->save();

        $seatBookings = SeatBooking::where('ride_id', $ride->id)->get();

        foreach ($seatBookings as $seatBooking) {
            $seatBooking->ride_status = 'cancelled';
            $seatBooking->save();
        }

        return response()->json(['message' => 'Your Ride has been  cancelled due to some technical reasons'], 200);
    }
    public function bookRemainingSeats(Request $request, $bookingId)
{
    try {
        $booking = SeatBooking::findOrFail($bookingId);

        $remainingSeats = $this->getRemainingSeats($booking);

        if ($remainingSeats <= 0) {
            return response()->json(['message' => 'No remaining seats available'], 400);
        }

        // Validate and process the seat booking request for remaining seats
        $remainingSeatBooking = new SeatBookingRequest();
        // Add any specific validation rules for the remaining seat booking if needed

        // You can modify the following code to customize the remaining seat booking details
        $remainingSeatBooking->merge($request->all());
        $remainingSeatBooking->merge(['booking_id' => $bookingId]);
        $remainingSeatBooking->validate();

        $seatBooking = SeatBooking::create($remainingSeatBooking->all());

        return response()->json(['message' => 'Seat booking for remaining seats successful', 'booking' => $seatBooking], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Seat booking for remaining seats failed'], 500);
    }
}

private function getRemainingSeats($booking)
{
    // Calculate the remaining seats based on the initial booking and already booked seats
    $initialSeats = $booking->front_seat + $booking->back_left + $booking->back_center + $booking->back_right;
    $bookedSeats = SeatBooking::where('booking_id', $booking->id)->sum('seats');

    $remainingSeats = $initialSeats - $bookedSeats;

    return $remainingSeats;
}

}
