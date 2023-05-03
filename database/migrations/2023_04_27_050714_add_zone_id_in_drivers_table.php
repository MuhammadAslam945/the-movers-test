<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZoneIdInDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->uuid('zone_id')->after('service_location_id')->nullable();
            $table->foreign('zone_id')
                    ->references('id')
                    ->on('zones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->uuid('zone_id')->after('service_location_id')->nullable();
            $table->foreign('zone_id')
                    ->references('id')
                    ->on('zones');
        });
    }
}
