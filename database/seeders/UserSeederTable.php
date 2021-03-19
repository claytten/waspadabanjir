<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Users\User;
use Spatie\Permission\Models\Role;

class UserSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'username'  => 'superadmin',
                'password'  => bcrypt('superadmin'),
                'role'      => 'admin',
                'assignRole'=> 'Super Admin',
                'status'    => true,
            ],
            [
                'username'  => 'employee1',
                'password'  => bcrypt('employee'),
                'role'      => 'employee',
                'assignRole'=> 'Secretary',
                'status'    => true,
            ]
        ];

        foreach($users as $item) {
            $user = User::where('username', $item['username'])->first();
            if(empty($user)){
                $store = User::create([
                    'username' => $item['username'],
                    'password' => $item['password'],
                    'role' => $item['role'],
                    'status' => $item['status']
                ]);

                $store->assignRole($item['assignRole']);
                
            }
        }
    }
}
