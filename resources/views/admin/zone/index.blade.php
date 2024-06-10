@extends('admin.layouts.app')

@section('title', 'Main page')

@section('content')

    <!-- Start Page content -->
    <section class="content">
        {{-- <div class="container-fluid"> --}}

        <div class="row">
            <div class="col-12">
                <div class="box">

                    <div class="box-header with-border">
                        <div class="row text-right">
                            <div class="col-8 col-md-3">
                                <div class="form-group">
                                    <input type="text" id="search_keyword" name="search" class="form-control"
                                        placeholder="@lang('view_pages.enter_keyword')">
                                </div>
                            </div>

                            <div class="col-4 col-md-2 text-left">
                                <button id="search" class="btn btn-success btn-outline btn-sm py-2" type="submit">
                                    @lang('view_pages.search')
                                </button>
                            </div>
                            @if(auth()->user()->can('add-zone'))            

                            <div class="col-md-3  text-md-right">
                                <a href="{{ url('zone/create') }}" class="btn btn-primary btn-sm">
                                    <i class="mdi mdi-plus-circle mr-2"></i>@lang('view_pages.add_zone')</a>
                                <!--  <a class="btn btn-danger">
                                        Export</a> -->
                            </div>
                            
                            
                            @endif
                            <div class="col-md-3 text-md-left">
                                <button id="openModalButton" type="button" class="btn btn-warning btn-sm">Swipe Zone<i class="mdi mdi-plus-circle mr-2"></i> </button> 
                                    
                            </div>
                            <!-- <div class="box-controls pull-right">
                    <div class="lookup lookup-circle lookup-right">
                      <input type="text" name="s">
                    </div>
                  </div> -->
                        </div>

                    </div>

                    <div id="js-zone-partial-target">
                        <include-fragment src="zone/fetch">
                            <span style="text-align: center;font-weight: bold;"> @lang('view_pages.loading')</span>
                        </include-fragment>
                    </div>

                </div>
            </div>
        </div>

        {{-- </div> --}}
        <!-- container -->


 <div class="modal" id="swapPositionsModal">
            <div class="modal-dialog">
                <div class="modal-content">
        
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Swap Positions</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
        
                    <!-- Modal Body -->
                    <div class="modal-body">
                        <form method="POST" action="{{ route('swap_positions') }}">
                            @csrf
                            <label for="zone1">Zone 1:</label>
                            <label for="zone1">Zone 1:</label>
                            <select name="zone1_id" id="zone1" class="form-control">
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }} (Position {{ $zone->position }})</option>
                                @endforeach
                            </select>
        
                            <!-- Zone 2 Dropdown -->
                            <label for="zone2">Zone 2:</label>
                            <select name="zone2_id" id="zone2" class="form-control">
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }} (Position {{ $zone->position }})</option>
                                @endforeach
                            </select>
        
                            <button type="submit" class="btn btn-success mt-5">Submit</button>
                        </form>
                    </div>
        
                </div>
            </div>
        </div>
        {{-- </div> --}}
        <!-- container -->


        <script src="{{ asset('assets/js/fetchdata.min.js') }}"></script>
        <script>
            
    $(document).ready(function() {
        $("#openModalButton").click(function() {
            $("#swapPositionsModal").modal('show');

        });
        $(".close").click(function() {
            $("#swapPositionsModal").modal('hide');
            
        });
    }); 
            var search_keyword = '';
            $(function() {
                $('body').on('click', '.pagination a', function(e) {
                    e.preventDefault();
                    var url = $(this).attr('href');
                    $.get(url, $('#search').serialize(), function(data) {
                        $('#js-zone-partial-target').html(data);
                    });
                });

                $('#search').on('click', function(e) {
                    e.preventDefault();
                    search_keyword = $('#search_keyword').val();

                    fetch('zone/fetch?search=' + search_keyword)
                        .then(response => response.text())
                        .then(html => {
                            document.querySelector('#js-zone-partial-target').innerHTML = html
                        });
                });


            });

            $(document).on('click', '.sweet-delete', function(e) {
                e.preventDefault();

                let url = $(this).attr('data-url');

                swal({
                    title: "Are you sure to delete ?",
                    type: "error",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Delete",
                    cancelButtonText: "No! Keep it",
                    closeOnConfirm: false,
                    closeOnCancel: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        swal.close();

                        $.ajax({
                            url: url,
                            cache: false,
                            success: function(res) {

                                fetch('zone/fetch?search=' + search_keyword)
                                    .then(response => response.text())
                                    .then(html => {
                                        document.querySelector('#js-zone-partial-target')
                                            .innerHTML = html
                                    });

                                $.toast({
                                    heading: '',
                                    text: res,
                                    position: 'top-right',
                                    loaderBg: '#ff6849',
                                    icon: 'success',
                                    hideAfter: 5000,
                                    stack: 1
                                });
                            }
                        });
                    }
                });
            });

        </script>
    @endsection
