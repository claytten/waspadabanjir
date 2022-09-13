<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminPermissions = [
            'admin-list',
            'admin-create',
            'admin-edit',
            'admin-delete',

            'roles-list',
            'roles-create',
            'roles-edit',
            'roles-delete',

            'provinces-list',
            'provinces-create',
            'provinces-edit',
            'provinces-delete',

            'regencies-list',
            'regencies-create',
            'regencies-edit',
            'regencies-delete',

            'districts-list',
            'districts-create',
            'districts-edit',
            'districts-delete',

            'villages-list',
            'villages-create',
            'villages-edit',
            'villages-delete',

            'setting-list'
        ];

        $employeePermissions = [
            'maps-list',
            'maps-create',
            'maps-edit',
            'maps-delete',

            'subscriber-list',
            'subscriber-create',
            'subscriber-edit',
            'subscriber-delete',

            'reports-list',
            'reports-create',
            'reports-edit',
            'reports-delete'
        ];

        $roles = [
            'Super Admin',
            'Secretary'
        ];

        // Create Permission
        foreach (array_merge($adminPermissions, $employeePermissions) as $permission) {
            $db_permission = Permission::whereName($permission)->first();
            if(empty($db_permission)){
                Permission::create(['name' => $permission]);
            }
        }

        foreach($roles as $item) {
            $role = Role::where('name', $item)->first();
            if(empty($role)){
                // If role is empty, create a new one
                $role = Role::create(['name' => $item]); // Add New Role Data
            }
            if($item == 'Super Admin') {
                $role->syncPermissions(array_merge($adminPermissions, $employeePermissions));
            } else {
                $role->syncPermissions($employeePermissions);
            }
        }
        
    }
}
