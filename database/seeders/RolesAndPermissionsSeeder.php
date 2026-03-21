<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $models = [
            'user',
            'customer',
            'category',
            'product',
            'product_purchase',
            'sale',
            'financial_transaction',
            'setting',
            'account_balance',
        ];

        $actions = ['view', 'create', 'edit', 'delete'];

        foreach ($models as $model) {
            foreach ($actions as $action) {
                Permission::findOrCreate("{$action} {$model}");
            }
        }

        $adminRole = Role::findOrCreate('admin');
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::findOrCreate('manager');
        $managerRole->givePermissionTo(Permission::all());
    }
}
