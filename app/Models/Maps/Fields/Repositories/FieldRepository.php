<?php

namespace App\Models\Maps\Fields\Repositories;

use App\Models\Maps\FieldImages\FieldImage;
use Jsdecena\Baserepo\BaseRepository;
use App\Models\Maps\Fields\Field;
use App\Models\Maps\Fields\Exceptions\FieldNotFoundException;
use App\Models\Maps\Fields\Repositories\Interfaces\FieldRepositoryInterface;
use App\Models\Maps\Geometries\Geometry;
use App\Models\Tools\UploadableTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Database\QueryException;

class FieldRepository extends BaseRepository implements FieldRepositoryInterface
{
    use UploadableTrait;
    /**
     * FieldRepository constructor.
     *
     * @param Field $field
     */
    public function __construct(Field $field)
    {
        parent::__construct($field);
        $this->model = $field;
    }

    /**
     * List all the fields
     *
     * @param string $order
     * @param string $sort
     *
     * @return Collection
     */
    public function listFields(string $order = 'id', string $sort = 'desc', $except = []) : Collection
    {
        return $this->model->orderBy($order, $sort)->get()->except($except);
    }

    /**
     * Create the Field
     *
     * @param array $data
     *
     * @return Field
     */
    public function createField(array $data): Field
    {
        try {
            $field = $this->model->create($data);
            $this->createGeo($field->id,$data['coordinates']);
            return $field;
        } catch (QueryException $e) {
            throw new FieldNotFoundException($e);
        }
    }

    /**
     * Create the Geometries
     *
     * @param array $data
     *
     *
     */
    private function createGeo($id, $coordinates) {

        $geometry = new Geometry();
        $geometry->geo_type     = "Polygon";
        $geometry->coordinates  = $coordinates;
        $geometry->field_id     = $id;
        $geometry->save();
    }

    /**
     * Find the field by id
     *
     * @param int $id
     *
     * @return Field
     */
    public function findFieldById(int $id): Field
    {
        try {
            return $this->findOneOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new FieldNotFoundException;
        }
    }

    /**
     * Update field
     *
     * @param array $params
     *
     * @return bool
     */
    public function updateField(array $params): bool
    {
        return $this->model->update($params);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function deleteField() : bool
    {
        return $this->delete();
    }

    /**
     * @param Collection $collection
     *
     * @return void
     */
    public function saveMapImages(Collection $collection)
    {
        $collection->each(function (UploadedFile $file) {
            $filename = $this->storeFile($file);
            $fieldImage = new FieldImage([
                'field_id'  => $this->model->id,
                'src'       => $filename
            ]);
            $this->model->images()->save($fieldImage);
        });
    }

    /**
     * Destroye File on Storage
     *
     * @param Collection $collection
     *
     *
     */
    public function deleteFiles(Collection $collection)
    {
        $collection->each(function ($file) {
            File::delete("storage/{$file->src}");
        });
    }

    /**
     * Destroye File on Storage
     *
     * @param string $get_data
     *
     */
    public function deleteFile(string $get_data)
    {
        return File::delete("storage/{$get_data}");
    }
}
