<?php

namespace App\Models\Address\Villages\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Models\Address\Villages\Village;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Support;

interface VillageRepositoryInterface extends BaseRepositoryInterface
{
    public function updateVillage(array $params) : bool;

    public function findVillageById(int $id) : Village;

    public function searchVillage(string $text) : Collection;
}
