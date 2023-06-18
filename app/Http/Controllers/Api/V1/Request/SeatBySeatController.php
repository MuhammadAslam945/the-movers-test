<?php

namespace App\Http\Controllers\api\v1\request;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeatBookingRequest;
use App\Models\Admin\SeatBooking;
use App\Models\Admin\Zone;
use App\Models\SeatPrice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SeatBySeatController extends Controller
{
    public function getActiveSeatPricesByCity()
    {
        $seatPrices = SeatPrice::where('status', 'active')
            ->get();
        return response()->json($seatPrices, 200);
    }
    public function checkSeatAvailability()
    {
        $currentTime = Carbon::now();
        $thresholdTime = $currentTime->subMinutes(30);

        $seatBookings = SeatBooking::where('front_seat', '>', 0)
            ->where('back_left', '>', 0)
            ->where('back_center', '>', 0)
            ->where('back_right', '>', 0)
            ->where('traveling_date', '>=', $currentTime->toDateString())
            ->where('moving_time', '>=', $thresholdTime->toTimeString())
            ->where('ride_status', '=', 'schedule')
            ->get();

        if ($seatBookings->isEmpty()) {
            return response()->json(['message' => 'No available seats'], 404);
        }

        return response()->json($seatBookings, 200);
    }
    public function getZoneByCity($city)
    {

        try {
            $zones = Zone::where('city', $city)->get();
            if ($zones) {
                return response()->json($zones, 200);
            } else {
                return response()->json('Currently this city is not available!', 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
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
            $booking->update($request->all());

            return response()->json(['message' => 'Seat booking for remaining seats successful', 'booking' => $booking], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Seat booking for remaining seats failed'], 500);
        }
    }


}
