@extends('layouts.admin.app',[
  'headers' => 'non-active',
  'menu' => 'Setting',
])

@section('headers')
<div class="header pb-6 d-flex align-items-center" style="min-height: 500px; background-image: url({{ asset('images/default/img-1-1000x600.jpg')}}); background-size: cover; background-position: center top;">
  <!-- Mask -->
  <span class="mask bg-gradient-default opacity-8"></span>
  <!-- Header container -->
  <div class="container-fluid d-flex align-items-center">
    <div class="row col-lg-7">
      <div class="col-lg-7 col-md-10">
        <h1 class="display-2 text-white">Hello {{ auth()->user()->name }}</h1>
      </div>
    </div>
  </div>
</div>
@endsection

@section('content_body')
<div class="row">
  <div class="col-xl-12 order-xl-5">
    <div class="card">
      {{-- Edit Profile --}}
      <form action="{{ route('admin.update.profile', auth()->user()->id )}}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT" readonly>
        <input type="hidden" name="statStages" value="user" readonly>
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col-6">
              <h3 class="mb-0">Settings</h3>
            </div>
            <div class="col-6 text-right">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <h6 class="heading-small text-muted mb-4">App Configuration</h6>
          <div class="pl-lg-4">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-name">App Name</label>
                  <input type="text" id="input-name" class="form-control " placeholder="App ENV" value="{{ config('app.name') }}" name="app_name">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-email">App Env</label>
                  <input type="text" id="input-email" class="form-control" placeholder="App ENV" name="app_env" value="{{ config('app.env') }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-name">App Key</label>
                  <input type="text" id="input-name" class="form-control" placeholder="App ENV" value="{{ config('app.key') }}" name="app_key">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-email">App Debug</label>
                  <input type="text" id="input-email" class="form-control" placeholder="App ENV" name="app_debug" value="{{ config('app.debug') === 1 ? 'true' : 'false' }}">
                </div>
              </div>
            </div>
          </div>
          <hr class="my-4" />
          <h6 class="heading-small text-muted mb-4">Database Configuration</h6>
          <div class="pl-lg-4">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-name">Database Connection</label>
                  <input type="text" id="input-name" class="form-control" placeholder="Database Connection" value="{{ config('database.default') }}" name="app_name">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-email">Database Host</label>
                  <input type="text" id="input-email" class="form-control" placeholder="Database Host" name="app_env" value="{{ config('database.connections.'.config('database.default').'.host') }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-name">Database Port</label>
                  <input type="text" id="input-name" class="form-control" placeholder="Database Port" value="{{ config('database.connections.'.config('database.default').'.port') }}" name="app_key">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-email">Database Name</label>
                  <input type="text" id="input-email" class="form-control" placeholder="Database Name" name="app_debug" value="{{ config('database.connections.'.config('database.default').'.database') }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-name">Database Username</label>
                  <input type="text" id="input-name" class="form-control" placeholder="Database Username" value="{{ config('database.connections.'.config('database.default').'.username') }}" name="app_key">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-email">Database Password</label>
                  <input type="password" id="input-email" class="form-control" placeholder="Database Password" name="app_debug" value="{{ config('database.connections.'.config('database.default').'.password') }}">
                </div>
              </div>
            </div>
          </div>
          <hr class="my-4" />
          <h6 class="heading-small text-muted mb-4">Twilio Configuration</h6>
          <div class="pl-lg-4">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-name">Twilio Auth SID</label>
                  <input type="text" id="input-name" class="form-control" placeholder="App ENV" value="{{ config('services.twilio.sid') }}" name="app_name">
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-email">Twilio Auth Token</label>
                  <input type="text" id="input-email" class="form-control" placeholder="App ENV" name="app_env" value="{{ config('services.twilio.token') }}">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label class="form-control-label" for="input-name">Twilio Phone Number</label>
                  <input type="text" id="input-name" class="form-control" placeholder="App ENV" value="{{ config('services.twilio.whatsapp_from') }}" name="app_key">
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection