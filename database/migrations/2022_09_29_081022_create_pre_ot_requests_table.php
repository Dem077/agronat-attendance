<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreOtRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_ot_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->date('ot_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedDecimal('mins',5,2);
            $table->text('purpose');
            $table->time('approved_start_time')->nullable();
            $table->time('approved_end_time')->nullable();
            $table->unsignedDecimal('approved_mins',5,2)->nullable();
            $table->foreignId('approved_by')->nullable();
            $table->foreignId('approved_date')->nullable();
            $table->time('checkin')->nullable();
            $table->time('checkout')->nullable();
            $table->unsignedDecimal('ot_mins',5,2)->nullable();
            $table->string('status');
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
        Schema::dropIfExists('pre_ot_requests');
    }
}
