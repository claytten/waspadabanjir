@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'maps',
  'title' => 'Peta',
  'first_title' => 'Peta',
  'first_link' => route('admin.map.view', [$date_in, $date_out]),
  'second_title' => 'Edit',
  'second_link' => route('admin.maps.edit', [$map->id, $date_in, $date_out]),
  'third_title'  => ucwords($map->name)
])

@section('plugins_css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/leaflet.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/easy-button.css')}}">
<link rel="stylesheet" type="text/css" rel='stylesheet' href="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
<!-- Load Esri Leaflet Geocoder from CDN -->
<link rel="stylesheet" type="text/css" href="{{ asset('css/esri-leaflet-geocoder.css')}}">
<link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/lightcase/css/lightcase.css')}}">
@endsection

@section('inline_css')
<style>
#mapid {
    height:750px
}
.topright {
  right:0px;
  position: absolute;
}
</style>
@endsection

@section('content_body')
<form id="mapsForm" action="{{ route('admin.maps.update', $map->id )}}" method="POST" enctype='multipart/form-data'>
  @csrf
  @method('PUT')
  <input type="hidden" name="date_in_edit" value="{{$date_in}}">
  <input type="hidden" name="date_out_edit" value="{{$date_out}}">
  <div class="card-wrapper">
    <div class="card">
      <div class="row">
        <div class="col-lg-12">
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col-lg-6 col-md-4">
                <h3 class="mb-0" id="form-map-title">Perbaharui Data Peta</h3>
              </div>
              <div class="col-lg-6 col-md-8 d-flex justify-content-end">
                <a class="btn btn-info" href="{{ route('admin.map.view', [$date_in, $date_out]) }}">Kembali</a>
                <button type="button" class="btn btn-danger" onclick="deleteArea('{{ $map->id }}')">Hapus</button>
                <button type="button" class="btn btn-warning" onclick="resetForm()" >Atur Ulang</button>
                <button type="submit" class="btn btn-primary" id="btn-submit" >Submit</button>
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
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fas fa-book-dead"></i></span>
                        </div>
                        <input type="number" id="deaths" class="form-control @error('deaths') is-invalid @enderror" name="deaths" placeholder="Jumlah korban yang meninggal" step="1" min="0" value="{{ $map->deaths }}">
                        @error('deaths')
                          <div class="invalid-feedback">
                              {{ $message }}
                          </div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fas fa-user-injured"></i></span>
                        </div>
                        <input type="number" id="injured" class="form-control @error('injured') is-invalid @enderror" name="injured" placeholder="Jumlah korban yang mengalami luka kecil/berat" step="1" min="0" value="{{ $map->injured }}">
                        @error('injured')
                          <div class="invalid-feedback">
                              {{ $message }}
                          </div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fas fa-user-slash"></i></span>
                        </div>
                        <input type="number" id="losts" class="form-control @error('losts') is-invalid @enderror" name="losts" placeholder="Jumlah korban yang hilang" step="1" min="0" value="{{ $map->losts }}">
                        @error('losts')
                          <div class="invalid-feedback">
                              {{ $message }}
                          </div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 btn-date-out-field">
                    <div class="row">
                      <div class="col-md-8">
                        <div class="form-group">
                          <div class="input-group input-group-merge">
                            <div class="input-group-prepend">
                              <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                            </div>
                            <input class="form-control" placeholder="Pilih tanggal awal kejadian" type="text" name="date_in" id="date_in" value="{{ date('m/d/Y', strtotime($map->date_in))}}"onchange="setDateOut()">
                            @error('date')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                            @enderror
                          </div>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="form-group">
                          <div class="input-group input-group-merge">
                            <div class="input-group-prepend">
                              <span class="input-group-text"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                            </div>
                            <input class="form-control" type="time" value="{{ date('H:i', strtotime($map->date_in))}}" id="example-time-input" name="date_in_time">
                            @error('time')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                            @enderror
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  @if (!empty($map->date_out))
                    <div class="col-md-12" id="add-date-out-field">
                      <div class="row">
                        <div class="col-md-8">
                          <div class="form-group">
                            <div class="input-group input-group-merge">
                              <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                              </div>
                              <input class="form-control date_out" placeholder="Pilih tanggal kejadian berakhir" type="text" name="date_out" id="date_out" value="{{ date('m/d/Y', strtotime($map->date_out))}}">
                            </div>
                          </div>
                        </div>
            
                        <div class="col-md-4">
                          <div class="form-group">
                            <div class="input-group input-group-merge">
                              <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                              </div>
                              <input class="form-control" type="time" value="{{ date('H:i', strtotime($map->date_out))}}" id="example-time-input" name="date_out_time">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endif
                  <div class="col-md-12">
                    <div class="form-group">
                      <button type="button" class="btn btn-info" data-toggle="tooltip" data-placement="top" title="Button ini berfungsi mengaktifkan tanggal berakhir banjir atau tidak" onclick="setDateOutField()" id="btn-set-date-out">Atur tanggal berakhirnya banjir</button>
                    </div>
                  </div>
                  @foreach ($map->detailLocations as $indexLocation => $locations)
                    <div class="col-md-12" id="add-locations-{{ $indexLocation }}">
                      <div class="row">
                        <div class="col-md-7">
                          <div class="form-group">
                            <div class="input-group input-group-merge">
                              <input type="text" id="district" class="form-control" name="locations[{{ $indexLocation }}][]" placeholder="Lokasi Kecamatan" value="{{ $locations->district }}" readonly>
                            </div>
                          </div>
                        </div>
          
                        <div class="col-md-4">
                          <div class="form-group">
                            <div class="input-group input-group-merge">
                              <input type="text" id="village" class="form-control" name="locations[{{ $indexLocation }}][]" placeholder="Lokasi Kelurahan" value="{{ $locations->village }}" readonly>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-1">
                          <div class="form-group">
                            <button type="button" class="btn btn-danger" onclick="btnCLoseLocations('add-locations-{{ $indexLocation }}')"><i class="fas fa-window-close"></i></button>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                  <div class="col-md-12 btn-detail-locations">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-add-address">Tambahkan Rincian Lokasi</button>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fas fa-info"></i></span>
                        </div>
                        <textarea class="form-control @error('description') is-invalid @enderror" placeholder="Kronologi" name="description" id="description">{!! $map->description !!}</textarea>
                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="input-group">
                      @foreach ($map->images as $item)
                        <div style="position:relative;padding-right: 20px; padding-bottom: 20px" id="mapImage-{{$item->id}}">
                          <button type="button" class="close topRight" aria-label="Close" onclick="deleteImage('{{ $item->id }}')">
                            <i class="fas fa-times"></i></span>
                          </button>
                          <a href="{{ url('/storage'.'/'.$item->src) }}" data-rel="lightcase:myCollection">
                            <img class="img-fluid rounded" src="{{ url('/storage'.'/'.$item->src) }}" alt="{{ $map->name }}" width="150px;">
                          </a>
                        </div>
                      @endforeach
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <div class="dropzone dropzone-multiple" data-toggle="dropzone" data-dropzone-multiple data-dropzone-url="#">
                        <div class="fallback">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="customFileUploadMultiple" multiple>
                            <label class="custom-file-label" for="customFileUploadMultiple">Choose file</label>
                          </div>
                        </div>
                        <ul class="dz-preview dz-preview-multiple list-group list-group-lg list-group-flush">
                          <li class="list-group-item px-0">
                            <div class="row align-items-center">
                              <div class="col-auto">
                                <div class="avatar">
                                  <img class="avatar-img rounded" src="..." alt="..." data-dz-thumbnail>
                                </div>
                              </div>
                              <div class="col ml--3">
                                <h4 class="mb-1" data-dz-name>...</h4>
                                <p class="small text-muted mb-0" data-dz-size>...</p>
                              </div>
                              <div class="col-auto">
                                <div class="dropdown">
                                  <a href="#" class="dropdown-ellipses dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fe fe-more-vertical"></i>
                                  </a>
                                  <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item" data-dz-remove>
                                      Remove
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12" id="status-form">
                    <div class="form-group row">
                      <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Status Penerbitan</label>
                      <div class="col-md-10">
                        <select name="status" id="status" class="form-control" data-toggle="select" onchange="statusAction()">
                          <option value=""></option>
                          <option value="1" {{ ($map->status == 1) ? 'selected' : '' }}>Terbitkan</option>
                          <option value="0" {{ ($map->status == 0) ? 'selected' : '' }}>Draft</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  @if ($map->status)
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
                  @endif
                  <div class="col-md-12">
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
    </div>
  </div>
</form>

{{-- Modal set Address --}}
<div class="modal fade" id="modal-add-address" tabindex="-1" role="dialog" aria-labelledby="modal-add-address" aria-hidden="true">
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
                  <select name="village_id" id="villages" class="form-control @error('villages') is-invalid @enderror" data-toggle="select" onchange="searchVillage()">
                    <option value="" disabled selected>--Pilih Kelurahan--</option>
                  </select>
                </div>
              </div>
              
              <div class="text-center">
                <button type="button" onclick="editAction()" class="btn btn-primary my-4 btn-submit-action">Submit</button>
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
<script type="text/javascript" src="{{ asset('js/edit_gis.js') }}" defer></script>
<script type="text/javascript" src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/select2/dist/js/select2.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('vendor/lightcase/js/lightcase.js')}}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function() {
    $('#districts').select2({
        'placeholder': 'Pilih Kecamatan',
    });
    $('#villages').select2({
        'placeholder': 'Pilih Kelurahan',
    });
    $('#status').select2({
      'placeholder': 'Pilih Status',
    });
    $('#broadcast').select2({
      'placeholder': '--Pilih Status Siaran--',
    });
    $('a[data-rel^=lightcase]').lightcase();

    $('#villages, .btn-submit-action').prop('disabled', true);
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
              console.log("Terjadi Kesalahan");
          }
      }
    });
    $('#date_in').datepicker({
      disableTouchKeyboard: true,
      autoclose: false
    });
    if($('#date_in').val() === '') {
      $('#btn-set-date-out').prop('disabled', true).tooltip();
    }
  });

  // this singleton variable use in main.gis.js at formable action
  let linkEdit = '{{ route('admin.maps.edit', [$map->id, $date_in, $date_out]) }}';
  let link = '{{ route('admin.maps.update', $map->id) }}';
  let setStartDateIn = $('#date_in').val();
  let countingLoc = 1;

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
    countingLoc = $('[id^=add-locations-]').length;
    if($('[id^=add-locations-]').length) {
      $(`#add-locations-${countingLoc-1}`).after(`
        <div class="col-md-12" id="add-locations-${countingLoc}">
          <div class="row">
            <div class="col-md-7">
              <div class="form-group">
                <div class="input-group input-group-merge">
                  <input type="text" id="district" class="form-control" name="locations[${countingLoc}][]" placeholder="Lokasi Kecamatan" value="${$('#districts option:selected').text()}" readonly>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <div class="input-group input-group-merge">
                  <input type="text" id="village" class="form-control" name="locations[${countingLoc}][]" placeholder="Lokasi Kelurahan" value="${$('#villages option:selected').text()}" readonly>
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

  function deleteImage(id) {
    let link = '{{ route('admin.maps.image.destroy', ':id') }}';
    link = link.replace(':id', id);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: link,
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
        console.log('this is response', response);
        if(response.status === 'success') {
          $(`#mapImage-${id}`).remove();
        } else {
          console.log(response);
        }
      }
    });
  }

  function resetForm() {
    $('#mapsForm')[0].reset();
    ($('.images').html() !== undefined) ? ($('.images').remove(), $('.dz-preview li').remove()) : console.log('doesnt have');
    ($('#coordinates').html() !== undefined) ? $('#coordinates').remove() : console.log('doesnt have coordinates');
    ($('#color').html() !== undefined) ? $('#color').remove() : console.log('doesnt have color');
  }

  function deleteArea(id) {
    let link = '{{ route('admin.maps.destroy', [':id', $date_in, $date_out])}}';
    link = link.replace(':id', id);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: link,
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

  function btnCLoseLocations(value){
    $(`#${value}`).remove();
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