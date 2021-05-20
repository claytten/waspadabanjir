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
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
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

@section('header-right')
<div class="col-lg-6 col-5 text-right">
  <button type="button" class="btn btn-sm btn-neutral" onclick="mapsTables()">Table of data Maps</button>
</div>
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
                <div id="formtable">
                  <div class="table-responsive py-4" id="mapsLayout">
                    <table class="table table-flush" id="mapsTable">
                      <thead class="thead-light">
                        <tr>
                          <th>No</th>
                          <th>Name</th>
                          <th>Locations</th>
                          <th>Datetime</th>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tfoot>
                        <tr>
                          <th>No</th>
                          <th>Name</th>
                          <th>Locations</th>
                          <th>Datetime</th>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                      </tfoot>
                      <tbody></tbody>
                    </table>
                  </div>
                </div>
                <div id="mapid"></div>
                <div class='pointer'></div>
            </div>
        </div>
    </div>
</div>

{{-- modal set district name  --}}
<div class="modal fade" id="modal-add-district" tabindex="-1" role="dialog" aria-labelledby="modal-add-district" aria-hidden="true">
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
                    <option value="" disabled selected>--Select District--</option>
                  </select>
                </div>
              </div>
              <div class="text-center">
                <button type="button" onclick="editAction('district')" class="btn btn-primary my-4">Submit</button>
              </div>
          </div>
        </div>
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
                  <label class="form-control-label" for="input-address">Village</label>
                  <select name="village_id" id="villages" class="form-control @error('villages') is-invalid @enderror" data-toggle="select" onchange="searchVillage()">
                    <option value="" disabled selected>--Select Village--</option>
                  </select>
                </div>
              </div>
              
              <div class="text-center">
                <button type="button" onclick="editAction('village')" class="btn btn-primary my-4 btn-submit-action">Submit</button>
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
<script type="text/javascript" src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function() {
    $('#districts, #villages').select2({width: "100%"});
    $('#villages, .btn-submit-action').prop('disabled', true);
    $('#mapsLayout').hide();

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

  const mapTable = $("#mapsTable").DataTable({
    lengthMenu: [ 5, 10, 25, 50, 75, 100 ],
    language: {
      "emptyTable": "Please select sort or search data"
    },
    pageLength: 5,
    columnDefs: [
      {
        target: 5,
        orderable: false,
        searchable: false
      }
    ],
    responsive: true,
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
          <th>District Name</th>
          <td>: ${field.name}</td>
        </tr>
        <tr>
          <th>Datetime</th>
          <td>: ${field.date}, ${field.time}</td>
        </tr>
        <tr>
          <th>Detail Location</th>
          <td>: ${field.locations}</td>
        </tr>
        <tr>
          <th>Status</th>
          <td>: ${(field.status) ? 'Published' : 'Draft'}</td>
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
      let { id, name,locations,date,time, status } = feature.properties.popupContent;
      time = new Date('1970-01-01T' + time + 'Z')
      .toLocaleTimeString({},
        {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
      );
      let content = {id, name, locations, date, time, status};

      if({{ auth()->user()->can('maps-edit') }}) {
        buttonAction += '<a href="{{ route('admin.maps.edit', ':id' )}}" class="show btn btn-warning btn-sm" style="color: white" >Edit</a>';
      }
      buttonAction += '<a href="{{ route('admin.maps.show', ':id' )}}" class="show btn btn-info btn-sm" style="color: white">Detail</a>';
      if({{ auth()->user()->can('maps-delete') }}) {
        buttonAction += `<button type="button" onclick="deleteArea(${id})" class="show btn btn-danger btn-sm" style="color: white">Delete</button>`;
      }
      buttonAction = buttonAction.replaceAll(':id', id);

      layer.bindPopup(getPopupContent(content));
      buttonAction = '';
    }
  }

  function searchDistrict() {
    $('#villages').empty();
    $('#villages').append('<option value="" disabled selected>--Select Village--</option>');
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
          $('#villages').prop('disabled', false);
          $('.btn-submit-action').prop('disabled', true);
          } else {
          console.log("data trouble");
          }
      }
    })
  }

  function searchVillage() {
    $('.btn-submit-action').prop('disabled', false);
  }

  function editAction(input) {
    if(input === 'district') {
      $('#area_name').val('').val($('#districts option:selected').text());
    } else {
      const address = `Kelurahan ${$('#villages option:selected').text()}`
      let result = '';
      if($('#locations').val()) {
          result = $('#locations').val() + ', ' + address;
      } else {
          result += address;
      }

      $('#locations').val(result);
    }
  }

  function resetForm() {
    $('#mapsForm')[0].reset();
    ($('.images').html() !== undefined) ? ($('.images').remove(), $('.dz-preview li').remove()) : console.log('doesnt have');
    ($('#coordinates').html() !== undefined) ? $('#coordinates').remove() : console.log('doesnt have coordinates');
    ($('#color').html() !== undefined) ? $('#color').remove() : console.log('doesnt have color');
  }

  function deleteArea(id) {
    let links = '{{ route('admin.maps.destroy', ':id')}}';
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: links.replace(':id', id),
      type: 'DELETE',
      async: false,
      cache: false,
      error: function (xhr, status, error) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Something went wrong!'
        });
      },
      success: function(response){
        if(response.status === 'success') {
          Swal.fire({
            position: 'middle',
            icon: 'success',
            title: 'Your work has been saved',
            showConfirmButton: false,
            timer: 1500
          }).then(() => window.location.href = response.redirect_url);
        } else {
          console.log(response);
        }
      }
    });
  }

  function statusAction() {
    if ($('#status').val() === '1') {
      $(`
        <div class="col-md-12" id="broadcast-form">
          <div class="form-group row">
            <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Broadcast ?</label>
            <div class="col-md-10">
              <select name="broadcast" id="broadcast" class="form-control" data-toggle="select">
                <option value=""></option>
                <option value="1">Yes</option>
                <option value="0">No</option>
              </select>
            </div>
          </div>
        </div>
      `).insertAfter('#status-form');
      $('#broadcast').select2({
        'placeholder': 'Select Broadcast Status',
      });
    } else {
      $('#broadcast-form').remove();
    }
  }

  function mapsTables() {
    $('#mapsLayout').toggle(200, () => {
      if($('#mapsLayout').is(':visible')) {
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{ route('admin.map.view')}}",
          type : "GET",
          dataType : "json",
          success:function(result) {
            if(result.status) {
              mapTable.clear().draw();
              let counting = 0;
              $.each(result.data, (key, value) => {
                let datetime = '';
                let status = '';
                const timeString12hr = new Date('1970-01-01T' + value['time'] + 'Z')
                .toLocaleTimeString({},
                  {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
                );
                datetime += value['date'] + ', ' + timeString12hr;
                status = (value['status'] === 1) ? 'Published' : 'Draft', 
                mapTable.row.add([
                  counting += 1,
                  value['name'],
                  value['locations'],
                  datetime,
                  status,
                  addActionOption(value['id'])
                ]).draw().node().id="rows_"+value['id'];
              });
            } else {
              console.log("data trouble");
            }
          }
        });
      } else {
        mapTable.clear().draw();
      }
    });
  }

  function addActionOption(id) {
    let result = '';
    @if(auth()->user()->can('maps-edit')) {
      result += '<a href="{{ route('admin.maps.edit', ':id' )}}" class="show btn btn-warning btn-sm" style="color: white" >Edit</a>';
    }
    @endif

    @if(auth()->user()->can('maps-delete')) {
      result += `<button type="button" onclick="deleteArea(${id})" class="show btn btn-danger btn-sm" style="color: white">Delete</button>`;
    }
    @endif

    result += '<a href="{{ route('admin.maps.show', ':id' )}}" class="show btn btn-info btn-sm" style="color: white">Detail</a>';

    result = result.replaceAll(':id', id);

    return result;
  }
</script>
    
@endsection