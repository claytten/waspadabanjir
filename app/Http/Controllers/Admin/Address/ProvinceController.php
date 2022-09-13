<?php

namespace App\Http\Controllers\Admin\Address;

use App\Models\Address\Provinces\Repositories\Interfaces\ProvinceRepositoryInterface;
use App\Models\Address\Provinces\Repositories\ProvinceRepository;
use App\Models\Address\Provinces\Province;
use App\Models\Address\Provinces\Requests\CreateProvinceRequest;
use App\Models\Address\Provinces\Requests\UpdateProvinceRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use DataTables;

class ProvinceController extends Controller
{
    /**
     * @var ProfinceRepositoryInterface
     */
    private $provinceRepo;

    /**
     * Province Controller Constructor
     *
     * @param ProvinceRepositoryInterface $ProvinceRepository
     * @return void
     */
    public function __construct(
        ProvinceRepositoryInterface $provinceRepository
    ) {
        // Spatie ACL Provinces
        $this->middleware('permission:provinces-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:provinces-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:provinces-delete', ['only' => ['destroy']]);

        // binding repository
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
            $provinces = [];
            $provinces = $this->provinceRepo->listProvinces()->sortBy('name');
            return response()->json([
                'data'=> $provinces
            ]);
        }
        $provinces = $this->provinceRepo->listProvinces()->sortBy('name');
        return view('admin.address.provinces.index', compact('provinces'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProvinceRequest $request)
    {
        $data = $request->except('_token','_method');
        $data['name'] = strtoupper($data['name']);
        $province = $this->provinceRepo->createProvince($data);
        $province->regencies_count = 0;
        if (Cache::has('provinces')) {
            Cache::forget('provinces');
        }

        return response()->json([
            'status'    => 'success',
            'message'   => 'Create Province successful!',
            'data'      => $province
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProvinceRequest $request, $id)
    {
        $data = $request->except('_token','_method');
        $data['name'] = strtoupper($data['name']);
        $province = $this->provinceRepo->findProvinceById($id);

        $provRepo = new ProvinceRepository($province);
        $provRepo->updateProvince($data);
        $province->regencies_count = 0;
        
        if (Cache::has('provinces')) {
            Cache::forget('provinces');
        }
        $this->provinceRepo->listProvinces()->sortBy('name');

        return response()->json([
            'status'    => 'success',
            'message'   => 'Create Province successful!',
            'data'      => $province,
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
        $province = $this->provinceRepo->findProvinceById($id);
        if (Cache::has('provinces')) {
            Cache::forget('provinces');
            if($province->countRegency() > 0) {
                Cache::forget('regencies');
                Cache::forget('districts');
                Cache::forget('villages');
            }
        }
        $getProvince = new ProvinceRepository($province);
        $getProvince->deleteProvince();

        return response()->json([
            'status'      => 'success',
            'message'     => 'Province successfully destroy'
        ]);
    }
}
