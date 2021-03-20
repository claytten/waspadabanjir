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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection as Support;

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
            return $this->model->update($params);
        } catch (QueryException $e) {
            throw new UpdateVillageInvalidArgumentException($e);
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
