<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToDoctors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('vh_doctors', function (Blueprint $table) {
            $table->string('specialization')->after('name')->nullable();
            $table->string('email')->after('specialization')->nullable();
            $table->string('phone')->after('email')->nullable();
            $table->string('working_hours_start')->after('phone')->nullable();
            $table->string('working_hours_end')->after('working_hours_start')->nullable();
        });
    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropColumns('vh_doctors');
    }
}
