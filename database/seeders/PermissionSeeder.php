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

        $roles = [
            'admin',
            'employee'
        ];

        // Create Permission
        foreach ($permissions as $permission) {
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
            $role->syncPermissions($permissions);
        }
        
    }
}
