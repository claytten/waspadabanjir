@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'maps',
  'title' => 'Maps',
  'first_title' => 'Maps',
  'first_link' => route('admin.map.view')
])

@section('plugins_css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/leaflet.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/easy-button.css')}}">
<link rel="stylesheet" type="text/css" rel='stylesheet' href="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}">
<!-- Load Esri Leaflet Geocoder from CDN -->
<link rel="stylesheet" type="text/css" href="{{ asset('css/esri-leaflet-geocoder.css')}}">
<link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
@endsection

@section('inline_css')
<style>
#mapid {
    height:750px
}
.leaflet-popup-content table tr {
  height: 30px;
}
</style>
@endsection

@section('content_body')
<div class="row">
    <div class="col">
        <div class="card">
            <!-- Card header -->
            <div class="card-header">
                <h3 class="mb-0">Maps Management</h3>
            </div>
            <div class="card">
                <div id="formtable"></div>
                <div id="mapid"></div>
                <div class='pointer'></div>
            </div>
        </div>
    </div>
</div>

{{-- Modal set Address --}}
<div class="modal fade" id="modal-add-address" tabindex="-1" role="dialog" aria-labelledby="modal-add-address" aria-hidden="true">
    <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">>
      <div class="modal-content">
        <div class="modal-body p-0">
          <div class="card bg-secondary border-0 mb-0">
            <div class="card-body px-lg-5 py-lg-5">
                <div class="text-center text-muted mb-4">
                    <small>Add Address</small>
                </div>
                <div class="form-group">
                  <div class="input-group input-group-merge input-group-alternative">
                    <label class="form-control-label" for="input-address">District</label>
                    <select onchange="searchDistrict()" id="districts" class="form-control" data-toggle="select">
                      <option value=""></option>
                    </select>
                  </div>
                </div>
  
                <div class="form-group">
                  <div class="input-group input-group-merge input-group-alternative">
                    <label class="form-control-label" for="input-address">Village</label>
                    <select name="village_id" id="villages" class="form-control @error('villages') is-invalid @enderror" data-toggle="select">
                      <option value=""></option>
                    </select>
                  </div>
                </div>
                
                <div class="text-center">
                  <button type="button" onclick="editAction()" class="btn btn-primary my-4">Submit</button>
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
<script type="text/javascript" src="{{ asset('vendor/dropzone/dist/min/dropzone.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/main_gis.js') }}" defer></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/select2/dist/js/select2.min.js')}}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function() {
    $('#districts').select2({
        'placeholder': 'Select District',
    });
    $('#villages').select2({
        'placeholder': 'Select Village',
    });

    $('#districts').empty();
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('admin.districts.index') }}?regencies=" + '3310',
      type : "GET",
      dataType : "json",
      success:function(result) {
          if(result) {
          $.each(result.data, (key, value) => {
              $('#districts').append('<option value="'+ value['id'] +'">'+ value['name'] +'</option>')
          });
          } else {
              console.log("data trouble");
          }
      }
    });
  });

  // this singleton variable use in main.gis.js at formable action
  const urlPOST = "{{ route('admin.maps.store') }}";
  let buttonAction = '';

  const getGeoJSONData = () => {
    let data;

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: `${url}admin/maps`,
      type: 'GET',
      async: false,
      cache: false,
      error: function (xhr, status, error) {
        console.log(xhr.responseText);
      },
      success: function(response){
        data = response.data;
      }
    });

    return data;
  }

  const getPopupContent = (field) => {
    return `
      <table>
        <tr>
          <th>Name</th>
          <td>: ${field.name}</td>
        </tr>
        <tr>
          <th>Datetime</th>
          <td>: ${field.date}, ${field.time}</td>
        </tr>
        <tr>
          <th>Locations</th>
          <td>: ${field.locations}</td>
        </tr>
        <tr>
          <th>Actions</th>
          <td>: ${buttonAction}</td>
        </tr>
      </table>
    `
  }

  const onEachFeatureCallback = (feature, layer) => {
    if (feature.properties && feature.properties.popupContent) {
      let { id, name,locations,description,deaths, losts,injured,date,time } = feature.properties.popupContent;
      let content = {id, name, locations, deaths, description, losts, injured, date, time};

      if({{ auth()->user()->can('maps-edit') }}) {
        buttonAction += '<a href="{{ route('admin.maps.edit', ':id' )}}" class="show btn btn-warning btn-sm" style="color: white" >Edit</a>';
      }
      buttonAction += '<a href="{{ route('admin.maps.show', ':id' )}}" class="show btn btn-info btn-sm" style="color: white">Detail</a>';
      if({{ auth()->user()->can('maps-delete') }}) {
        buttonAction += '<a href="{{ route('admin.maps.edit', ':id' )}}" class="show btn btn-danger btn-sm" style="color: white">Delete</a>';
      }
      buttonAction = buttonAction.replaceAll(':id', id);

      layer.bindPopup(getPopupContent(content));
      buttonAction = '';
    }
  }

  function searchDistrict() {
    $('#villages').empty();
    $.ajax({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: "{{ route('admin.villages.index') }}?districts=" + $('#districts').val(),
    type : "GET",
    dataType : "json",
    success:function(result) {
        if(result) {
        $.each(result.data, (key, value) => {
            $('#villages').append('<option value="'+ value['id'] +'">'+ value['name'] +'</option>')
        });
        } else {
        console.log("data trouble");
        }
    }
    })
  }

  function editAction() {
    const address = `Kec.${$('#villages option:selected').text()}-Kel.${$('#districts option:selected').text()}`
    let result = '';
    if($('#locations').val()) {
        result = $('#locations').val() + ', ' + address;
    } else {
        result += address;
    }

    $('#locations').val(result);
  }

  function resetForm() {
    $('#mapsForm')[0].reset();
    ($('.images').html() !== undefined) ? ($('.images').remove(), $('.dz-preview li').remove()) : console.log('doesnt have');
    ($('#coordinates').html() !== undefined) ? $('#coordinates').remove() : console.log('doesnt have coordinates');
    ($('#color').html() !== undefined) ? $('#color').remove() : console.log('doesnt have color');
  }
</script>
    
@endsection