<?php

namespace App\Providers;

use App\Models\Users\Repositories\UserRepository;
use App\Models\Users\Repositories\Interfaces\UserRepositoryInterface;
use App\Models\Address\Provinces\Repositories\Interfaces\ProvinceRepositoryInterface;
use App\Models\Address\Provinces\Repositories\ProvinceRepository;
use App\Models\Address\Regencies\Repositories\Interfaces\RegencyRepositoryInterface;
use App\Models\Address\Regencies\Repositories\RegencyRepository;
use App\Models\Address\Districts\Repositories\Interfaces\DistrictRepositoryInterface;
use App\Models\Address\Districts\Repositories\DistrictRepository;
use App\Models\Address\Villages\Repositories\Interfaces\VillageRepositoryInterface;
use App\Models\Address\Villages\Repositories\VillageRepository;
use App\Models\Maps\Fields\Repositories\Interfaces\FieldRepositoryInterface;
use App\Models\Maps\Fields\Repositories\FieldRepository;
use App\Models\Subscribers\Repositories\Interfaces\SubscribeRepositoryInterface;
use App\Models\Subscribers\Repositories\SubscribeRepository;
use App\Models\Reports\Repositories\Interfaces\ReportRepositoryInterface;
use App\Models\Reports\Repositories\ReportRepository;
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
        $this->app->bind(
            FieldRepositoryInterface::class,
            FieldRepository::class
        );
        $this->app->bind(
            SubscribeRepositoryInterface::class,
            SubscribeRepository::class
        );
        $this->app->bind(
            ReportRepositoryInterface::class,
            ReportRepository::class
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
