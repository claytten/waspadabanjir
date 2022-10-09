@extends('layouts.admin.app',[
'headers' => 'active',
'menu' => 'maps',
'title' => 'Peta',
'first_title' => 'Peta',
'first_link' => route('admin.map.view', [$date_in, $date_out])
])

@section('plugins_css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/leaflet.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/easy-button.css')}}">
<link rel="stylesheet" type="text/css" rel='stylesheet'
  href="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css">
<link rel="stylesheet" type="text/css"
  href="{{ asset('vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css"
  href="{{ asset('vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}">
<!-- Load Esri Leaflet Geocoder from CDN -->
<link rel="stylesheet" type="text/css" href="{{ asset('css/esri-leaflet-geocoder.css')}}">
<link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
@endsection

@section('inline_css')
<style>
  #mapid {
    height: 750px;
    z-index: 9;
  }

  .leaflet-popup-content table tr {
    height: 30px;
  }
</style>
@endsection

@section('header-right')
<div class="col-lg-6 col-5 text-right">
  <input class="btn btn-sm btn-neutral" placeholder="Sortir Tanggal Awal" type="text" name="start_date" id="start_date"
    onchange="onChangeStartDate()">
  <input class="btn btn-sm btn-neutral" placeholder="Sortir Tanggal Akhir" type="text" name="end_date" id="end_date"
    onchange="onChangeEndDate()">
  <button type="button" class="btn btn-sm btn-neutral" onclick="mapsTables()">Data peta secara tabel</button>
</div>
@endsection

@section('content_body')
<div class="row">
  <div class="col">
    <div class="card">
      <!-- Card header -->
      <div class="card-header">
        <h3 class="mb-0">Manajemen Data Peta Tanggal Kejadian {{ ($date_in === $date_out) ? $date_in : $date_in.' Sampai
          '.$date_out }}</h3>
      </div>
      <div class="card">
        <div id="formtable">
          <div class="table-responsive py-4" id="mapsLayout">
            <table class="table table-flush" id="mapsTable">
              <thead class="thead-light">
                <tr>
                  <th>No</th>
                  <th>Lokasi</th>
                  <th>Tanggal Awal Kejadian</th>
                  <th>Tanggal Akhir Kejadian</th>
                  <th>Jumlah Korban</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>No</th>
                  <th>Lokasi</th>
                  <th>Tanggal Awal Kejadian</th>
                  <th>Tanggal Akhir Kejadian</th>
                  <th>Jumlah Korban</th>
                  <th>Status</th>
                  <th>Aksi</th>
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

{{-- Modal set Address --}}
<div class="modal fade" id="modal-add-address" tabindex="-1" role="dialog" aria-labelledby="modal-add-address"
  aria-hidden="true">
  <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">>
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card bg-secondary border-0 mb-0">
          <div class="card-body px-lg-5 py-lg-5">
            <div class="text-center text-muted mb-4">
              <small>Tambahkan Alamat</small>
            </div>
            <div class="form-group">
              <div class="input-group input-group-merge input-group-alternative">
                <label class="form-control-label" for="input-address">Kecamatan</label>
                <select onchange="searchDistrict()" id="districts" class="form-control" data-toggle="select">
                  <option value="" disabled selected>--Pilih Kecamatan--</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <div class="input-group input-group-merge input-group-alternative">
                <label class="form-control-label" for="input-address">Kelurahan</label>
                <select name="village_id" id="villages" class="form-control @error('villages') is-invalid @enderror"
                  data-toggle="select" onchange="searchVillage()">
                  <option value="" disabled selected>--Pilih Kelurahan--</option>
                </select>
              </div>
            </div>

            <div class="text-center">
              <button type="button" onclick="editAction('village')"
                class="btn btn-primary my-4 btn-submit-action">Submit</button>
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
<script type="text/javascript" src="{{ asset('vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}">
</script>
<script type="text/javascript" src="{{ asset('js/popper.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/main_gis.min.js') }}" defer></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}">
</script>
<script type="text/javascript" src="{{ asset('vendor/select2/dist/js/select2.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function() {
    $("body").tooltip({ selector: '[data-toggle=tooltip]' });
    $('#districts, #villages').select2({width: "100%"});
    $('#villages, .btn-submit-action').prop('disabled', true);
    $('#mapsLayout').hide();

    $('#districts').empty();
    $('#districts').append('<option value="" disabled selected>--Pilih Kecamatan--</option>');
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
          console.log("Terjadi Kesalahan");
        }
      }
    });
    $('#start_date').datepicker({
      disableTouchKeyboard: true,
      autoclose: false
    });
    $('#end_date').prop('disabled', true);
    $("#level .select2-container").tooltip({
      title: $('#level option:selected').attr('title')}
    );
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
  let setStartDateIn = new Date();
  let countingLoc = 1;

  const getGeoJSONData = () => {
    let data;

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('admin.maps.index', [$date_in, $date_out]) }}",
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
      <table class="pop-table">
        <tr>
          <th>Jumlah Korban</th>
          <td>: ${field.total_victims} orang</td>
        </tr>
        <tr>
          <th>Tanggal Awal Kejadian</th>
          <td>: ${field.date_in_time} WIB, ${field.date_in}</td>
        </tr>
        <tr>
          <th>Tanggal Akhir Kejadian</th>
          <td>: ${(field.date_out === false ? 'Sedang Berlangsung' : field.date_out_time + ' WIB, '+ field.date_out)}</td>
        </tr>
        <tr>
          <th>Jumlah Kelurahan yang terdampak</th>
          <td>: ${field.total_village} Kelurahan</td>
        </tr>
        <tr>
          <th>Status</th>
          <td>: ${(field.status) ? 'Terbit' : 'Draft'}</td>
        </tr>
        <tr>
          <th>Level</th>
          <td>: ${field.level.name} &nbsp;&nbsp;<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="${field.level.desc}"></i></td>
        </tr>
        <tr>
          <th>Aksi</th>
          <td>: ${buttonAction}</td>
        </tr>
      </table>
    `
  }

  const onEachFeatureCallback = (feature, layer) => {
    if (feature.properties && feature.properties.popupContent) {
      let { id, total_victims, total_village,date_in, date_in_time, date_out, date_out_time, status, level } = feature.properties.popupContent;
      let content = {id, total_victims, total_village, date_in, date_in_time, date_out, date_out_time, status, level};

      if({{ auth()->user()->can('maps-edit') }}) {
        buttonAction += '<a href="{{ route('admin.maps.edit', [':id',$date_in, $date_out]) }}" class="show btn btn-warning btn-sm" style="color: white" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';
      }
      buttonAction += '<a href="{{ route('admin.maps.show', [':id',$date_in, $date_out]) }}"class="show btn btn-info btn-sm" style="color: white" data-toggle="tooltip" data-placement="top" title="Detail"><i class="fas fa-eye"></i></a>';
      if({{ auth()->user()->can('maps-delete') }}) {
        buttonAction += `<button type="button" onclick="deleteArea(${id})" class="show btn btn-danger btn-sm" style="color: white" data-toggle="tooltip" data-placement="top" title="Hapus"><i class="fas fa-trash-alt"></i></button>`;
      }
      buttonAction = buttonAction.replaceAll(':id', id);

      var helpPolygon = layer.bindPopup(getPopupContent(content));
      buttonAction = '';

      savePolygon.push(helpPolygon);
    }
  }

  function searchDistrict() {
    $('#villages').empty();
    $('#villages').append('<option value="" disabled selected>--Pilih Kelurahan--</option>');
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
          console.log("Terjadi Kesalahan");
          }
      }
    })
  }

  function searchVillage() {
    $('.btn-submit-action').prop('disabled', false);
  }

  function editAction(input) {
    if(input === 'village') {
      if($(`#add-locations-${countingLoc-1}`).length) {
        $(`#add-locations-${countingLoc-1}`).after(`
          <div class="col-md-12" id="add-locations-${countingLoc}">
            <div class="row">
              <div class="col-md-7">
                <div class="form-group">
                  <div class="input-group input-group-merge">
                    <input type="text" id="district" class="form-control" name="locations[${countingLoc-1}][]" placeholder="Lokasi Kecamatan" value="${$('#districts option:selected').text()}" readonly>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <div class="input-group input-group-merge">
                    <input type="text" id="village" class="form-control" name="locations[${countingLoc-1}][]" placeholder="Lokasi Kelurahan" value="${$('#villages option:selected').text()}" readonly>
                  </div>
                </div>
              </div>
              <div class="col-md-1">
                <div class="form-group">
                  <button type="button" class="btn btn-danger" onclick="btnCLoseLocations('add-locations-${countingLoc}')"><i class="fas fa-window-close"></i></button>
                </div>
              </div>
            </div>
          </div>
        `);
      } else {
        $( ".btn-detail-locations" ).after(`
          <div class="col-md-12" id="add-locations-${countingLoc}">
            <div class="row">
              <div class="col-md-7">
                <div class="form-group">
                  <div class="input-group input-group-merge">
                    <input type="text" id="district" class="form-control" name="locations[${countingLoc-1}][]" placeholder="Lokasi Kecamatan" value="${$('#districts option:selected').text()}" readonly>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <div class="input-group input-group-merge">
                    <input type="text" id="village" class="form-control" name="locations[${countingLoc-1}][]" placeholder="Lokasi Kelurahan" value="${$('#villages option:selected').text()}" readonly>
                  </div>
                </div>
              </div>
              <div class="col-md-1">
                <div class="form-group">
                  <button type="button" class="btn btn-danger" onclick="btnCLoseLocations('add-locations-${countingLoc}')"><i class="fas fa-window-close"></i></button>
                </div>
              </div>
            </div>
          </div>
        `);
      }
      
      countingLoc += 1;
    }
  }

  function btnCLoseLocations(value){
    $(`#${value}`).remove();
  }

  function resetForm() {
    $('#mapsForm')[0].reset();
    ($('.images').html() !== undefined) ? ($('.images').remove(), $('.dz-preview li').remove()) : console.log('doesnt have');
    ($('#coordinates').html() !== undefined) ? $('#coordinates').remove() : console.log('doesnt have coordinates');
    ($('#color').html() !== undefined) ? $('#color').remove() : console.log('doesnt have color');
  }

  function deleteArea(id) {
    let links = '{{ route('admin.maps.destroy', [':id', $date_in, $date_out])}}';
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
          text: 'Terjadi Kesalahan!'
        });
      },
      success: function(response){
        if(response.status === 'success') {
          Swal.fire({
            position: 'middle',
            icon: response.status,
            title: response.messsage,
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
            <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Siaran</label>
            <div class="col-md-10">
              <select name="broadcast" id="broadcast" class="form-control" data-toggle="select">
                <option value=""></option>
                <option value="1">Ya</option>
                <option value="0">Tidak</option>
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
          url: "{{ route('admin.map.view', [$date_in, $date_out]) }}",
          type : "GET",
          dataType : "json",
          success:function(result) {
            if(result.status) {
              mapTable.clear().draw();
              let counting = 0;
              $.each(result.data, (key, value) => {
                let time = '';
                let status = '';
                status = (value['status'] === 1) ? 'Terbit' : 'Draft';
                mapTable.row.add([
                  counting += 1,
                  value['detail_locations'].reduce( (pv,cv,i) => {
                    return i == 0 ? cv.village.toLowerCase() + '-' + cv.district.toLowerCase() : pv + ', ' + cv.village.toLowerCase() + ',' + cv.district.toLowerCase();
                  }, ''),
                  convertDateTime(value['date_in']),
                  (value['date_out'] !== null) ? convertDateTime(value['date_out']) : 'Sedang Berlangsung',
                  value['deaths'] + value['injured'] + value['losts'] + ' Orang',
                  status,
                  addActionOption(value['id'])
                ]).draw().node().id="rows_"+value['id'];
              });
            } else {
              console.log("Terjadi Kesalahan");
            }
          }
        });
      } else {
        mapTable.clear().draw();
      }
    });
  }

  function convertDateTime(value) {
    const tmstr_date = value.split(' ');
    const d_date = tmstr_date[0].split('-');
    const t_date = tmstr_date[1].split(':');
    return `${t_date[0]}:${t_date[1]} WIB, ${d_date[2]}-${d_date[1]}-${d_date[0]}`;
  }

  function addActionOption(id) {
    let result = '';
    result += `<button type="button" onclick="showPath(${id})" class="show btn btn-info btn-sm" style="color: white">Arah</button>`;
    @if(auth()->user()->can('maps-edit')) {
      result += '<a href="{{ route('admin.maps.edit', [':id',$date_in, $date_out]) }}" class="show btn btn-warning btn-sm" style="color: white" >Edit</a>';
    }
    @endif

    @if(auth()->user()->can('maps-delete')) {
      result += `<button type="button" onclick="deleteArea(${id})" class="show btn btn-danger btn-sm" style="color: white">Delete</button>`;
    }
    @endif

    result += '<a href="{{ route('admin.maps.show', [':id',$date_in, $date_out]) }}" class="show btn btn-info btn-sm" style="color: white">Detail</a>';

    result = result.replaceAll(':id', id);

    return result;
  }

  function setDateOutField() {
    if($('#add-date-out-field').length) {
      $('#add-date-out-field').remove();
    } else {
      $('.btn-date-out-field').after(`
        <div class="col-md-12" id="add-date-out-field">
          <div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <div class="input-group input-group-merge">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                  </div>
                  <input class="form-control date_out" placeholder="Pilih tanggal kejadian berakhir" type="text" name="date_out" id="date_out">
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <div class="input-group input-group-merge">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                  </div>
                  <input class="form-control" type="time" value="00:00:00" id="example-time-input" name="date_out_time">
                </div>
              </div>
            </div>
          </div>
        </div>
      `);

      $('#date_out').datepicker({
        disableTouchKeyboard: true,
        autoclose: false,
        startDate: setStartDateIn
      });
    }
  }

  function setDateOut() {
    setStartDateIn = $('#date_in').val();
    $('#btn-set-date-out').prop('disabled', false);
    if($('#add-date-out-field').length) {
      $('#add-date-out-field').remove();
      setDateOutField();
    }
  }

  function onChangeStartDate() {
    $('#end_date').prop('disabled', false);
    if($('#end_date').length) {
      $('#end_date').remove();
      $('#start_date').after('<input class="btn btn-sm btn-neutral" placeholder="Sortir Tanggal Akhir" type="text" name="end_date" id="end_date" onchange="onChangeEndDate()">');
      $('#end_date').datepicker({
        disableTouchKeyboard: true,
        autoclose: false,
        startDate: $('#start_date').val()
      });
    }
  }

  function onChangeEndDate() {
    const start_date = $('#start_date').val().replaceAll('/', '-');
    const end_date = $('#end_date').val().replaceAll('/', '-');
    window.location.href = "{{URL::to('admin/maps/view')}}" + "/" + start_date + "/" + end_date;
  }
</script>

@endsection