<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Accounts\Employees\Employee;

class EmployeeSeederTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employees = [
            [
                'id_user'   => 2,
                'name'      => 'Employee 1',
                'email'     => 'employee@employee.com',
                'address_id'=> '49088',
                'phone'     => '+62',
                'id_card'   => 'xx',
                'image'     => null
            ]
        ];

        foreach($employees as $item) {
            $employee = Employee::where('email', $item['email'])->first();
            if(empty($employee)) {
                Employee::create([
                    'id_user'   => $item['id_user'],
                    'name'      => $item['name'],
                    'email'     => $item['email'],
                    'address_id'=> $item['address_id'],
                    'phone'     => $item['phone'],
                    'id_card'   => $item['id_card'],
                    'image'     => $item['image']
                ]);
            }
        }
    }
}
