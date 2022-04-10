<?php

namespace App\Models\Maps\Fields\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Models\Maps\Fields\Field;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface FieldRepositoryInterface extends BaseRepositoryInterface
{
    public function listFields(string $date_in = '', string $date_out = '') : Collection;

    public function listFieldsPublic(string $date_in = ''): Collection;

    public function createField(array $params) : Field;

    public function getDateAttribute($date,$time);
    
    public function convertDateAttribute($date);

    public function convertTimeAttribute($time);

    public function findFieldById(int $id) : Field;

    public function findFieldByAddress(string $address);

    public function updateField(array $params): bool;

    public function deleteField() : bool;

    public function saveMapImages(Collection $collection);

    public function deleteFiles(Collection $collection);

    public function deleteFile(string $get_data);

    public function listFieldsAndGeo(): string;

    public function broadcastField(object $item, string $location): string;
}
