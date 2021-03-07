<?php

namespace App\Providers;

use App\Models\Accounts\Employees\Repositories\EmployeeRepository;
use App\Models\Accounts\Employees\Repositories\Interfaces\EmployeeRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {

        $this->app->bind(
            EmployeeRepositoryInterface::class,
            EmployeeRepository::class
        );
    }
}
