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
                'email'     => 'superadmin@gmail.com',
                'password'  => bcrypt('superadmin'),
                'name'      => 'Superadmin',
                'role'      => 'Super Admin',
                'address'   => 'JETISWETAN, PEDAN, KABUPATEN KLATEN, JAWA TENGAH',
                'phone'     => "+6285702151766",
                'image'     => null,
                'status'    => true
            ],
            [
                'email'     => 'employee@gmail.com',
                'password'  => bcrypt('employee'),
                'name'      => 'Employee 1',
                'role'      => 'Secretary',
                'address'   => 'JETISWETAN, PEDAN, KABUPATEN KLATEN, JAWA TENGAH',
                'phone'     => "+6285702151766",
                'image'     => null,
                'status'    => true
            ]
        ];

        foreach($users as $item) {
            $user = User::where('email', $item['email'])->first();
            if(empty($user)){
                $store = User::create([
                    'email'     => $item['email'],
                    'password'  => $item['password'],
                    'name'      => $item['name'],
                    'address'   => $item['address'],
                    'phone'     => $item['phone'],
                    'image'     => $item['image'],
                    'role'      => $item['role'],
                    'status'    => $item['status']
                ]);

                $store->assignRole($item['role']);
                
            }
        }
    }
}
