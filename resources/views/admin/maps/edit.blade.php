@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'maps',
  'title' => 'Maps',
  'first_title' => 'Maps',
  'first_link' => route('admin.map.view'),
  'second_title' => 'Edit',
  'second_link' => route('admin.maps.edit', $map->id),
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
  <div class="card-wrapper">
    <div class="card">
      <div class="row">
        <div class="col-lg-12">
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col-lg-8 col-md-6">
                <h3 class="mb-0" id="form-map-title">Edit {{ ucwords($map->name) }} Map</h3>
              </div>
              <div class="col-lg-4 col-md-6 d-flex justify-content-end">
                <button type="button" class="btn btn-danger" onclick="deleteArea('{{ $map->id }}')">Delete</button>
                <button type="button" class="btn btn-warning" onclick="resetForm()" >Reset</button>
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
                          <span class="input-group-text"><i class="fas fa-chart-area"></i></span>
                        </div>
                        <input class="form-control @error('name') is-invalid @enderror" placeholder="Place Name" type="text" name="name" id="name" value="{{ $map->name }}">
                        @error('name')
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
                          <span class="input-group-text"><i class="fas fa-book-dead"></i></span>
                        </div>
                        <input type="number" id="deaths" class="form-control @error('deaths') is-invalid @enderror" name="deaths" placeholder="Total People has Dead" step="1" min="0" value="{{ $map->deaths }}">
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
                        <input type="number" id="injured" class="form-control @error('injured') is-invalid @enderror" name="injured" placeholder="Total People has Injured" step="1" min="0" value="{{ $map->injured }}">
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
                        <input type="number" id="losts" class="form-control @error('losts') is-invalid @enderror" name="losts" placeholder="Total People has Lost" step="1" min="0" value="{{ $map->losts }}">
                        @error('losts')
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
                          <span class="input-group-text"><i class="fas fa-map-marker"></i></span>
                        </div>
                        <input type="text" id="locations" class="form-control @error('locations') is-invalid @enderror" name="locations" placeholder="Locations" value="{{ $map->locations }}">
                        @error('locations')
                          <div class="invalid-feedback">
                              {{ $message }}
                          </div>
                        @enderror
                        <div class="input-group-append">
                          <span class="input-group-text" data-toggle="modal" data-target="#modal-add-address"><i class="fas fa-plus"></i></span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-8">
                        <div class="form-group">
                          <div class="input-group input-group-merge">
                            <div class="input-group-prepend">
                              <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                            </div>
                            <input class="form-control datepicker @error('date') is-invalid @enderror" placeholder="Select date" type="text" name="date" id="date" value="{{ $map->date }}">
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
                            <input class="form-control @error('time') is-invalid @enderror" type="time" value="00:00:00" id="example-time-input" name="time" value="{{ $map->time }}">
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
                  <div class="col-md-12">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fas fa-info"></i></span>
                        </div>
                        <textarea class="form-control @error('description') is-invalid @enderror" placeholder="Description" name="description" id="description">{!! $map->description !!}</textarea>
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
</form>

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
        'placeholder': 'Select District',
    });
    $('#villages').select2({
        'placeholder': 'Select Village',
    });
    $('a[data-rel^=lightcase]').lightcase();

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
  let linkEdit = '{{ route('admin.maps.edit', ':id') }}';
  let link = '{{ route('admin.maps.update', ':id') }}';
  linkEdit = linkEdit.replace(':id', "{{ $map->id }}");
  link = link.replace(':id', "{{ $map->id }}");

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
    let link = '{{ route('admin.maps.destroy', ':id') }}';
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
</script>
    
@endsection