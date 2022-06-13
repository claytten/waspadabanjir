@extends('layouts.front.app')

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
body { margin: 0; }
html,
body,
.form-box {
  height: auto;
}
body {
  background-color: #C0E3C2;
}

#mapid {
  height:750px
}
.leaflet-popup-content table tr {
  height: 30px;
}
.leaflet-bar {
  background-color: #fff !important;
  border: none !important;
  border-bottom: 1px solid #ccc !important;
  line-height: 26px;
  display: block;
  color: black;
}

@media only screen and (min-width: 993px) {
  .container-modal {
    width: 25% !important;
  }
}

@media only screen and (max-width : 601px) {
  .form-box {
    height: auto;
  }
}
</style>
@endsection

@section('content_body')
<div class="container form-box valign-wrapper">
  <div class="section valign" style="width:100%">    
    <div id="contact-page" class="card hoverable">
        <div class="card-content">
            <div class="row">
              <div class="col s12 m6">
                <div class="row">
                  <div class="col s6">
                    <h5>Detail Informasi Banjir</h5>
                  </div>
                  <div class="col s6 right-align">
                    <a href="{{ route('home') }}" class="btn waves-effect waves-light orange">Home
                      <i class="material-icons right">home</i>
                    </a>
                  </div>
                </div>
                <div class="row">
                  <div class="input-field col s6">
                    <h6>Tanggal Awal Kejadian</h6>
                  </div>
                  <div class="input-field col s6">
                    <h6>: {{ date('H:i', strtotime($map->date_in)) }} WIB, {{ date('d-m-Y', strtotime($map->date_in)) }}</h6>
                  </div>
                </div>
                <div class="row">
                  <div class="input-field col s6">
                    <h6>Tanggal Akhir Kejadian</h6>
                  </div>
                  <div class="input-field col s6">
                    <h6>: {{ $map->date_out !== null ? date('H:i', strtotime($map->date_in)).' WIB, '.date('d-m-Y', strtotime($map->date_in)) : 'Sedang Berlangsung' }}</h6>
                  </div>
                </div>
                <div class="row">
                  <div class="input-field col s6">
                    <h6>Jumlah korban yang meninggal</h6>
                  </div>
                  <div class="input-field col s6">
                    <h6>: {{ $map->deaths }}</h6>
                  </div>
                </div>
                <div class="row">
                  <div class="input-field col s6">
                    <h6>Jumlah korban yang mengalami luka kecil/berat</h6>
                  </div>
                  <div class="input-field col s6">
                    <h6>: {{ $map->injured }}</h6>
                  </div>
                </div>
                <div class="row">
                  <div class="input-field col s6">
                    <h6>Jumlah korban yang hilang</h6>
                  </div>
                  <div class="input-field col s6">
                    <h6>: {{ $map->losts }}</h6>
                  </div>
                </div>
                <div class="row">
                  <div class="input-field col s6">
                    <h6>Lokasi Kelurahan yang terdampak banjir</h6>
                  </div>
                  <div class="input-field col s6">
                    @php
                      $counting = 1;
                    @endphp
                    @foreach($map->detailLocations->sortBy('district') as $indexLoc => $dataLoc)
                      @if ($counting == 1)
                        <h6>: {{ $counting }}. Kelurahan {{ $dataLoc->village}} - Kecamatan {{ $dataLoc->district }}</h6>
                      @else
                        <h6>{{ $counting }}. Kelurahan {{ $dataLoc->village}} - Kecamatan {{ $dataLoc->district }}</h6>
                      @endif
                    
                      @php
                        $counting++;
                      @endphp
                    @endforeach
                  </div>
                </div>
                <div class="row">
                  <div class="input-field col s6">
                    <h6>Kronologi Kejadian</h6>
                  </div>
                  <div class="input-field col s6">
                    <h6>: {{ $map->description }}</h6>
                  </div>
                </div>
                <div class="row">
                  @if(count($map->images) > 0)
                    <div class="input-field col s12 center">
                      <h6>Kumpulan Gambar Kejadian<</h6>
                    </div>
                  @else
                    <div class="input-field col s6">
                      <h6>Kumpulan Gambar Kejadian<</h6>
                    </div>
                    <div class="input-field col s6">
                      <h6>: Tidak ada gambar</h6>
                    </div>
                  @endif
                </div>
                @if($map->images)
                  <div class="row">
                    <div class="input-field col s12">
                      @foreach ($map->images as $item)
                        <a href="{{ url('/storage'.'/'.$item->src) }}" data-rel="lightcase:myCollection" >
                          <img class="img-fluid rounded" src="{{ url('/storage'.'/'.$item->src) }}" alt="{{ $map->name }}" width="150px;">
                        </a>
                      @endforeach
                    </div>
                  </div>
                @endif
              </div>                      
              <div class="col s12 m6">
                <div id="mapid"></div>
                <div class='pointer'></div>
              </div>
            </div>
            <footer style="text-align: center">
              <span>Copyright © 2021 <a class="blue-text text-darken-2" href="{{ route('home') }}" target="_blank">{{ (!empty(config('app.name')) ? config('app.name') : 'Laravel') }}</a> All rights reserved.</span>
            </footer>
        </div>
    </div>            
  </div>
</div>
@endsection

@section('plugins_js')
<script type="text/javascript" src="{{ asset('js/leaflet.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/leaflet_fullscreen.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/esri-leaflet.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/esri-leaflet-geocoder.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/easy-button.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/lightcase/js/lightcase.js')}}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function() {
    $('a[data-rel^=lightcase]').lightcase();
  });

  let polygon = undefined;
  let link = '{{ route('maps.show', ':id') }}';
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

  var geoJsonLayer = L.geoJSON(getGeoJSONData(), {
    style: function(feature){
      return {color: feature.properties.color}
    },
    onEachFeature: onEachFeatureCallback
  }).addTo(maps);

  maps.fitBounds(geoJsonLayer.getBounds());
</script>
@endsection