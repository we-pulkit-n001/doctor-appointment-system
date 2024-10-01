<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnsInVhDoctors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('vh_doctors', function (Blueprint $table) {
            $table->time('working_hours_start')->nullable()->after('phone')->change();
            $table->time('working_hours_end')->nullable()->after('working_hours_start')->change();
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
