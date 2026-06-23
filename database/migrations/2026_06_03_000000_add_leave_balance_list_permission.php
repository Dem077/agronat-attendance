<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class AddLeaveBalanceListPermission extends Migration
{
    public function up()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(
            ['name' => 'leave-balance-list', 'guard_name' => 'web']
        );
    }

    public function down()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::where('name', 'leave-balance-list')->where('guard_name', 'web')->delete();
    }
}
