<?php

namespace App\Http\Controllers\Admin\Address;

use App\Models\Address\Provinces\Repositories\Interfaces\ProvinceRepositoryInterface;
use App\Models\Address\Provinces\Repositories\ProvinceRepository;
use App\Models\Address\Provinces\Province;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


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
    )
    {
        // Spatie ACL Provinces
        $this->middleware('permission:provinces-list',['only' => ['index']]);
        $this->middleware('permission:provinces-create', ['only' => ['create','store']]);
        $this->middleware('permission:provinces-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:provinces-delete', ['only' => ['destroy']]);

        // binding repository
        $this->provinceRepo = $provinceRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $provinces = $this->provinceRepo->listProvinces()->sortBy('name');
        foreach($provinces as $province) {
            $countDistrict = 0;
            $countVillage = 0;
            foreach($province->regencies as $regency) {
                $countDistrict += $regency->countDistrict();
                foreach($regency->districts as $district) {
                    $countVillage += $district->countVillage();
                }
            }
            $province->countRegency = $province->countRegency();
            $province->countDistrict = $countDistrict;
            $province->countVillage = $countVillage;
        }
        return view('admin.address.provinces.index', compact('provinces'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, $id)
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
        //
    }
}
