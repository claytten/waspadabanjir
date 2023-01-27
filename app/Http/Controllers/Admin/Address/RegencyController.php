<?php

namespace App\Http\Controllers\Admin\Address;

use App\Models\Address\Provinces\Repositories\Interfaces\ProvinceRepositoryInterface;
use App\Models\Address\Regencies\Repositories\Interfaces\RegencyRepositoryInterface;
use App\Models\Address\Regencies\Repositories\RegencyRepository;
use App\Models\Address\Regencies\Regency;
use App\Models\Address\Regencies\Requests\CreateRegencyRequest;
use App\Models\Address\Regencies\Requests\UpdateRegencyRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RegencyController extends Controller
{
    /**
     * @var ProvinceRepositoryInterface
     */
    private $provinceRepo;

    /**
     * @var RegencyRepositoryInterface
     */
    private $regencyRepo;

    /**
     * Province Controller Constructor
     *
     * @param RegencyRepositoryInterface $regencyRepository
     * @param ProvinceRepositoryInterface $provinceRepository
     * @return void
     */
    public function __construct(
        RegencyRepositoryInterface $regencyRepository,
        ProvinceRepositoryInterface $provinceRepository
    ) {
        // Spatie ACL Provinces
        $this->middleware('permission:regencies-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:regencies-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:regencies-delete', ['only' => ['destroy']]);

        // binding repository
        $this->regencyRepo = $regencyRepository;
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
            $regencies = [];
            if (!empty($request->provinces)) {
                $regencies = $this->regencyRepo->listRegencies()->sortBy('name')->where('province_id', $request->provinces);
            } else {
                $regencies = $this->regencyRepo->listRegencies()->sortBy('name');
            }
            return response()->json([
                'data' => $regencies
            ]);
        }
        $provinces = $this->provinceRepo->listProvinces()->sortBy('name');

        return view('admin.address.regencies.index', compact('provinces'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRegencyRequest $request)
    {
        $data = $request->except('_token', '_method');
        $data['name'] = strtoupper($data['name']);
        if (!empty($this->regencyRepo->checkDuplicateRegency($data['name'], $data['province_id']))) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'Data Kabupaten/Kota Sudah Ada pada Provinsi Tersebut!'
            ]);
        }
        $regency = $this->regencyRepo->createRegency($data);
        $regency->districts_count = 0;

        return response()->json([
            'status'    => 'success',
            'message'   => 'Data Kabupaten/Kota Berhasil Ditambahkan!',
            'data'      => $regency
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRegencyRequest $request, $id)
    {
        $regency = $this->regencyRepo->findRegencyById($id);
        $data = $request->except('_token', '_method');
        $data['name'] = strtoupper($data['name']);
        if (!empty($this->regencyRepo->checkDuplicateRegency($data['name'], $data['province_id']))) {
            return response()->json([
                'status'    => 'error',
                'message'   => 'Data Kabupaten/Kota Sudah Ada pada Provinsi Tersebut!'
            ]);
        }

        $regenRepo = new RegencyRepository($regency);
        $regenRepo->updateRegency($data);
        $regency->districts_count = 0;

        return response()->json([
            'status'    => 'success',
            'message'   => 'Data Kabupaten/Kota Berhasil Diubah!',
            'data'      => $regency
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $regency = $this->regencyRepo->findRegencyById($id);
        $getRegency = new RegencyRepository($regency);
        $getRegency->deleteRegency();

        return response()->json([
            'status'      => 'success',
            'message'     => 'Data Kabupaten/Kota Berhasil Dihapus!',
        ]);
    }
}
