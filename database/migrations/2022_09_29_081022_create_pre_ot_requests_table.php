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
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->unsignedDecimal('mins',5,2);
            $table->text('purpose');
            $table->datetime('approved_start_time')->nullable();
            $table->datetime('approved_end_time')->nullable();
            $table->unsignedDecimal('approved_mins',5,2)->nullable();
            $table->foreignId('requested_user_id');
            $table->foreignId('approved_user_id')->nullable();
            $table->datetime('approved_date')->nullable();
            $table->datetime('checkin')->nullable();
            $table->datetime('checkout')->nullable();
            $table->unsignedDecimal('ot_mins',5,2)->nullable();
            $table->string('status')->default('pending');
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
