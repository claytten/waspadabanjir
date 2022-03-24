@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'maps',
  'title' => 'Peta',
  'first_title' => 'Peta',
  'first_link' => route('admin.map.view', [$date_in, $date_out]),
  'second_title' => 'Rincian',
  'second_link' => route('admin.maps.show', [$map->id, $date_in, $date_out]),
  'third_title'  => ucwords($map->name)
])

@section('plugins_css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/leaflet.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/easy-button.css')}}">
<link rel="stylesheet" type="text/css" rel='stylesheet' href="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css">
<!-- Load Esri Leaflet Geocoder from CDN -->
<link rel="stylesheet" type="text/css" href="{{ asset('css/esri-leaflet-geocoder.css')}}">
<link rel="stylesheet" href="{{ asset('vendor/lightcase/css/lightcase.css')}}">
@endsection

@section('inline_css')
<style>
  #mapid {
      height:750px
  }
</style>
@endsection

@section('content_body')
<div class="card-wrapper">
  <div class="card">
    <div class="row">
      <div class="col-lg-12">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col-lg-8 col-md-6">
              <h3 class="mb-0" id="form-map-title">Rincian Data Peta</h3>
            </div>
            <div class="col-lg-4 col-md-6 d-flex justify-content-end">
              <a class="btn btn-info" href="{{ route('admin.map.view', [$date_in, $date_out]) }}">Kembali</a>
              <a href="{{ route('admin.maps.edit', [$map->id, $date_in, $date_out])}}">
                <button type="button" class="btn btn-warning">Edit</button>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-12">
        <div class="row">
          <div class="col-lg-6">
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Jumlah korban meninggal/luka/hilang</label>
                    <div class="col-md-10">
                      <span class="form-control">{{ $map->deaths }}/{{ $map->injured }}/{{ $map->losts }} orang</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Lokasi yang terdampak</label>
                    <div class="col-md-10">
                      <span>
                        @foreach($map->detailLocations->sortBy('district') as $indexLoc => $dataLoc)
                          {{$indexLoc+1}}. Kelurahan {{ $dataLoc->village}} - Kecamatan {{ $dataLoc->district }} <br>
                        @endforeach
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Tanggal Awal Kejadian</label>
                    <div class="col-md-10">
                      <span class="form-control">{{ date('H:i', strtotime($map->date_in)) }} WIB, {{ date('d-m-Y', strtotime($map->date_in)) }}</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Tanggal Akhir Kejadian</label>
                    <div class="col-md-10">
                      <span class="form-control">{{ $map->date_out !== null ? date('H:i', strtotime($map->date_in)).' WIB, '.date('d-m-Y', strtotime($map->date_in)) : 'Sedang Berlangsung' }}</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Kronologi</label>
                    <div class="col-md-10">
                      <span class="form-control">{{ $map->description }}</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Status Diterbitkan</label>
                    <div class="col-md-10">
                      <span class="form-control">{{ ($map->status) ? 'Terbit' : 'Draft' }}</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Kumpulan Gambar Kejadian</label>
                    <div class="col-md-10">
                      @forelse ($map->images as $item)
                        <a href="{{ url('/storage'.'/'.$item->src) }}" data-rel="lightcase:myCollection" >
                          <img class="img-fluid rounded" src="{{ url('/storage'.'/'.$item->src) }}" alt="{{ $map->name }}" width="150px;">
                        </a>
                      @empty
                        <span class="form-control">Tidak ada gambar</span>
                      @endforelse
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card-body">
              <div class="form-group">
                <div id="mapid"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('plugins_js')
<script type="text/javascript" src="{{ asset('js/leaflet.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/leaflet_fullscreen.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/easy-button.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/esri-leaflet.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/esri-leaflet-geocoder.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/lightcase/js/lightcase.js')}}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function() {
    $('a[data-rel^=lightcase]').lightcase();
  });

  let polygon = undefined;

  let maps = L.map(
    'mapid', 
    {
      fullscreenControl: true,
      center: new L.LatLng(-7.694512268978755, 110.67233986914458),
      zoom: 12
    }
  );

  L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',{
      maxZoom: 60,
      subdomains:['mt0','mt1','mt2','mt3']
  }).addTo(maps);

  // DOM & AJAX
  const getGeoJSONData = () => {
    let data;

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('admin.maps.edit', [$map->id, $date_in, $date_out]) }}",
      type: 'GET',
      async: false,
      cache: false,
      error: function (xhr, status, error) {
        console.log(xhr.responseText);
      },
      success: function(response){
        data = response.data;
        console.log('this is response', response.data);
      }
    });

    return data;
  }

  const onEachFeatureCallback = (feature, layer) => {
    if (feature.properties && feature.properties.popupContent) {
        polygon = L.polygon([feature.geometry.coordinates], {
            color: feature.properties.color,
            fillOpacity: 0.4
        });
        
        const setCenter = polygon.getBounds().getCenter();
        maps.panTo(new L.LatLng(setCenter.lng, setCenter.lat));
    }
  }

  L.geoJSON(getGeoJSONData(), {
    style: function(feature){
      return {color: feature.properties.color}
    },
    onEachFeature: onEachFeatureCallback
  }).addTo(maps);
</script>
    
@endsection