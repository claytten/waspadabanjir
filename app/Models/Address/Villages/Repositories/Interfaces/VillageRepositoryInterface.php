<?php

namespace App\Models\Address\Villages\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Models\Address\Villages\Village;
use Illuminate\Database\Eloquent\Collection;

interface VillageRepositoryInterface extends BaseRepositoryInterface
{
    public function listVillages(): Collection;

    public function createVillage(array $data): Village;

    public function updateVillage(array $params) : bool;

    public function deleteVillage() : bool;

    public function findVillageById(int $id) : Village;

    public function searchVillage(string $text) : Collection;

    public function checkDuplicateVillage(string $name, int $district_id): ?Village;
}
