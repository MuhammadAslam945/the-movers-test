<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seat_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('driver_id')->nullable();
            $table->foreign('driver_id')->references('id')->on('drivers');
            $table->string('vehicle_no')->nullable();
            $table->uuid('pickup_franchise');
            $table->foreign('pickup_franchise')->references('id')->on('zones');
            $table->integer('seat1')->default(0)->comment('0 for available for bookin,user_id show booked,');
            $table->unsignedInteger('p_1')->nullable();
            $table->foreign('p_1')->references('id')->on('users');
            $table->integer('p1_status')->default(0);
            $table->integer('seat2')->default(0)->comment('0 for available for bookin,user_id show booked,');
            $table->unsignedInteger('p_2')->nullable();
            $table->foreign('p_2')->references('id')->on('users');
            $table->integer('p2_status')->default(0);
            $table->integer('seat3')->default(0)->comment('0 for available for bookin,user_id show booked,');
            $table->unsignedInteger('p_3')->nullable();
            $table->foreign('p_3')->references('id')->on('users');
            $table->integer('p3_status')->default(0);
            $table->integer('seat4')->default(0)->comment('0 for available for bookin,user_id show booked,');
            $table->unsignedInteger('p_4')->nullable();
            $table->foreign('p_4')->references('id')->on('users');
            $table->integer('p4_status')->default(0);
            $table->uuid('drop_franchise');
            $table->foreign('drop_franchise')->references('id')->on('zones');
            $table->date('traveling_date');
            $table->time('moving_time');
            $table->enum('ride_status', ['scheduled', 'started', 'completed', 'canceled'])->default('scheduled');
            $table->text('pickup_address');
            $table->text('drop_address');
            $table->double('pickup_lng', 15, 8)->nullable();
            $table->double('pickup_lat', 15, 8)->nullable();
            $table->double('drop_lng', 15, 8)->nullable();
            $table->double('drop_lat', 15, 8)->nullable();
            $table->integer('seats')->default(0);
            $table->double('price')->default(0);
            $table->integer('admin_commission')->default(0);
            $table->integer('frunchise_commission')->default(0);
            $table->double('paid_driver')->default(0);
            $table->double('rent_cost')->default(0);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seat_bookings');
    }
}
