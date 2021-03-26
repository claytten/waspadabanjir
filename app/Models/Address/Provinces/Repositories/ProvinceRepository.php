<?php

namespace App\Models\Address\Provinces\Repositories;

use Jsdecena\Baserepo\BaseRepository;
use App\Models\Address\Provinces\Province;
use App\Models\Address\Provinces\Exceptions\CreateProvinceInvalidArgumentException;
use App\Models\Address\Provinces\Exceptions\ProvinceNotFoundException;
use App\Models\Address\Provinces\Exceptions\UpdateProvinceInvalidArgumentException;
use App\Models\Address\Provinces\Repositories\Interfaces\ProvinceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection as Support;
use Illuminate\Support\Facades\Cache;

class ProvinceRepository extends BaseRepository implements ProvinceRepositoryInterface
{
    /**
     * ProvinceRepository constructor.
     * @param Province $province
     */
    public function __construct(Province $province)
    {
        parent::__construct($province);
        $this->model = $province;
    }

    /**
     * List all the provinces
     *
     * @param string $order
     * @param string $sort
     *
     * @return Collection
     */
    public function listProvinces(string $order = 'id', string $sort = 'desc', $except = []) : Collection
    {
        return Cache::rememberForever('provinces', function () use ($order, $sort, $except) {
            return $this->model->withCount('regencies')->orderBy($order, $sort)->get()->except($except);
        });
    }

    /**
     * Create the province
     *
     * @param array $data
     *
     * @return Province
     */
    public function createProvince(array $data): Province
    {
        try {
            return $this->create($data);
        } catch (QueryException $e) {
            throw new CreateProvinceInvalidArgumentException($e);
        }
    }

    /**
     * Update the province
     *
     * @param array $params
     *
     * @return bool
     * @throws UpdateProvinceInvalidArgumentException
     */
    public function updateProvince(array $params) : bool
    {
        try {
            return $this->model->update($params);
        } catch (QueryException $e) {
            throw new UpdateProvinceInvalidArgumentException($e);
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function deleteProvince() : bool
    {
        return $this->model->delete();
    }

    /**
     * Find the province or fail
     *
     * @param int $id
     *
     * @return Province
     * @throws ProvinceNotFoundException
     */
    public function findProvinceById(int $id) : Province
    {
        try {
            return $this->findOneOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ProvinceNotFoundException($e);
        }
    }

    /**
     * @param string $text
     * @return mixed
     */
    public function searchProvince(string $text = null) : Collection
    {
        if (is_null($text)) {
            return $this->all();
        }
        return $this->model->searchProvince($text)->get();
    }
}
