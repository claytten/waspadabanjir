<?php

namespace App\Models\Address\Regencies\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Models\Address\Regencies\Regency;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as Support;

interface RegencyRepositoryInterface extends BaseRepositoryInterface
{
    public function updateRegency(array $params) : bool;

    public function findRegencyById(int $id) : Regency;

    public function searchRegency(string $text) : Collection;
}
