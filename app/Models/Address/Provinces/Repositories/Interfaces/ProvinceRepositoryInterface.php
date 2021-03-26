<?php

namespace App\Models\Address\Provinces\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Models\Address\Provinces\Province;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Support;

interface ProvinceRepositoryInterface extends BaseRepositoryInterface
{
    public function listProvinces(): Collection;

    public function createProvince(array $data): Province;

    public function updateProvince(array $params) : bool;

    public function findProvinceById(int $id) : Province;

    public function deleteProvince() : bool;

    public function searchProvince(string $text) : Collection;
}
