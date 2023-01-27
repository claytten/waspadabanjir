<?php

namespace App\Models\Address\Districts\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Models\Address\Districts\District;
use Illuminate\Database\Eloquent\Collection;

interface DistrictRepositoryInterface extends BaseRepositoryInterface
{
    public function listDistricts(): Collection;

    public function createDistrict(array $data): District;

    public function updateDistrict(array $params) : bool;

    public function deleteDistrict() : bool;

    public function findDistrictById(int $id) : District;

    public function searchDistrict(string $text) : Collection;

    public function listDistrictByRegencies(): string;

    public function createListDistrictMenu(): array;

    public function checkDuplicateDistrict(string $name, int $regency_id): ?District;
}
