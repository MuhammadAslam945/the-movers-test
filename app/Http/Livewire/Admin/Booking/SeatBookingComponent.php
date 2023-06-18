<?php

namespace App\Http\Livewire\Admin\Booking;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admin\SeatBooking;
use App\Models\Admin\Zone;
use App\Models\Admin\Driver;
class SeatBookingComponent extends Component
{
    use WithPagination;
    public $perPage=5;
    public $sorting='desc';
    public $byFrunch;

    public $search;

    public $driver_id;
    public $paid_amount;

    public $rent_cost;
    public $ride_id;

    public function updateDriver()
    {
       try{
        $booking=SeatBooking::findorFail($this->ride_id);
        $driver=Driver::findorFail($this->driver_id);
        $booking->driver_id=$this->driver_id;
        $booking->vehicle_no=$driver->car_number;
        $booking->paid_driver=$this->paid_amount;
        $booking->rent_cost=$this->rent_cost;
        $booking->save();
        return redirect()->back()->with('message','driver has been updated');
       }catch(\Exception $e){
        return redirect()->back()->with('error',$e->getMessage());
       }
    }
    public function render()
    {
        if(Auth::user()->id != 1){
            $bookings = SeatBooking::where('pickup_franchise',Auth::user()->admin->zone_id)->search($this->search)->orWhere('pickup_franchise',$this->byFrunch)->orderBy('created_at', $this->sorting)
            ->paginate($this->perPage);
        }else{
            $bookings = SeatBooking::search($this->search)->orWhere('pickup_franchise',$this->byFrunch)->orderBy('created_at', $this->sorting)
            ->paginate($this->perPage);

        }
        $zones=Zone::where('active',1)->get();

        return view('livewire.admin.booking.seat-booking-component',['bookings'=>$bookings,
        'zones'=>$zones,]);
    }
}
