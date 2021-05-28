<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionSeeder::class);
        $this->call(UserSeederTable::class);
        $this->call(AdminSeederTable::class);
        $this->call(EmployeeSeederTable::class);
        $this->call(ProvincesSeederTable::class);
        $this->call(RegenciesSeederTable::class);
        $this->call(DistrictsSeederTable::class);
        $this->call(VillagesSeederTable::class);
        // $this->call(SubscribersTable::class);
    }
}
