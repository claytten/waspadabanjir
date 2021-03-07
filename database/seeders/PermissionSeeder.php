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
        $permissions = [
            'admin-list',
            'admin-create',
            'admin-edit',
            'admin-delete',

            'roles-list',
            'roles-create',
            'roles-edit',
            'roles-delete',
        ];

        // Create Permission
        foreach ($permissions as $permission) {
            $db_permission = Permission::whereName($permission)->first();
            if(empty($db_permission)){
                Permission::create(['name' => $permission]);
            }
        }

        $role = Role::where('name', 'superadmin')->first();
        if(empty($role)){
            // If role is empty, create a new one
            $role = Role::create(['name' => 'superadmin']); // Add New Role Data
        }

        $role->syncPermissions($permissions);
        
    }
}
