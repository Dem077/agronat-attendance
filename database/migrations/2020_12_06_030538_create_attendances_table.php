<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('ck_date');
            $table->time('sc_in')->nullable();
            $table->time('sc_out')->nullable();
            $table->time('in')->nullable();
            $table->time('out')->nullable();
            $table->unsignedInteger('late_min')->default(0);
            $table->string('status')->nullable(); //Present,Late,Absent,Holiday,Leave
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
        Schema::dropIfExists('attendances');
    }
}
