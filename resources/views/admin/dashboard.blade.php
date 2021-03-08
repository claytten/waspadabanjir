@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'dashboard',
  'title' => 'Dashboard',
  'first_title' => 'Dashboard',
  'first_link' => route('admin.dashboard')
])

@section('content_body')
  <!-- Card stats -->
  <div class="row">
    <div class="col-xl-3 col-md-6">
      <div class="card card-stats">
        <!-- Card body -->
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h5 class="card-title text-uppercase text-muted mb-0">Web Hits</h5>
              <span class="h2 font-weight-bold mb-0">0</span>
            </div>
            <div class="col-auto">
              <div class="icon icon-shape bg-gradient-red text-white rounded-circle shadow">
                <i class="ni ni-check-bold"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection