<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeChangeLogTable extends Migration
{
    public function up()
    {
        Schema::create('time_change_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendances_id');
            $table->unsignedBigInteger('time_sheet_id');
            $table->string('changed_by');
            $table->text('reason');
            $table->text('type')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('time_change_logs');
    }
}
