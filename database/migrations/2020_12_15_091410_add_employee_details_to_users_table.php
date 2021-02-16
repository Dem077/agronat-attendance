<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployeeDetailsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('emp_no')->nullable();
            $table->string('designation')->nullable();
            $table->string('mobile')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('department_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('emp_no');
            $table->dropColumn('designation');
            $table->dropColumn('mobile');
            $table->dropColumn('phone');
            $table->dropColumn('department_id');
        });
    }
}
