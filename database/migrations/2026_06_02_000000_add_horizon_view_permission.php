<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class AddHorizonViewPermission extends Migration
{
    public function up()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(
            ['name' => 'horizon-view', 'guard_name' => 'web']
        );
    }

    public function down()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::where('name', 'horizon-view')->where('guard_name', 'web')->delete();
    }
}
