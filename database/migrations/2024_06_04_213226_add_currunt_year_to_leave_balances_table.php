<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurruntYearToLeaveBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_balances', function (Blueprint $table) {
            $table->boolean('currunt_year')->default(false); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_balances', function (Blueprint $table) {
            $table->dropColumn('currunt_year'); 
        });
    }
}
