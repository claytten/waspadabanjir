<?php

namespace App\Http\Controllers\Admin\Maps;

use App\Models\Subscribers\Repositories\Interfaces\SubscribeRepositoryInterface;
use App\Models\Maps\Fields\Repositories\Interfaces\FieldRepositoryInterface;
use App\Models\Maps\Fields\Repositories\FieldRepository;
use App\Models\Maps\FieldImages\FieldImage;
use App\Models\Maps\Fields\Field;
use App\Models\Maps\Fields\Requests\CreateFieldRequest;
use App\Models\Maps\Fields\Requests\UpdateFieldRequest;
use App\Http\Controllers\Controller;
use App\Jobs\onSubscribeProcessing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MapController extends Controller
{
  /**
   * @var SubscribeRepositoryInterface
   */
  private $subscribeRepo;

  /**
   * @var FieldRepositoryInterface
   */
  private $fieldRepo;

  /**
   * Province Controller Constructor
   *
   * @param FieldRepositoryInterface $FieldRepository
   * @return void
   */
  public function __construct(
    FieldRepositoryInterface $fieldRepository,
    SubscribeRepositoryInterface $subscribeRepository
  ) {
    // Spatie ACL Provinces
    $this->middleware('permission:maps-list');
    $this->middleware('permission:maps-create', ['only' => ['create', 'store']]);
    $this->middleware('permission:maps-edit', ['only' => ['update']]);
    $this->middleware('permission:maps-delete', ['only' => ['destroy']]);

    // binding repository
    $this->fieldRepo = $fieldRepository;
    $this->subscribeRepo = $subscribeRepository;
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function indexView($date_in, $date_out, Request $request)
  {
    if ($request->ajax()) {
      $date_ins = Carbon::createFromFormat('m-d-Y', $date_in)->format('Y-m-d');
      $date_outs = Carbon::createFromFormat('m-d-Y', $date_out)->format('Y-m-d');
      $fields = $this->fieldRepo->listFields($date_ins, $date_outs);
      return response()->json([
        'code'      => 200,
        'status'    => 'success',
        'data'      => $fields,
      ]);
    }
    return view('admin.maps.index', [
      'date_in'   => $date_in,
      'date_out'  => $date_out
    ]);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index($date_in, $date_out)
  {
    $date_in = Carbon::createFromFormat('m-d-Y', $date_in)->format('Y-m-d');
    $date_out = Carbon::createFromFormat('m-d-Y', $date_out)->format('Y-m-d');
    $fields = $this->fieldRepo->listFields($date_in, $date_out);

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
            "status"      => $item->status,
            "level" => Field::F_LEVEL[$item->level - 1]
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
      'code'    => 200,
      'status'  => 'success',
      'data'    => $field_response
    ]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(CreateFieldRequest $request)
  {
    $data = $request->except('_token', '_method');
    $data['date_in'] = $this->fieldRepo->getDateAttribute($data['date_in'], $data['date_in_time']);
    if (!empty($data['date_out'])) {
      $data['date_out'] = $this->fieldRepo->getDateAttribute($data['date_out'], $data['date_out_time']);
    }
    // dd($data['locations'][0][0], gettype($data['locations'][0][0]));
    $field = $this->fieldRepo->createField($data);

    $fieldRepo = new FieldRepository($field);

    if ($request->hasFile('images')) {
      $fieldRepo->saveMapImages(collect($request->file('images')));
    }

    if ($request->has('broadcast') && $data['broadcast'] === "1") {
      // Broadcast Message
      $message = strval($this->fieldRepo->broadcastField($field, $data['locations'][0][0]));
      $subscribers = $this->subscribeRepo->listSubscribes()->sortBy('name');
      foreach ($subscribers->where('status', 1) as $item) {
        $item->body = strval($message);
        onSubscribeProcessing::dispatch($item);
      }
    }

    return redirect()->route('admin.map.view', [
      str_replace("/", "-", $request['date_in']),
      (!empty($request['date_out']) ? str_replace("/", "-", $request['date_out']) : str_replace("/", "-", $request['date_in']))
    ])->with([
      'status'    => 'success',
      'message'   => 'Data Banjir Berhasil Disimpan'
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id, $date_in, $date_out, Request $request)
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
    return view('admin.maps.edit', [
      'map' => $field,
      'levels' => Field::F_LEVEL,
      'date_in' => $date_in,
      'date_out' => $date_out,
    ]);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id, $date_in, $date_out, Request $request)
  {
    $field = $this->fieldRepo->findFieldById($id);
    $field->level = Field::F_LEVEL[$field->level - 1];
    return view('admin.maps.show', [
      'map' => $field,
      'date_in' => $date_in,
      'date_out' => $date_out
    ]);
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
    $data = $request->except('_token', '_method');
    $field = $this->fieldRepo->findFieldById($id);
    $fieldRepo = new FieldRepository($field);

    if ($request->ajax()) {
      $field->area = $request->area;
      $field->save();
      return response()->json([
        'code'    => 200,
        'status'  => 'success'
      ]);
    }
    $rules = [
      'description'=>['required', 'string'],
      'deaths'    => ['required', 'numeric', 'min:0'],
      'losts'     => ['required', 'numeric', 'min:0'],
      'injured'   => ['required', 'numeric', 'min:0'],
      'date_in'   => ['required', 'date'],
      'locations' => ['required', 'array'],
      'status'    => ['required'],
      'level'     => ['required']
    ];
    $customMessages = [
      'description.required'  => 'Deskripsi harus diisi',
      'description.string'    => 'Deskripsi harus berupa string',
      'level.required'        => 'Level harus diisi',
      'deaths.required'       => 'Jumlah kematian harus diisi',
      'deaths.numeric'        => 'Jumlah kematian harus berupa angka',
      'deaths.min'            => 'Jumlah kematian minimal 0',
      'losts.required'        => 'Jumlah hilang harus diisi',
      'losts.numeric'         => 'Jumlah hilang harus berupa angka',
      'losts.min'             => 'Jumlah hilang minimal 0',
      'injured.required'      => 'Jumlah pengungsi harus diisi',
      'injured.numeric'       => 'Jumlah pengungsi harus berupa angka',
      'injured.min'           => 'Jumlah pengungsi minimal 0',
      'date_in.required'      => 'Tanggal harus diisi',
      'date_in.date'          => 'Tanggal harus berupa tanggal',
      'locations.required'    => 'Lokasi harus diisi',
      'locations.array'       => 'Lokasi harus berupa array',
      'status.required'       => 'Status harus diisi'
    ];

    if(request()->hasFile('images')) {
      $rule['images.*'] = ['required', 'mimes:jpeg,png,jpg', 'max:5000'];
      $customMessages['images.*.required'] = 'Berkas foto tidak boleh kosong!';
      $customMessages['images.*.mimes'] = 'File harus berupa gambar dengan format jpeg, png, atau jpg';
      $customMessages['images.*.max'] = 'Ukuran file maksimal untuk semua berkas 5 MB';
    }

    $this->validate($request, $rules, $customMessages);

    if ($request->hasFile('images')) {
      $fieldRepo->saveMapImages(collect($request->file('images')));
    }
    $data['date_in'] = $this->fieldRepo->getDateAttribute($data['date_in'], $data['date_in_time']);
    if (!empty($data['date_out'])) {
      $data['date_out'] = $this->fieldRepo->getDateAttribute($data['date_out'], $data['date_out_time']);
    } else {
      $data['date_out'] = null;
    }
    $fieldRepo->updateField($data);
    return redirect()->route('admin.map.view', [$data['date_in_edit'], $data['date_out_edit']])->with([
      'status'    => 'success',
      'message'   => 'Data Banjir Berhasil diubah'
    ]);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id, $date_in, $date_out)
  {
    $field = $this->fieldRepo->findFieldById($id);
    $fieldRepo = new FieldRepository($field);
    if ($field->images()) {
      $fieldRepo->deleteFiles($field->images);
    }
    $fieldRepo->deleteField();

    return  response()->json([
      'code'          => 200,
      'status'        => 'success',
      'messsage'       => 'Data Banjir Berhasil Di Hapus',
      'redirect_url'  => route('admin.map.view', [$date_in, $date_out])
    ]);
  }

  /**
   * Remove Some of image Map
   * 
   * @param int $id
   * @return \Illuminate\Http\Response
   */
  public function destroyImage($id)
  {
    $mapImage = FieldImage::findOrFail($id);
    $status = '';
    $message = '';
    if (empty($mapImage)) {
      $status = 'failed';
      $message = 'Destroy Image Failed!';
    }
    $this->fieldRepo->deleteFile($mapImage->src);
    $mapImage->delete();
    $status = 'success';
    $message = 'Destroy Image Successfully!';

    return response()->json([
      'status'    => $status,
      'message'   => $message
    ]);
  }
}
