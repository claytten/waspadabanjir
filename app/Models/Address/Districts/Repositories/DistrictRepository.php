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
use Illuminate\Support\Facades\Cache;

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
     * List all the regencies
     *
     * @param string $order
     * @param string $sort
     *
     * @return Collection
     */
    public function listDistricts(string $order = 'id', string $sort = 'desc', $except = []) : Collection
    {
        return Cache::rememberForever('districts', function () use ($order, $sort, $except) {
            return $this->model->withCount(['villages'])->orderBy($order, $sort)->get()->except($except);
        });
    }

    /**
     * Create the province
     *
     * @param array $data
     *
     * @return District
     */
    public function createDistrict(array $data): District
    {
        try {
            if (Cache::has('districts') || Cache::has('regencies')) {
                Cache::forget('districts');
                Cache::forget('regencies');
            }
            return $this->create($data);
        } catch (QueryException $e) {
            throw new CreateDistrictInvalidArgumentException($e);
        }
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
            if (Cache::has('districts')) {
                Cache::forget('districts');
            }
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
     * @return bool
     * @throws \Exception
     */
    public function deleteDistrict() : bool
    {
        
        if (Cache::has('districts')) {
            Cache::forget('districts');
            if($this->model->countVillage() > 0) {
                Cache::forget('villages');
            }
        }
        return $this->model->delete();
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

    /**
     * Listing data regency of Kabupaten Klaten
     * 
     * @return string
     */
    public function listDistrictByRegencies(): string
    {
        $districts = $this->listDistricts()->sortBy('name')->where('regency_id', 3310);
        $message = "--MENU KECAMATAN--\nBerikut Daftar Kecamatan yang ada pada Kabupaten Klaten : \n";
        $reportForm = route('form.report');
        if(count($districts) > 0) {
            $count = 0;
            foreach($districts as $index => $item) {
                $count = $count+1;
                $message .= "{$count}. {$item['name']} \n";
            }
            $message .= "\nPilih *SATU ANGKA* diatas untuk melihat daftar banjir di kecamatan tersebut.";
        } else {
            $message .= "Belum ada data Kecamatan pada sistem . Silahkan  kamu bikin laporan pada form berikut {$reportForm}.";
        }
        return $message;
    }

    /** Sample filter listing regency of kabupaten klaten
     * 
     * @return array
     */
    public function createListDistrictMenu(): array
    {
      return Cache::rememberForever('listDistrictsMenu', function () {
        $districts = $this->listDistricts()->sortBy('name')->where('regency_id', 3310);
        $setAddress = array();
        $count = 0;
        foreach($districts as $item) {
            array_push($setAddress, array('id' => $count+1, 'name' => $item['name']));
            $count += 1;
        }
        return $setAddress;
      });
    }
}
