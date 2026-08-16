<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogIdToTimeSheetsTable extends Migration
{
    public function up()
    {
        if (Schema::hasColumn('time_sheets', 'log_id')) {
            return;
        }

        Schema::table('time_sheets', function (Blueprint $table) {
            $table->string('log_id', 64)->nullable()->after('user_id');
        });
    }

    public function down()
    {
        if (! Schema::hasColumn('time_sheets', 'log_id')) {
            return;
        }

        Schema::table('time_sheets', function (Blueprint $table) {
            $table->dropColumn('log_id');
        });
    }
}
