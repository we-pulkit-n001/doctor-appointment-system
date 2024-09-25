<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToAppointments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('vh_appointments', function (Blueprint $table) {
            $table->date('date')->after('name')->nullable();
            $table->time('time')->after('date')->nullable();
            $table->enum('status', ['Booked', 'Cancelled'])->after('time')->nullable();
        });
    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropColumns('vh_appointments');
    }
}
