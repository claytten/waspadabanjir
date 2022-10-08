<?php

namespace App\Http\Controllers\Front;

use App\Models\Maps\Fields\Repositories\Interfaces\FieldRepositoryInterface;
use App\Models\Subscribers\Repositories\Interfaces\SubscribeRepositoryInterface;
use App\Models\Reports\Repositories\Interfaces\ReportRepositoryInterface;
use App\Models\Subscribers\Requests\CreateSubscribeRequest;
use App\Http\Controllers\Controller;
use App\Jobs\onCompleteSubscribe;
use App\Models\Maps\Fields\Field;
use App\Models\Reports\Repositories\ReportRepository;
use App\Models\Reports\Requests\CreateReportRequest;
use App\Models\Tools\PhoneFilterTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LandingController extends Controller
{
  use PhoneFilterTrait;
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
      $date_in = Carbon::now()->toDateString();
      $date_out = Carbon::now()->toDateString();
      $fields = $this->fieldRepo->listFieldsPublic($date_in, $date_out);

      $field_response = array(
        "type" => "FeatureCollection",
        "features" => array()
      );
      foreach ($fields as $item) {
        $temp = array(
          "type" => "Feature",
          "properties" => array(
            "popupContent" => array(
              "id"          => $item->id,
              "total_victims" => ($item->deaths + $item->injured + $item->losts),
              "total_village" => $item->detailLocations()->count(),
              "date_in"     => $this->fieldRepo->convertDateAttribute($item->date_in),
              "date_in_time" => $this->fieldRepo->convertTimeAttribute($item->date_in),
              "date_out"    => ($item->date_out !== null ? $this->fieldRepo->convertDateAttribute($item->date_out) : false),
              "date_out_time" => ($item->date_out !== null ? $this->fieldRepo->convertTimeAttribute($item->date_out) : false),
              "address"     => $item->detailLocations()->get(),
              "status"      => $item->status,
              "level"       => Field::F_LEVEL[$item->level - 1],
              "url"         => route('maps.show', $item->id)
            )
          ),
          "geometry" => array(
            "type" => 'Polygon',
            "coordinates" => json_decode($item->area)
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
    $data = $request->except('_token', '_method');

    $subscribe = $this->subscribeRepo->createSubscribe($data);

    onCompleteSubscribe::dispatch($subscribe);

    return response()->json([
      'code'  => 200,
      'status' => 'success'
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
  public function storeReport(CreateReportRequest $request)
  {
    if ($request->report_type == 'report') {
      $statusPhone = $this->checkingPhone($request->phone);
      if ($statusPhone == false) {
        return response()->json([
          'code'  => 200,
          'status' => 'error',
          'message' => 'Nomor telepon tidak valid'
        ]);
      }
    }
    $report = $this->reportRepo->createReport($request->validated());

    $reportRepo = new ReportRepository($report);

    if ($request->hasFile('images')) {
      $reportRepo->saveMapImages(collect($request->file('images')));
    }

    return response()->json([
      'code'          => 200,
      'status'        => 'success',
      'message'       => 'Laporan Anda Telah Terkirim',
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
    if ($request->ajax()) {
      $field_response = array(
        "type" => "FeatureCollection",
        "features" => array()
      );
      $temp = array(
        "type" => "Feature",
        "properties" => array(
          "level" => Field::F_LEVEL[$field->level - 1],
        ),
        "geometry" => array(
          "type" => 'Polygon',
          "coordinates" => json_decode($field->area)
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
