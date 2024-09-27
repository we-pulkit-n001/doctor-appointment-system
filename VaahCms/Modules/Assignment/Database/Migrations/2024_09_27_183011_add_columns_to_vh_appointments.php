<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToVhAppointments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('vh_appointments', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_id')->after('uuid')->nullable();
            $table->unsignedBigInteger('doctor_id')->after('patient_id')->nullable();

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
