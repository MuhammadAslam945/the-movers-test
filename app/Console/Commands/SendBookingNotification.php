<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Admin\SeatBooking;
use Illuminate\Support\Facades\Http;
use App\Models\Admin\Driver;
class SendBookingNotification extends Command
{
    protected $signature = 'booking:notification';

    protected $description = 'Send booking notifications to the application';

    public function handle()
    {
        $currentDateTime = Carbon::now();
        $bookings = SeatBooking::where('travelling_date', $currentDateTime->toDateString())
            ->where('start_time', $currentDateTime->toTimeString())->where('ride_status','scheduled')
            ->get();
        foreach ($bookings as $booking) {
            $this->sendBookingToDriver($booking);
        }

        $this->info('Booking notifications sent successfully.');
    }

    private function sendBookingToDriver($booking)
    {
        $driverId = $booking->driver_id;

        if (!$driverId) {
            $this->error('Driver not assigned for booking: ' . $booking->id);
            return;
        }

        $driver = Driver::find($driverId);

        if (!$driver) {
            $this->error('Driver not found: ' . $driverId);
            return;
        }

        $bookingDetails = [
            'id' => $booking->id,
            'travelling_date' => $booking->travelling_date,
            'start_time' => $booking->start_time,
            'seats' =>  $booking->seats,
            'drop_address' =>$booking->dropZone->name,
            'passenger1' =>$booking->passenger1->name,
            'passenger2' =>$booking->passenger2->name,
            'passenger3' =>$booking->passenger3->name,
            'passenger4' =>$booking->passenger4->name,
        ];

        // Send the booking details to the driver's mobile app
        $response = Http::post($driver->notification_endpoint, $bookingDetails);
        if ($response->successful()) {
            $this->info('Booking sent to the driver: ' . $booking->id);
        } else {
            $this->error('Failed to send booking to the driver: ' . $booking->id);
        }
    }
}
