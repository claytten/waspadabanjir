@extends('layouts.admin.app',[
  'headers' => 'non-active',
  'menu' => 'Profile',
])

@section('headers')
<div class="header pb-6 d-flex align-items-center" style="min-height: 500px; background-image: url({{ asset('images/default/img-1-1000x600.jpg')}}); background-size: cover; background-position: center top;">
  <!-- Mask -->
  <span class="mask bg-gradient-default opacity-8"></span>
  <!-- Header container -->
  <div class="container-fluid d-flex align-items-center">
    <div class="row col-lg-7">
      <div class="col-lg-7 col-md-10">
        <h1 class="display-2 text-white">Hello {{ $user->name }}</h1>
      </div>
    </div>
  </div>
</div>
@endsection

@section('plugins_css')
<link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
@endsection

@section('content_body')
<div class="row">
  <div class="col-xl-4 order-xl-2 images-content">
    <div class="card card-profile">
      <img src="{{ asset('images/default/img-1-1000x600.jpg') }}" alt="Image placeholder" class="card-img-top">
      <div class="row justify-content-center">
        <div class="col-lg-3 order-lg-2">
          <div class="card-profile-image">
            <img src="{{ 
                  !empty($user->image)
                      ? url('/storage'.'/'.$user->image)
                          : asset('images/default/team-4.jpg')
              }}" alt="User Avatar" class="rounded-circle">
          </div>
        </div>
      </div>
      <br>
      <div class="text-center">
        <br>
        <div class="h5 mt-4">
          <i class="ni business_briefcase-24 mr-2"></i>{{ $user->name }} - @foreach (auth()->user()->roles->pluck('name') as $item )
            {{ $item }} 
          @endforeach
        </div>
        <div>
          <i class="ni education_hat mr-2"></i>{{ ucfirst(auth()->user()->email) }}
        </div>
      </div>
      <br>
    </div>
    <!-- Upload Photo -->
    <div class="card">
      <form action="{{ route('admin.update.profile.avatar', auth()->user()->id )}}" method="POST" enctype="multipart/form-data" id="dropzone-form">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT" readonly>
        <input type="hidden" name="statStages" value="photo" readonly>
        <!-- Card header -->
        <div class="card-header">
          <!-- Title -->
          <div class="row align-items-center">
            <div class="col-8">
              <h5 class="h3 mb-0">Ubah Foto Pengguna</h5>
            </div>
            <div class="col-4 text-right">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </div>
        </div>
        <!-- Card body -->
        <div class="card-body">
          <!-- Single -->
          <div class="mb-3">
            <div class="fallback">
              <div class="custom-file">
                <input type="file" accept=".jpg, .jpeg, .png" name="image" class="form-control imgs" onchange="previewImage(this)"id="projectCoverUploads">
                <label class="custom-file-label" for="projectCoverUploads">Pilih Berkas</label>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
    <div class="card" style="align-items: center">
        <!-- Card body -->
        <div class="card-body">
          <!-- Single -->
          <div class="col-12 col-md-12 ">
            <button type="button" class="btn btn-sm btn-danger d-block mb-2 mx-auto remove_preview text-center" onclick="resetPreview(this)" disabled>Reset Preview</button>
            <img class="img-responsive" width="200px;" style="padding:.25rem;background:#eee;display:block;">
          </div>
        </div>
    </div>
  </div>
  <div class="col-xl-8 order-xl-1">
    <div class="card">
      {{-- Edit Profile --}}
      <form action="{{ route('admin.update.profile', auth()->user()->id )}}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT" readonly>
        <input type="hidden" name="statStages" value="user" readonly>
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col-6">
              <h3 class="mb-0">Atur Informasi Pengguna </h3>
            </div>
            <div class="col-6 text-right">
              <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-change-password">
                <i class="ni ni-lock-circle-open"></i>
                Ubah Password
              </button>
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <h6 class="heading-small text-muted mb-4">Informasi Pengguna</h6>
          <div class="pl-lg-4">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-name">Nama</label>
                  <input type="text" id="input-name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Lengkap" value="{{ $user->name }}" name="name">
                  @error('name')
                      <div class="invalid-feedback">
                          {{ $message }}
                      </div>
                  @enderror
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-email">Alamat Email</label>
                  <input type="email" id="input-email" class="form-control @error('email') is-invalid @enderror" placeholder="Alamat Email" name="email" value="{{ $user->email }}">
                  @error('email')
                      <div class="invalid-feedback">
                          {{ $message }}
                      </div>
                  @enderror
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-address">Alamat Rumah</label>
                  <button type="button" class="form-control" id="btn-address" data-toggle="modal" data-target="#modal-change-address">
                    {{ $user->address }}
                  </button>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-phone">Nomor HP</label>
                  <div class="input-group input-group-merge">
                    <div class="input-group-prepend">
                      <span class="input-group-text">+62</span>
                    </div>
                    <input type="number" step="1" min="0" id="input-phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Nomor HP" value="{{ $user->phone }}" name="phone">
                    @error('phone')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <hr class="my-4" />
        </div>
      </form>
    </div>
  </div>
</div>

{{-- modal change address --}}
<div class="modal fade" id="modal-change-address" tabindex="-1" role="dialog" aria-labelledby="modal-change-address" aria-hidden="true">
  <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">>
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card bg-secondary border-0 mb-0">
          <div class="card-body px-lg-5 py-lg-5">
            <div class="text-center text-muted mb-4">
                <small>Ubah Alamat Rumah</small>
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
              <button type="button" onclick="addAddress('{{auth()->user()->id}}')" class="btn btn-primary my-4 btn-add-address">Submit</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- modal change password --}}
<div class="modal fade" id="modal-change-password" tabindex="-1" role="dialog" aria-labelledby="modal-change-password" aria-hidden="true">
  <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">>
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card bg-secondary border-0 mb-0">
          <div class="card-body px-lg-5 py-lg-5">
              <div class="text-center text-muted mb-4">
                  <small>Ubah Password</small>
              </div>
              <form role="form" action="{{ route('admin.reset.password', auth()->user()->id) }}" method="POST">
                  {{ csrf_field() }}
                  <input type="hidden" name="_method" value="PUT" readonly>
                  <input type="hidden" name="statStages" value="user" readonly>
                  <div class="form-group">
                    <div class="input-group input-group-merge input-group-alternative">
                        <input class="form-control" placeholder="Password Lama" type="password" name="oldpassword">
                    </div>
                  </div>
                  <div class="form-group">
                      <div class="input-group input-group-merge input-group-alternative">
                          <input class="form-control" placeholder="Password Baru" type="password" name="password">
                      </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group input-group-merge input-group-alternative">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                        </div>
                        <input class="form-control" placeholder="Konfirmasi Password Baru" type="password" name="password_confirmation">
                    </div>
                </div>
                  <div class="text-center">
                      <button type="Submit" class="btn btn-primary my-4">Submit</button>
                  </div>
              </form>
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
      console.log("Preview Image");
      let preview_image = $(input).closest('.images-content').find('.img-responsive');
      let preview_button = $(input).closest('.images-content').find('.remove_preview');

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

      $('.custom-file-label').html('Choose File');
      $(preview_image).attr('src', '');
      $(preview_button).prop('disabled', true);
      $(preview_form).val('');
  }

  function addAddress(userId) {
    const address = `${$('#villages option:selected').text()},${$('#districts option:selected').text()},${$('#regencies option:selected').text()},${$('#provinces option:selected').text()}`;
    let link = '{{ route('admin.update.address.profile', ':id') }}';
    link = link.replace(':id', userId);
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: link,
      type : "PUT",
      dataType : "json",
      data: { address: address},
      success:function(result) {
        if(result) {
          const data = result.data;
          $('#btn-address').empty().append(`${address}`);
          resetSelect();
        } else {
          console.log("data trouble");
        }
      }
    });
  }

  function searchProvince() {
    $('#regencies, #districts, #villages').empty();
    $('#regencies').append('<option value="" disabled selected>--Pilih Kabupaten/Kota--</option>');
    $('#districts').append('<option value="" disabled selected>--Pilih Kecamatan--</option>');
    $('#villages').append('<option value="" disabled selected>--Pilih Kelurahan--</option>');
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
    $('#villages').append('<option value="" disabled selected>--Pilih Kelurahan--</option>');
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
    $('#villages').append('<option value="" disabled selected>--Pilih Kelurahan--</option>');
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

  function resetSelect() {
    $('#regencies, #districts, #villages').empty();
    $('#regencies, #districts, #villages, .btn-add-address').prop('disabled', true);
    $('#provinces').append('<option value="" disabled selected>--Pilih Provinsi--</option>');
    $('#regencies').append('<option value="" disabled selected>--Pilih Kabupaten/Kota--</option>');
    $('#districts').append('<option value="" disabled selected>--Pilih Kecamatan--</option>');
    $('#villages').append('<option value="" disabled selected>--Pilih Kelurahan--</option>');
  }
</script>
    
@endsection
