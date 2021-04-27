<?php

namespace App\Models\Maps\Fields\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Models\Maps\Fields\Field;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

interface FieldRepositoryInterface extends BaseRepositoryInterface
{
    public function listFields(string $order = 'id', string $sort = 'desc', $except = []) : Collection;

    public function createField(array $params) : Field;

    public function findFieldById(int $id) : Field;

    public function updateField(array $params): bool;

    public function deleteField() : bool;

    public function saveMapImages(Collection $collection);

    public function deleteFiles(Collection $collection);

    public function deleteFile(string $get_data);
}
