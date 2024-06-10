<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seat_prices', function (Blueprint $table) {
            $table->id();
            $table->double('front_seat')->default(0);
            $table->double('back_left')->default(0);
            $table->double('back_right')->default(0);
            $table->double('back_center')->default(0);
            $table->string('pick_city');
            $table->string('drop_city');
            $table->enum('status',['active','inactive'])->default('active');
            $table->double('price')->default(17000);
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
        Schema::dropIfExists('seat_prices');
    }
}
