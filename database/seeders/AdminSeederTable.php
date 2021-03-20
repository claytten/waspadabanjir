<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Accounts\Admins\Admin;

class AdminSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admins = [
            [
                'id_user'   => 1,
                'name'      => 'Superadmin',
                'email'     => 'superadmin@admin.com',
                'address_id'=> '34480',
                'phone'     => '+62',
                'image'     => null
            ]
        ];

        foreach($admins as $item) {
            $admin = Admin::where('email', $item['email'])->first();
            if(empty($admin)) {
                $store = Admin::create([
                    'id_user'   => $item['id_user'],
                    'name'      => $item['name'],
                    'email'     => $item['email'],
                    'address_id'=> $item['address_id'],
                    'phone'     => $item['phone'],
                    'image'     => $item['image']
                ]);
            }
        }
    }
}
