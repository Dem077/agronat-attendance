<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppliedOtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applied_ots', function (Blueprint $table) {
            $table->id();
            $table->string('hash')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->date('ck_date');
            $table->time('in')->nullable();
            $table->time('out')->nullable();
            $table->unsignedInteger('ot')->nullable();
            $table->string('status')->default('Pending');
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
        Schema::dropIfExists('applied_ots');
    }
}
