<?php

namespace App\Models\Address\Districts\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Models\Address\Districts\District;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Support;

interface DistrictRepositoryInterface extends BaseRepositoryInterface
{
    public function updateDistrict(array $params) : bool;

    public function findDistrictById(int $id) : District;

    public function searchDistrict(string $text) : Collection;
}
