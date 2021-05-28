<?php

namespace App\Http\Controllers\Front;

use App\Models\Maps\Fields\Repositories\Interfaces\FieldRepositoryInterface;
use App\Models\Subscribers\Repositories\Interfaces\SubscribeRepositoryInterface;
use App\Models\Reports\Repositories\Interfaces\ReportRepositoryInterface;
use App\Models\Subscribers\Requests\CreateSubscribeRequest;
use App\Notifications\SubscribeProcessed;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * @var FieldRepositoryInterface
     */
    private $fieldRepo;

    /**
     * @var SubscribeRepositoryInterface
     */
    private $subscribeRepo;

    /**
     * @var ReportRepositoryInterface
     */
    private $reportRepo;

    /**
     * Province Controller Constructor
     *
     * @param FieldRepositoryInterface $FieldRepository
     * @param SubscribeRepositoryInterface $SubscribeRepository
     * @param ReportRepositoryInterface $reportRepository
     * @return void
     */
    public function __construct(
        FieldRepositoryInterface $fieldRepository,
        SubscribeRepositoryInterface $subscribeRepository,
        ReportRepositoryInterface $reportRepository
    ) {

        // binding repository
        $this->fieldRepo = $fieldRepository;
        $this->subscribeRepo = $subscribeRepository;
        $this->reportRepo = $reportRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $fields = $this->fieldRepo->listFields()->sortBy('name')->where('status', 1);

            $field_response = array(
                "type" => "FeatureCollection",
                "features" => array()
            );
            foreach($fields as $item){
                $temp = array(
                    "type" => "Feature",
                    "properties" => array(
                        "color" => $item->color,
                        "popupContent" => array(
                            "id"          => $item->id,
                            "name"        => $item->name,
                            "locations"   => $item->locations,
                            "date"        => $item->date,
                            "time"        => $item->time,
                            "status"      => $item->status
                        )
                    ),
                    "geometry" => array(
                    "type" => $item->geometries->geo_type,
                    "coordinates" => json_decode($item->geometries->coordinates)
                    )
                );
    
                $field_response["features"][] = $temp;
            }
            
            return response()->json([
                'code'      => 200,
                'status'    => 'success',
                'data'      => $field_response
            ]);
        }
        
        return view('front.landing');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSubscribeRequest $request)
    {
        $data = $request->except('_token','_method');

        $subscribe = $this->subscribeRepo->createSubscribe($data);

        $subscribe->notify(new SubscribeProcessed($subscribe));

        return response()->json([
            'code'  => 200,
            'status'=> 'success'
        ]);
    }

    /**
     * Show the form Reporting
     * 
     * @return \Illuminate\Http\Response
     */
    public function formReport(Request $request) 
    {
        return view('front.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeReport(Request $request) 
    {
        $data = $request->except('_tokne', '_method');

        $this->reportRepo->createReport($data);

        return response()->json([
            'code'          => 200,
            'status'        => 'success',
            'redirect_url'  => route('home'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $field = $this->fieldRepo->findFieldById($id);
        if($request->ajax()) {
            $field_response = array(
                "type" => "FeatureCollection",
                "features" => array()
            );
            $temp = array(
                "type" => "Feature",
                "properties" => array(
                    "color" => $field->color,
                ),
                "geometry" => array(
                    "type" => $field->geometries->geo_type,
                    "coordinates" => json_decode($field->geometries->coordinates)
                )
            );

            $field_response["features"][] = $temp;

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'data'    => $field_response
            ]);
        }
        return view('front.show', [
            'map' => $field
        ]);
    }
}
