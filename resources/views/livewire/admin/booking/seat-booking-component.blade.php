<div class="main">
    <div class="row m-1">
        <div class="modal fade" id="request-modal" wire:ignore>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Assign Driver to Booking Ride</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body text-left">
                        @if (Session::has('message'))
                            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                        @endif
                        <div class="request-status">
                            <form wire:submit.prevent="updateDriver">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="label-control">Booking ID</label>
                                            <input type="text" name="ride_id" id="ride_id" class="form-control"
                                                wire:model="ride_id">
                                            @error('ride_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="label-control">Driver ID</label>
                                            <input type="text" name="driver_id" id="driver_id" class="form-control"
                                                wire:model="driver_id">
                                            @error('driver_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="label-control">Amount Paid Driver</label>
                                            <input type="text" name="paid_amount" id="paid_amount"
                                                class="form-control" wire:model="paid_amount">
                                            @error('paid_amount')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="label-control">Total Cost Vehicle</label>
                                            <input type="text" name="rent_cost" id="rent_cost" class="form-control"
                                                wire:model="rent_cost">
                                            @error('rent_cost')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Add Driver</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label for="search" class="label-control">Search Here</label>
                <input type="text" name="search" id="search" class="form-control"
                    placeholder="search by booking ID, date ...." wire:model="search">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="byFrunch" class="label-control">Sort By Frunchise</label>
                <select name="byFrunch" id="byFrunch" class="form-control" wire:model="byFrunch">
                    <option value="">Select Frunchise</option>
                    @foreach ($zones as $zone)
                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="perPage" class="label-control">Booking PerPage</label>
                <select name="perPage" id="perPage" class="form-control" wire:model="perPage">
                    <option value="">Select Page Size</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="18">18</option>
                    <option value="24">24</option>
                    <option value="32">32</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="sorting" class="label-control">Default Sorting</label>
                <select name="sorting" id="sorting" class="form-control" wire:model="sorting">
                    <option value="desc">Select Sorting Type</option>
                    <option value="desc">New</option>
                    <option value="asc">Old</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <label for="status" class="label-control">Assgin Driver</label>
            <button class="btn btn-outline btn-sm btn-danger py-2" type="button" data-toggle="modal"
                data-target="#request-modal">
                @lang('view_pages.assign_driver')
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mt-1">
            <thead>
                <th style="font-size:11px;font-weight:900;">Sr.No</th>
                <th style="font-size:11px;font-weight:900;">B.Detail</th>
                <th style="font-size:11px;font-weight:900;">Pickup FRN</th>
                <th style="font-size:11px;font-weight:900;">Drop FRN</th>
                <th style="font-size:11px;font-weight:900;">B.Seats</th>
                <th style="font-size:11px;font-weight:900;">R.Status</th>
                <th style="font-size:11px;font-weight:900;">Fare</th>
                <th style="font-size:11px;font-weight:900;">TM.CM</th>
                <th style="font-size:11px;font-weight:900;">FRN.CM</th>
                <th style="font-size:11px;font-weight:900;">R.Cost</th>
                <th style="font-size:11px;font-weight:900;">Action</th>
            </thead>
            <tbody>
                @php
                    $i = 1;
                @endphp
                @forelse ($bookings as $booking)
                    <tr>
                        <td>
                            {{ $i++ }}
                        </td>
                        <td>
                            <div style="font-size:13px;font-weight:700;">
                                <p>Booking ID:&nbsp;<span>Booking_{{ $booking->id }}</span></p>
                                <p>Booking
                                    Date:&nbsp;<span>{{ \Carbon\Carbon::parse($booking->created_at)->isoFormat('MMM Do YYYY') }}</span>
                                </p>
                                <p>Travelling
                                    Date:&nbsp;<span>{{ \Carbon\Carbon::parse($booking->traveling_date)->isoFormat('MMM Do YYYY') }}</span>
                                </p>
                                <p>Travelling Time:&nbsp;<span>{{ $booking->moving_time }}</span></p>
                                <p>Driver:&nbsp;<span>
                                        @if ($booking->driver_id)
                                            {{ $booking->driver->name }}
                                        @else
                                            No Driver Assign Yet
                                        @endif
                                    </span></p>
                                <p>Vehicle:&nbsp;<span>
                                        @if ($booking->vehicle_no)
                                            {{ $booking->vehicle_no }}
                                        @else
                                            No Driver Assign Yet
                                        @endif
                                    </span></p>
                                <p>Passenger 1:&nbsp;<span>
                                        @if ($booking->p_1 != null)
                                            {{ $booking->passenger1->name }}
                                        @else
                                            No User Found
                                        @endif
                                    </span></p>
                                <p>Passenger 2:&nbsp;<span>
                                        @if ($booking->p_2 != null)
                                            {{ $booking->passenger2->name }}
                                        @else
                                            No User Found
                                        @endif
                                    </span></p>
                                <p>Passenger 3:&nbsp;<span>
                                        @if ($booking->p_3 != null)
                                            {{ $booking->passenger3->name }}
                                        @else
                                            No User Found
                                        @endif
                                    </span></p>
                                <p>Passenger 4:&nbsp;<span>
                                        @if ($booking->p_4 != null)
                                            {{ $booking->passenger4->name }}
                                        @else
                                            No User Found
                                        @endif
                                    </span></p>


                            </div>
                        </td>
                        <td>{{ $booking->pickupZone->name }}</td>
                        <td>{{ $booking->dropZone->name }}</td>
                        <td>{{ $booking->seats }}</td>
                        <td><span class="badge bg-primary">{{ $booking->ride_status }}</span></td>
                        <td>{{ $booking->price }}</td>
                        <td>{{ $booking->admin_commission }}</td>
                        <td>{{ $booking->frunchise_commission }}</td>
                        <td>{{ $booking->rent_cost }}</td>
                        <td>
                            <div>
                                <a href="@" class="btn btn-success"><i class="fa fa-pencil"></i></a>
                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <p class="text-center">Ops there is no record found</p>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
