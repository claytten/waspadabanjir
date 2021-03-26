@if(Session::get('message'))
    <div class="alert alert-result alert-{{ Session::get('status') }} alert-dismissible fade show" style="z-index: 1000; margin-bottom: 0" role="alert">
      <span class="alert-icon"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
      <span class="alert-text">{{ Session::get('message') }}</span>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
@endif

@if ($message = Session::get('success'))
    <div class="alert alert-result alert-{{ Session::get('status') }} alert-dismissible fade show" style="z-index: 1000; margin-bottom: 0" role="alert">
        <span class="alert-icon"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
        <span class="alert-text">{{ $message }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if ($message = Session::get('error'))
    <div class="alert alert-result alert-{{ Session::get('status') }} alert-dismissible fade show" style="z-index: 1000; margin-bottom: 0" role="alert">
        <span class="alert-icon"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
        <span class="alert-text">{{ $message }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if ($message = Session::get('warning'))
    <div class="alert alert-result alert-{{ Session::get('status') }} alert-dismissible fade show" style="z-index: 1000; margin-bottom: 0" role="alert">
        <span class="alert-icon"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
        <span class="alert-text">{{ $message }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if ($message = Session::get('info'))
    <div class="alert alert-result alert-{{ Session::get('status') }} alert-dismissible fade show" style="z-index: 1000; margin-bottom: 0" role="alert">
        <span class="alert-icon"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
        <span class="alert-text">{{ $message }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif