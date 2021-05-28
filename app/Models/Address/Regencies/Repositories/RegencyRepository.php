<?php

namespace App\Models\Address\Regencies\Repositories;

use Jsdecena\Baserepo\BaseRepository;
use App\Models\Address\Regencies\Regency;
use App\Models\Address\Regencies\Exceptions\CreateRegencyInvalidArgumentException;
use App\Models\Address\Regencies\Exceptions\RegencyNotFoundException;
use App\Models\Address\Regencies\Exceptions\UpdateRegencyInvalidArgumentException;
use App\Models\Address\Regencies\Repositories\Interfaces\RegencyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;

class RegencyRepository extends BaseRepository implements RegencyRepositoryInterface
{
    /**
     * RegencyRepository constructor.
     * @param Regency $regency
     */
    public function __construct(Regency $regency)
    {
        parent::__construct($regency);
        $this->model = $regency;
    }

    /**
     * List all the regencies
     *
     * @param string $order
     * @param string $sort
     *
     * @return Collection
     */
    public function listRegencies(string $order = 'id', string $sort = 'desc', $except = []) : Collection
    {
        return Cache::rememberForever('regencies', function () use ($order, $sort, $except) {
            return $this->model->withCount('districts')->orderBy($order, $sort)->get()->except($except);
        });
    }

    /**
     * Create the province
     *
     * @param array $data
     *
     * @return Regency
     */
    public function createRegency(array $data): Regency
    {
        try {
            if (Cache::has('regencies') || Cache::has('provinces')) {
                Cache::forget('regencies');
                Cache::forget('provinces');
            }
            return $this->create($data);
        } catch (QueryException $e) {
            throw new CreateRegencyInvalidArgumentException($e);
        }
        
    }

    /**
     * Update the regency
     *
     * @param array $params
     *
     * @return bool
     * @throws UpdateRegencyInvalidArgumentException
     */
    public function updateRegency(array $params) : bool
    {
        try {
            if (Cache::has('regencies')) {
                Cache::forget('regencies');
            }
            return $this->model->update($params);
        } catch (QueryException $e) {
            throw new UpdateRegencyInvalidArgumentException($e);
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function deleteRegency() : bool
    {
        
        if (Cache::has('regencies')) {
            Cache::forget('regencies');
            if($this->model->countDistrict() > 0) {
                Cache::forget('districts');
                Cache::forget('villages');
            }
        }
        return $this->model->delete();
    }

    /**
     * Find the regency or fail
     *
     * @param int $id
     *
     * @return Regency
     * @throws RegencyNotFoundException
     */
    public function findRegencyById(int $id) : Regency
    {
        try {
            return $this->findOneOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new RegencyNotFoundException($e);
        }
    }

    /**
     * Find the regency by name
     * 
     * @param string $name
     * 
     * @return Regency
     */
    public function findRegencyByName(string $name)
    {
        return Regency::where('name', 'LIKE', "%{$name}%")->get();
    }

    /**
     * @param string $text
     * @return mixed
     */
    public function searchRegency(string $text = null) : Collection
    {
        if (is_null($text)) {
            return $this->all();
        }
        return $this->model->searchRegency($text)->get();
    }
}
