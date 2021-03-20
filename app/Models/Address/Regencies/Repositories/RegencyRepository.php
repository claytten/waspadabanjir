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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection as Support;

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
            return $this->model->update($params);
        } catch (QueryException $e) {
            throw new UpdateRegencyInvalidArgumentException($e);
        }
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
