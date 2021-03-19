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
        <h1 class="display-2 text-white">Hello {{ $admin->name }}</h1>
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
                  !empty($admin->image)
                      ? url('/storage'.'/'.$admin->image)
                          : asset('images/default/team-4.jpg')
              }}" alt="User Avatar" class="rounded-circle">
          </div>
        </div>
      </div>
      <br>
      <div class="text-center">
        <h5 class="h3">
          ==========
        </h5>
        <div class="h5 mt-4">
          <i class="ni business_briefcase-24 mr-2"></i>{{ $admin->name }} - {{ $admin->position }}
        </div>
        <div>
          <i class="ni education_hat mr-2"></i>{{ ucfirst(auth()->user()->role) }}
        </div>
      </div>
      <br>
    </div>
    <!-- Upload Photo -->
    <div class="card">
      <form action="{{ route('admin.update.profile.avatar', [auth()->user()->id, auth()->user()->role] )}}" method="POST" enctype="multipart/form-data" id="dropzone-form">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT" readonly>
        <input type="hidden" name="statStages" value="photo" readonly>
        <!-- Card header -->
        <div class="card-header">
          <!-- Title -->
          <div class="row align-items-center">
            <div class="col-8">
              <h5 class="h3 mb-0">Update Photo Profile</h5>
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
                <label class="custom-file-label" for="projectCoverUploads">Choose file</label>
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
      <form action="{{ route('admin.update.profile', [auth()->user()->id, auth()->user()->role] )}}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT" readonly>
        <input type="hidden" name="statStages" value="user" readonly>
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col-8">
              <h3 class="mb-0">Edit profile </h3>
            </div>
            <div class="col-4 text-right">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <h6 class="heading-small text-muted mb-4">Profile information</h6>
          <div class="pl-lg-4">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-name">Name</label>
                  <input type="text" id="input-name" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ $admin->name }}" name="name">
                  @error('name')
                      <div class="invalid-feedback">
                          {{ $message }}
                      </div>
                  @enderror
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-email">Email address</label>
                  <input type="email" id="input-email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" name="email" value="{{ $admin->email }}">
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
                  <label class="form-control-label" for="input-address">Address</label>
                  <input type="text" id="input-address" class="form-control @error('address') is-invalid @enderror" placeholder="Address" value="{{ $admin->address }}" name="address">
                  @error('address')
                      <div class="invalid-feedback">
                          {{ $message }}
                      </div>
                  @enderror
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-phone">Phone</label>
                  <input type="text" id="input-phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Phone Number" value="{{ $admin->phone }}" name="phone">
                  @error('phone')
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
                  <label class="form-control-label" for="input-id_card">ID Card</label>
                  <input type="text" id="input-id_card" class="form-control @error('id_card') is-invalid @enderror" placeholder="ID Card" value="{{ $admin->id_card }}" name="id_card">
                  @error('id_card')
                      <div class="invalid-feedback">
                          {{ $message }}
                      </div>
                  @enderror
                </div>
              </div>
            </div>
          </div>
          <hr class="my-4" />
        </div>
      </form>
    </div>
    <div class="card">
      {{-- Edit Account --}}
      <form action="{{ route('admin.update.setting', auth()->user()->id )}}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT" readonly>
        <input type="hidden" name="statStages" value="users" readonly>
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col-7">
              <h3 class="mb-0">Edit Account </h3>
            </div>
            <div class="col-5 text-right">
              <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-change-password">
                <i class="ni ni-lock-circle-open"></i>
                Change Password
              </button>
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <h6 class="heading-small text-muted mb-4">Account information</h6>
          <div class="pl-lg-4">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-username">Username</label>
                  <input type="text" id="input-username" class="form-control @error('username') is-invalid @enderror" placeholder="Username" value="{{ $admin->user->username }}" name="username">
                  @error('username')
                      <div class="invalid-feedback">
                          {{ $message }}
                      </div>
                  @enderror
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-password_confirmation">Confirmation Password</label>
                  <input type="password" id="input-password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirmation Password" name="password_confirmation">
                  @error('password_confirmation')
                      <div class="invalid-feedback">
                          {{ $message }}
                      </div>
                  @enderror
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

{{-- modal change password --}}
<div class="modal fade" id="modal-change-password" tabindex="-1" role="dialog" aria-labelledby="modal-change-password" aria-hidden="true">
  <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">>
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card bg-secondary border-0 mb-0">
          <div class="card-body px-lg-5 py-lg-5">
              <div class="text-center text-muted mb-4">
                  <small>Change Password</small>
              </div>
              <form role="form" action="{{ route('admin.reset.password', auth()->user()->id) }}" method="POST">
                  {{ csrf_field() }}
                  <input type="hidden" name="_method" value="PUT" readonly>
                  <input type="hidden" name="statStages" value="user" readonly>
                  <div class="form-group">
                    <div class="input-group input-group-merge input-group-alternative">
                        <input class="form-control" placeholder="Old Password" type="password" name="oldpassword">
                    </div>
                  </div>
                  <div class="form-group">
                      <div class="input-group input-group-merge input-group-alternative">
                          <input class="form-control" placeholder="New Password" type="password" name="password">
                      </div>
                  </div>
                  <div class="form-group">
                    <div class="input-group input-group-merge input-group-alternative">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                        </div>
                        <input class="form-control" placeholder="Confirmation Password" type="password" name="password_confirmation">
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
</script>
    
@endsection
