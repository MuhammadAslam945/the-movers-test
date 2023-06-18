@extends('admin.layouts.app')

@section('content')
    <!-- Start Page content -->
    <section class="content">

        <div class="row">
            @foreach ($card as $item)
                <div class="col-xl-4 col-md-6 col-12">
                    <div class="box box-body">
                        <h5 class="text-capitalize">{{ $item['display_name'] }}</h5>
                        <div class="flexbox wid-icons mt-2">
                            <span class="{{ $item['icon'] }} font-size-40"></span>
                            <span class=" font-size-30">{{ $item['count'] }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <input type="hidden" id="items" name="items" value="{{ $items }}">

        <div class="content">

            <div class="row">
                <div class="col-12">
                    <div class="box">

                        @livewire('admin.driver.request-list-component', ['driver' => $driver], key($driver->id))




                    </div>
                </div>
            </div>
        </div>



    </section>
@endsection
