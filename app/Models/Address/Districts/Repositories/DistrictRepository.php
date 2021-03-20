<?php

namespace App\Models\Address\Districts\Repositories;

use Jsdecena\Baserepo\BaseRepository;
use App\Models\Address\Districts\District;
use App\Models\Address\Districts\Exceptions\CreateDistrictInvalidArgumentException;
use App\Models\Address\Districts\Exceptions\DistrictNotFoundException;
use App\Models\Address\Districts\Exceptions\UpdateDistrictInvalidArgumentException;
use App\Models\Address\Districts\Repositories\Interfaces\DistrictRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection as Support;

class DistrictRepository extends BaseRepository implements DistrictRepositoryInterface
{
    /**
     * DistrictRepository constructor.
     * @param District $district
     */
    public function __construct(District $district)
    {
        parent::__construct($district);
        $this->model = $district;
    }

    /**
     * Update the district
     *
     * @param array $params
     *
     * @return bool
     * @throws UpdateDistrictInvalidArgumentException
     */
    public function updateDistrict(array $params) : bool
    {
        try {
            return $this->model->update($params);
        } catch (QueryException $e) {
            throw new UpdateDistrictInvalidArgumentException($e);
        }
    }

    /**
     * Find the district or fail
     *
     * @param int $id
     *
     * @return District
     * @throws DistrictNotFoundException
     */
    public function findDistrictById(int $id) : District
    {
        try {
            return $this->findOneOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new DistrictNotFoundException($e);
        }
    }

    /**
     * @param string $text
     * @return mixed
     */
    public function searchDistrict(string $text = null) : Collection
    {
        if (is_null($text)) {
            return $this->all();
        }
        return $this->model->searchDistrict($text)->get();
    }
}
