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
    public function indexView(Request $request)
    {
      if ($request->ajax()) {
          $fields = $this->fieldRepo->listFields()->sortBy('name');
          return response()->json([
              'code'      => 200,
              'status'    => 'success',
              'data'      => $fields
          ]);
      }
      return view('admin.maps.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fields = $this->fieldRepo->listFields()->sortBy('name');

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
        $data = $request->except('_token','_method');
        $field = $this->fieldRepo->createField($data);

        $fieldRepo = new FieldRepository($field);

        if ($request->hasFile('images')) {
          $fieldRepo->saveMapImages(collect($request->file('images')));
        }

        if ($request->has('broadcast') && $data['broadcast'] === "1") {
          // Broadcast Message
          $detailLink = route('maps.show', $field['id']);
          $detailLink = str_replace('http','https',$detailLink);
          $message = "--Update Data Terbaru--\nTelah terjadi banjir di daerah Kecamatan {$field->name}:";
          $message .= "\n  -Waktu & Tgl Kejadian : {$field->time}, {$field->date}";
          $message .= "\n  -Detail Lokasi : {$field->locations}";
          $message .= "\n  -Deskripsi : {$field->description}";
          $message .= "\n  -Detail informasi peta dan gambar: {$detailLink} ";

          $subscribers = $this->subscribeRepo->listSubscribes()->sortBy('name');
          foreach($subscribers->where('status', 1) as $item) {
            $item->body = strval($message);
            onSubscribeProcessing::dispatch($item);
          }
        }

        return redirect()->route('admin.map.view')->with([
            'status'    => 'success',
            'message'   => 'Create Map successful!'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
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
        return view('admin.maps.edit', [
            'map' => $field
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
        $field = $this->fieldRepo->findFieldById($id);
        return view('admin.maps.show', [
            'map' => $field
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
        $data = $request->except('_token','_method');
        $field = $this->fieldRepo->findFieldById($id);
        $fieldRepo = new FieldRepository($field);

        if ($request->ajax()) {
            $field->geometries()->update([
                'coordinates' => $request->coordinates,
            ]);
            $fieldRepo->updateField($data);
            return response()->json([
                'code'    => 200,
                'status'  => 'success'
            ]);
        }
        $rules = [
            'name'      => ['required', 'string'],
            'locations' => ['required', 'string'],
            'description'=>['required', 'string'],
            'deaths'    => ['required', 'numeric', 'min:0'],
            'losts'     => ['required', 'numeric', 'min:0'],
            'injured'   => ['required', 'numeric', 'min:0'],
            'date'      => ['required', 'string'],
            'time'      => ['required', 'string'],
            'status'    => ['required']
        ];
        $customMessages = [
            'required' => 'The :attribute field is required.'
        ];
    
        $this->validate($request, $rules, $customMessages);

        if ($request->hasFile('images')) {
            $fieldRepo->saveMapImages(collect($request->file('images')));
        }
        $fieldRepo->updateField($data);
        return redirect()->route('admin.map.view')->with([
            'status'    => 'success',
            'message'   => 'Update Map successful!'
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
        $field = $this->fieldRepo->findFieldById($id);
        $fieldRepo = new FieldRepository($field);
        if($field->images()) {
            $fieldRepo->deleteFiles($field->images);
        }
        $fieldRepo->deleteField();

        return  response()->json([
            'code'          => 200,
            'status'        => 'success',
            'redirect_url'  => route('admin.map.view')
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
        if(empty($mapImage)) {
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
