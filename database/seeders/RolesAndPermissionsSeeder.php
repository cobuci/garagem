<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (PermissionEnum::cases() as $permission) {
            Permission::findOrCreate($permission->value);
        }

        $adminRole = Role::findOrCreate('admin');
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::findOrCreate('manager');
        $managerRole->givePermissionTo(Permission::all());

        $userRole = Role::findOrCreate('user');
        $userRole->givePermissionTo([
            PermissionEnum::ViewProduct->value,
            PermissionEnum::ViewCustomer->value,
            PermissionEnum::ViewCategory->value,
            PermissionEnum::ViewSale->value,
            PermissionEnum::CreateSale->value,
        ]);
    }
}
