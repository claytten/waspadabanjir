@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'maps',
  'title' => 'Maps',
  'first_title' => 'Maps',
  'first_link' => route('admin.map.view'),
  'second_title' => 'Show',
  'second_link' => route('admin.maps.show', $map->id),
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
              <h3 class="mb-0" id="form-map-title">Show {{ ucwords($map->name) }} Map</h3>
            </div>
            <div class="col-lg-4 col-md-6 d-flex justify-content-end">
              <a class="btn btn-info" href="{{ route('admin.map.view') }}">Back</a>
              <a href="{{ route('admin.maps.edit', $map->id)}}">
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
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">District Name</label>
                    <div class="col-md-10">
                      <input class="form-control" name="name" type="text" value="{{ $map->name }}" id="name" disabled>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Total Deaths</label>
                    <div class="col-md-10">
                      <input class="form-control" name="deaths" type="text" value="{{ $map->deaths }}" id="deaths" disabled>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Total Injures</label>
                    <div class="col-md-10">
                      <input class="form-control" name="injured" type="text" value="{{ $map->injured }}" id="injured" disabled>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Total Losts</label>
                    <div class="col-md-10">
                      <input class="form-control" name="losts" type="text" value="{{ $map->losts }}" id="losts" disabled>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Detail Location</label>
                    <div class="col-md-10">
                      <input class="form-control" name="locations" type="text" value="{{ $map->locations }}" id="elocations" disabled>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Date</label>
                    <div class="col-md-10">
                      <input class="form-control" name="date" type="text" value="{{ $map->date }}" id="date" disabled>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Time</label>
                    <div class="col-md-10">
                      <input class="form-control" name="time" type="text" value="{{ date('h:i A', strtotime($map->time)) }}" id="time" disabled>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Description</label>
                    <div class="col-md-10">
                      <input class="form-control" name="description" type="text" value="{{ $map->description }}" id="description" disabled>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Status Publish</label>
                    <div class="col-md-10">
                      <input class="form-control" name="status" type="text" value="{{ ($map->status) ? 'Published' : 'Draft' }}" id="status" disabled>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Images</label>
                    <div class="col-md-10">
                      @forelse ($map->images as $item)
                        <a href="{{ url('/storage'.'/'.$item->src) }}" data-rel="lightcase:myCollection" >
                          <img class="img-fluid rounded" src="{{ url('/storage'.'/'.$item->src) }}" alt="{{ $map->name }}" width="150px;">
                        </a>
                      @empty
                        <input class="form-control" name="images" type="text" value="No Images" id="images" disabled>
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
  let link = '{{ route('admin.maps.edit', ':id') }}';
  link = link.replace(':id', "{{ $map->id }}");

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
      url: link,
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
  const getPopupContent = (field) => {
    return `
      <table>
        <tr>
          <th>Name Area</th>
          <td>${field.name}</td>
        </tr>
        <tr>
          <th>Locations</th>
          <td>${field.locations}</td>
        </tr>
      </table>
    `
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