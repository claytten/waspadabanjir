@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'accounts',
  'title' => 'Admin',
  'first_title' => 'Admin',
  'first_link' => route('admin.admin.index'),
  'second_title' => 'Pembuatan'
])

@section('content_alert')
<div id="alert-section">
  @if(Session::get('message'))
    <div class="alert alert-{{ Session::get('status') }} alert-dismissible fade show alert-result" role="alert">
      <span class="alert-icon"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
      <span class="alert-text">{{ Session::get('message') }}</span>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif
</div>
@endsection

@section('plugins_css')
<link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
@endsection


@section('content_body')
<form action="{{ route('admin.admin.store') }}" method="POST" enctype="multipart/form-data" id="adminCreate">
  {{ csrf_field() }}
  <input type="hidden" name="address_id" id="village_id" readonly>
  <div class="row">
    <div class="col-lg-6">
      <div class="card-wrapper">
        <!-- Input groups -->
        <div class="card">
          <!-- Card header -->
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col-lg-6 col-md-6">
                <h3 class="mb-0">Informasi Pengguna</h3>
              </div>
              <div class="col-lg-6 col-md-6 d-flex justify-content-end">
                <a class="btn btn-info" href="{{ route('admin.admin.index') }}">Kembali</a>
                <button type="button" class="btn btn-danger" id="btn-reset">Atur Ulang</button>
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
          </div>
          <!-- Card body -->
          <div class="card-body">
              <!-- Input groups with icon -->
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="input-group input-group-merge">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                      </div>
                      <input class="form-control @error('name') is-invalid @enderror" placeholder="Nama Pengguna" type="text" name="name" value="{{ old('name') }}" id="name">
                      @error('name')
                          <div class="invalid-feedback">
                              {{ $message }}
                          </div>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="input-group input-group-merge">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                      </div>
                      <input class="form-control @error('email') is-invalid @enderror" placeholder="Email" type="email" name="email" value="{{ old('email')}}" id="email">
                      @error('email')
                          <div class="invalid-feedback">
                              {{ $message }}
                          </div>
                      @enderror
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="input-group input-group-merge">
                      <div class="input-group-prepend">
                        <span class="input-group-text">+62</span>
                      </div>
                      <input class="form-control @error('phone') is-invalid @enderror" placeholder="Nomor HP (ex. 85702142789)" type="text" name="phone" value="{{ old('phone')}}" id="phone">
                      @error('phone')
                          <div class="invalid-feedback">
                              {{ $message }}
                          </div>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="input-group input-group-merge">
                      <div class="custom-file">
                        <input type="file" accept=".jpg, .jpeg, .png" name="image" class="form-control imgs" onchange="previewImage(this)" id="projectCoverUploads" multiple>
                        <label class="custom-file-label" for="projectCoverUploads">Pilih Avatar</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="input-group input-group-merge">
                      <input class="form-control @error('password') is-invalid @enderror" placeholder="Password" type="password" name="password" id="password">
                      <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-eye"></i></span>
                      </div>
                      @error('password')
                          <div class="invalid-feedback">
                              {{ $message }}
                          </div>
                      @enderror
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="input-group input-group-merge">
                      <input class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Konfirmasi Password" type="password" name="password_confirmation" id="password_confirmation">
                      <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-eye"></i></span>
                      </div>
                      @error('password_confirmation')
                          <div class="invalid-feedback">
                              {{ $message }}
                          </div>
                      @enderror
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="input-group input-group-merge">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-map-marker"></i></span>
                      </div>
                      <button type="button" class="form-control text-left" id="btn-address" data-toggle="modal" data-target="#modal-change-address">
                        Atur Alamat Pengguna
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row images-content">
                <div class="col-md-12">
                  <div class="form-group" style="align-items: center">
                    <div class="input-group">
                      <button type="button" class="btn btn-sm btn-danger d-block mb-2 mx-auto remove_preview text-center" onclick="resetPreview(this)" disabled>Atur Ulang Tampilan Avatar</button>
                    </div>
                    <div class="input-group" style="justify-content: center">
                      <img class="img-responsive" width="200px;" style="padding:.25rem;background:#eee;display:block;">
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card-wrapper">
        <!-- Toggle buttons -->
        <div class="card">
          <!-- Card header -->
          <div class="card-header">
            <h3 class="mb-0">Terbitkan</h3>
          </div>
          <!-- Card body -->
          <div class="card-body">
            <label class="custom-toggle custom-toggle-default">
              <input type="checkbox" name="is_active">
              <span class="custom-toggle-slider rounded-circle" data-label-off="Tidak" data-label-on="Ya"></span>
            </label>
          </div>
        </div>
        {{-- Roles --}}
        <div class="card">
          <!-- Card header -->
          <div class="card-header">
            <h3 class="mb-0">Role</h3>
          </div>
          <!-- Card body -->
          <div class="card-body">
            <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" data-toggle="select">
              <option value=""></option>
              @forelse ($roles as $item)
                <option value="{{ $item }}">{{ $item }}</option>
              @empty
                <option value=""></option>
              @endforelse
            </select>
            @error('role')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

{{-- modal change address --}}
<div class="modal fade" id="modal-change-address" tabindex="-1" role="dialog" aria-labelledby="modal-add-address" aria-hidden="true">
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
                <label class="form-control-label" for="input-address">Provinsi</label>
                <select onchange="searchProvince()" id="provinces" class="form-control" data-toggle="select">
                  <option value="" disabled selected>--Pilih Provinsi--</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <div class="input-group input-group-merge input-group-alternative">
                <label class="form-control-label" for="input-address">Kabupaten/Kota</label>
                <select onchange="searchRegency()" id="regencies" class="form-control" data-toggle="select">
                  <option value="" disabled selected>--Pilih Kabupaten/Kota--</option>
                </select>
              </div>
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
                <select id="villages" class="form-control @error('villages') is-invalid @enderror" data-toggle="select" onchange="searchVillage()">
                  <option value="" disabled selected>--Pilih Kelurahan--</option>
                </select>
              </div>
            </div>
            
            <div class="text-center">
              <button type="button" onclick="addAddress()" class="btn btn-primary my-4 btn-add-address">Submit</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('plugins_js')
<script src="{{ asset('vendor/select2/dist/js/select2.min.js')}}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function() {
    $('#role').select2({
        'placeholder': 'Pilih Role',
    });
    $("#provinces, #regencies, #districts, #villages").select2({width: "100%"});
    $('#regencies, #districts, #villages, .btn-add-address').prop('disabled', true);

    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('api.provinces.index') }}",
      type : "GET",
      dataType : "json",
      success:function(result) {
        if(result) {
          $.each(result.data, (key, value) => {
            $('#provinces').append(`
              <option value="${value['id']}">${value['name']}
              </option>`);
          });
        } else {
          console.log("data trouble");
        }
      }
    });
  });

  // Add More Image
  function previewImage(input){
    let preview_image = $('.images-content').find('.img-responsive');
    let preview_button = $('.images-content').find('.remove_preview');

    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            // console.log(e.target.result);
            $(preview_image).attr('src', e.target.result);
            
        }
        $('.custom-file-label').html(input.files[0].name);
        reader.readAsDataURL(input.files[0]);
        $(preview_button).prop('disabled', false);
    }
  }

  function resetPreview(input){
    let preview_image = $(input).closest('.images-content').find('.img-responsive');
    let preview_button = $(input).closest('.images-content').find('.remove_preview');
    let preview_form = $(input).closest('.images-content').find('.imgs');

    $('.custom-file-label').html('Pilih Avatar');
    $(preview_image).attr('src', '');
    $(preview_button).prop('disabled', true);
    $(preview_form).val('');
  }
  $("#btn-reset").click(function(e){
    e.preventDefault();
    $('#adminCreate')[0].reset();
    $('#btn-address').empty().append('Atur Alamat Pengguna');
    resetPreview();
  });

  function addAddress() {
    $('#village_id').val('');
    $('#village_id').val( $('#villages').val() );
    const result = `
      ${$('#villages option:selected').text()}, 
      ${$('#districts option:selected').text()}, 
      ${$('#regencies option:selected').text()}, 
      ${$('#provinces option:selected').text()}
    `;

    $('#btn-address').empty().append(result);
  }

  function searchProvince() {
    $('#regencies, #districts, #villages').empty();
    $('#regencies').append('<option value="" disabled selected>--Pilih Kabupuaten/Kota--</option>');
    $('#districts').append('<option value="" disabled selected>--Pilih Kecamatan--</option>');
    $('#villages').append('<option value="" disabled selected>--Pilh Kelurahan--</option>');
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('api.regencies.index') }}?provinces=" + $('#provinces').val(),
      type : "GET",
      dataType : "json",
      success:function(result) {
        if(result) {
          $.each(result.data, (key, value) => {
            $('#regencies').append(`
              <option value="${value['id']}">${value['name']}
              </option>`);
          });
          $('#regencies').prop('disabled', false);
          $('#districts, #villages, .btn-add-address').prop('disabled', true);
        } else {
          console.log("data trouble");
        }
      }
    })
  }

  function searchRegency() {
    $('#districts, #villages').empty();
    $('#districts').append('<option value="" disabled selected>--Pilih Kecamatan--</option>');
    $('#villages').append('<option value="" disabled selected>--Pilh Kelurahan--</option>');
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('api.districts.index') }}?regencies=" + $('#regencies').val(),
      type : "GET",
      dataType : "json",
      success:function(result) {
        if(result) {
          $.each(result.data, (key, value) => {
            $('#districts').append('<option value="'+ value['id'] +'">'+ value['name'] +'</option>')
          });
          $('#districts').prop('disabled', false);
          $('#villages, .btn-add-address').prop('disabled', true);
        } else {
          console.log("data trouble");
        }
      }
    })
  }

  function searchDistrict() {
    $('#villages').empty();
    $('#villages').append('<option value="" disabled selected>--Pilh Kelurahan--</option>');
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('api.villages.index') }}?districts=" + $('#districts').val(),
      type : "GET",
      dataType : "json",
      success:function(result) {
        if(result) {
          $.each(result.data, (key, value) => {
            $('#villages').append('<option value="'+ value['id'] +'">'+ value['name'] +'</option>')
          });
          $('#villages').prop('disabled', false);
          $('.btn-add-address').prop('disabled', true);
        } else {
          console.log("data trouble");
        }
      }
    })
  }
  
  function searchVillage() {
    $('.btn-add-address').prop('disabled', false);
  }
</script>
    
@endsection