<?php

namespace App\Http\Controllers\Admin\Address;

use App\Models\Address\Provinces\Repositories\Interfaces\ProvinceRepositoryInterface;
use App\Models\Address\Villages\Repositories\Interfaces\VillageRepositoryInterface;
use App\Models\Address\Villages\Repositories\VillageRepository;
use App\Models\Address\Villages\Requests\CreateVillageRequest;
use App\Models\Address\Villages\Requests\UpdateVillageRequest;
use App\Http\Controllers\Controller;
use App\Models\Address\Villages\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VillageController extends Controller
{
    /**
     * @var VillageRepositoryInterface
     */
    private $villageRepo;

    /**
     * @var ProfinceRepositoryInterface
     */
    private $provinceRepo;


    /**
     * Village Controller Constructor
     *
     * @param VillageRepositoryInterface $VillageRepository
     * @param ProvinceRepositoryInterface $ProvinceRepository
     * @return void
     */
    public function __construct(
        VillageRepositoryInterface $villageRepository,
        ProvinceRepositoryInterface $provinceRepository
    ) {
        // Spatie ACL Villages
        $this->middleware('permission:villages-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:villages-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:villages-delete', ['only' => ['destroy']]);

        // binding repository
        $this->villageRepo = $villageRepository;
        $this->provinceRepo = $provinceRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $villages = [];
            if (!empty($request->districts)) {
                $villages = Cache::rememberForever('villages', function () {
                    return $this->villageRepo->listVillages()->sortBy('name');
                })->where('district_id', $request->districts);
            }
            return response()->json([
                'data' => $villages
            ]);
        }

        $provinces = Cache::rememberForever('provinces', function () {
            return $this->provinceRepo->listProvinceAll();
        });
        return view('admin.address.villages.index', compact('provinces'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateVillageRequest $request)
    {
        $data = $request->except('_token', '_method');
        $data['name'] = strtoupper($data['name']);
        if (!empty($this->villageRepo->checkDuplicateVillage($data['name'], $data['district_id']))) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'Data Kecamatan Sudah Ada pada Kabupaten/Kota Tersebut!'
            ]);
        }

        $village = $this->villageRepo->createVillage($data);

        return response()->json([
            'status'    => 'success',
            'message'   => 'Data Kelurahan Berhasil Ditambahkan!',
            'data'      => $village
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVillageRequest $request, $id)
    {
        $village = $this->villageRepo->findVillageById($id);
        $data = $request->except('_token', '_method');
        $data['name'] = strtoupper($data['name']);
        if (!empty($this->villageRepo->checkDuplicateVillage($data['name'], $data['district_id']))) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'Data Kecamatan Sudah Ada pada Kabupaten/Kota Tersebut!'
            ]);
        }

        $vilRepo = new VillageRepository($village);
        $vilRepo->updateVillage($data);

        return response()->json([
            'status'        => 'success',
            'messages'      => 'Data Kelurahan Berhasil Diubah!',
            'data'          => $village
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $village = $this->villageRepo->findVillageById($id);
        $vilRepo = new VillageRepository($village);
        $vilRepo->deleteVillage();

        return response()->json([
            'status'    => 'success',
            'messages'  => 'Data Kelurahan Berhasil Dihapus!'
        ]);
    }
}
