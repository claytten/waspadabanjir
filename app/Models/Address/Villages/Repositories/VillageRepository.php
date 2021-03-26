<?php

namespace App\Models\Address\Villages\Repositories;

use Jsdecena\Baserepo\BaseRepository;
use App\Models\Address\Villages\Village;
use App\Models\Address\Villages\Exceptions\CreateVillageInvalidArgumentException;
use App\Models\Address\Villages\Exceptions\VillageNotFoundException;
use App\Models\Address\Villages\Exceptions\UpdateVillageInvalidArgumentException;
use App\Models\Address\Villages\Repositories\Interfaces\VillageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

class VillageRepository extends BaseRepository implements VillageRepositoryInterface
{
    /**
     * VillageRepository constructor.
     * @param Village $village
     */
    public function __construct(Village $village)
    {
        parent::__construct($village);
        $this->model = $village;
    }

    /**
     * List all the villages
     *
     * @param string $order
     * @param string $sort
     *
     * @return Collection
     */
    public function listVillages(string $order = 'id', string $sort = 'desc', $except = []) : Collection
    {
        return Cache::rememberForever('villages', function () use ($order, $sort, $except) {
            return $this->model->orderBy($order, $sort)->get()->except($except);
        });
    }

    /**
     * Update the village
     *
     * @param array $params
     *
     * @return bool
     * @throws UpdateVillageInvalidArgumentException
     */
    public function updateVillage(array $params) : bool
    {
        try {
            if (Cache::has('villages')) {
                Cache::forget('villages');
            }
            return $this->model->update($params);
        } catch (QueryException $e) {
            throw new UpdateVillageInvalidArgumentException($e);
        }
    }

    /**
     * Create the province
     *
     * @param array $data
     *
     * @return Village
     */
    public function createVillage(array $data): Village
    {
        try {
            if (Cache::has('villages') || Cache::has('districts')) {
                Cache::forget('villages');
                Cache::forget('districts');
            }
            return $this->create($data);
        } catch (QueryException $e) {
            throw new CreateVillageInvalidArgumentException($e);
        }
    }

    /**
     * Find the village or fail
     *
     * @param int $id
     *
     * @return Village
     * @throws VillageNotFoundException
     */
    public function findVillageById(int $id) : Village
    {
        try {
            return $this->findOneOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new VillageNotFoundException($e);
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function deleteVillage() : bool
    {
        
        if (Cache::has('villages')) {
            Cache::forget('villages');
        }
        return $this->model->delete();
    }

    /**
     * @param string $text
     * @return mixed
     */
    public function searchVillage(string $text = null) : Collection
    {
        if (is_null($text)) {
            return $this->all();
        }
        return $this->model->searchVillage($text)->get();
    }
}
