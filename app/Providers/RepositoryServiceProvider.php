<?php

namespace App\Providers;

use App\Models\Users\Repositories\UserRepository;
use App\Models\Users\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\Accounts\Admins\Repositories\AdminRepository;
use App\Models\Accounts\Admins\Repositories\Interfaces\AdminRepositoryInterface;
use App\Models\Accounts\Employees\Repositories\EmployeeRepository;
use App\Models\Accounts\Employees\Repositories\Interfaces\EmployeeRepositoryInterface;
use App\Models\Address\Provinces\Repositories\Interfaces\ProvinceRepositoryInterface;
use App\Models\Address\Provinces\Repositories\ProvinceRepository;
use App\Models\Address\Regencies\Repositories\Interfaces\RegencyRepositoryInterface;
use App\Models\Address\Regencies\Repositories\RegencyRepository;
use App\Models\Address\District\Repositories\Interfaces\DistrictRepositoryInterface;
use App\Models\Address\District\Repositories\DistrictRepository;
use App\Models\Address\Villages\Repositories\Interfaces\VillageRepositoryInterface;
use App\Models\Address\Villages\Repositories\VillageRepository;
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
        $this->app->bind(
            ProvinceRepositoryInterface::class,
            ProvinceRepository::class
        );
        $this->app->bind(
            RegencyRepositoryInterface::class,
            RegencyRepository::class
        );
        $this->app->bind(
            DistrictRepositoryInterface::class,
            DistrictRepository::class
        );
        $this->app->bind(
            VillageRepositoryInterface::class,
            VillageRepository::class
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
