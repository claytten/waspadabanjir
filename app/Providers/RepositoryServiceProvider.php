<?php

namespace App\Providers;

use App\Models\Users\Repositories\UserRepository;
use App\Models\Users\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\Accounts\Admins\Repositories\AdminRepository;
use App\Models\Accounts\Admins\Repositories\Interfaces\AdminRepositoryInterface;
use App\Models\Accounts\Employees\Repositories\EmployeeRepository;
use App\Models\Accounts\Employees\Repositories\Interfaces\EmployeeRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register Service
     */
    public function register()
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
        $this->app->bind(
            AdminRepositoryInterface::class,
            AdminRepository::class
        );
        $this->app->bind(
            EmployeeRepositoryInterface::class,
            EmployeeRepository::class
        );
    }

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
