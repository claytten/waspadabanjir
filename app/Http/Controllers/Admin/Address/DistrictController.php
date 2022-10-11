<?php

namespace App\Http\Controllers\Admin\Address;

use App\Models\Address\Provinces\Repositories\Interfaces\ProvinceRepositoryInterface;
use App\Models\Address\Districts\Repositories\Interfaces\DistrictRepositoryInterface;
use App\Models\Address\Districts\Repositories\DistrictRepository;
use App\Models\Address\Districts\District;
use App\Models\Address\Districts\Requests\UpdateDistrictRequest;
use App\Models\Address\Districts\Requests\CreateDistrictRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DistrictController extends Controller
{

    /**
     * @var ProfinceRepositoryInterface
     */
    private $provinceRepo;

    /**
     * @var DistrictRepositoryInterface
     */
    private $districtRepo;

    /**
     * District Controller Constructor
     *
     * @param DistrictRepositoryInterface $DistrictRepository
     * @return void
     */
    public function __construct(
        ProvinceRepositoryInterface $provinceRepository,
        DistrictRepositoryInterface $districtRepository
    ) {
        // Spatie ACL Districts
        $this->middleware('permission:districts-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:districts-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:districts-delete', ['only' => ['destroy']]);

        // binding repository
        $this->provinceRepo = $provinceRepository;
        $this->districtRepo = $districtRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $districts = [];
            if (!empty($request->regencies)) {
                $districts = $this->districtRepo->listDistricts()->sortBy('name')->where('regency_id', $request->regencies);
            }
            return response()->json([
                'data' => $districts
            ]);
        }

        $provinces = $this->provinceRepo->listProvinces()->sortBy('name');
        return view('admin.address.districts.index', compact('provinces'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateDistrictRequest $request)
    {
        $data = $request->except('_token', '_method');
        $data['name'] = strtoupper($data['name']);
        $district = $this->districtRepo->createDistrict($data);
        $district->villages_count = 0;

        $this->provinceRepo->listProvinces()->sortBy('name');
        $this->districtRepo->listDistricts()->sortBy('name');

        return response()->json([
            'status'    => 'success',
            'message'   => 'Data Kecamatan Berhasil Ditambahkan!',
            'data'      => $district
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
    public function update(UpdateDistrictRequest $request, $id)
    {
        $data = $request->except('_token', '_method');
        $data['name'] = strtoupper($data['name']);
        $district = $this->districtRepo->findDistrictById($id);
        $distRepo = new DistrictRepository($district);
        $distRepo->updateDistrict($data);

        $district->villages_count = 0;
        $this->districtRepo->listDistricts()->sortBy('name');

        return response()->json([
            'status'    => 'success',
            'message'   => 'Data Kecamatan Berhasil Diubah!',
            'data'      => $district
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
        $district = $this->districtRepo->findDistrictById($id);
        $distRepo = new DistrictRepository($district);
        $distRepo->deleteDistrict();

        return response()->json([
            'status'    => 'success',
            'message'   => 'Data Kecamatan Berhasil Dihapus!'
        ]);
    }
}
