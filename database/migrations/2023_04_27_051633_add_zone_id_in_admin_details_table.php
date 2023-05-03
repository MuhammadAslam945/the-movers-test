<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZoneIdInAdminDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_details', function (Blueprint $table) {
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
        Schema::table('admin_details', function (Blueprint $table) {
              $table->uuid('zone_id')->after('service_location_id')->nullable();
            $table->foreign('zone_id')
                    ->references('id')
                    ->on('zones');
        });
    }
}
