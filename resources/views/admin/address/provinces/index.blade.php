@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'provinces',
  'title' => 'Province',
  'first_title' => 'Province',
  'first_link' => route('admin.provinces.index')
])

@section('plugins_css')
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
@endsection

@section('header-right')
@if(auth()->user()->can('provinces-create'))
  <div class="col-lg-6 col-5 text-right">
    <a href="{{ route('admin.provinces.create') }}" class="btn btn-sm btn-neutral">New</a>
  </div>
@endif
@endsection

@section('content_body')
    <div class="row">
      <div class="col">
        <div class="card">
          <!-- Card header -->
          <div class="card-header">
            <h3 class="mb-0">Province Management</h3>
          </div>
          <div class="table-responsive py-4">
            <table class="table table-flush" id="provincesTable">
              <thead class="thead-light">
                <tr>
                  <th>No</th>
                  <th>Name</th>
                  <th>Regencies</th>
                  <th>Districts</th>
                  <th>Villages</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>No</th>
                  <th>Name</th>
                  <th>Regencies</th>
                  <th>Districts</th>
                  <th>Villages</th>
                  <th>Action</th>
                </tr>
              </tfoot>
              <tbody>
                @foreach($provinces as $index => $item)
                    <tr id="rows_{{ $item->id }}">
                      <td>{{ $index +1 }}</td>
                      <td>{{ $item->name }}</td>
                      <td>{{ $item->countRegency }}</td>
                      <td>{{ $item->countDistrict }}</td>
                      <td>{{ $item->countVillage }}</td>
                      <td>
                        <div class="dropdown">
                          <a class="btn btn-md btn-icon-only text-primary" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ni ni-settings-gear-65"></i>
                          </a>
                          <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                            @if(auth()->user()->can('provinces-edit'))
                                <a href="{{ route('admin.provinces.edit', $item->id) }}" class="dropdown-item text-warning">Edit</a>
                            @endif
                            @if(auth()->user()->can('provinces-delete'))
                                <button class="dropdown-item text-danger" id="block_{{ $item->id }}">Delete</button>
                            @endif
                          </div>
                        </div>
                      </td>
                    </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
@endsection

@section('plugins_js')
<script type="text/javascript" src="{{ asset('vendor/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  const DatatableButtons = (function() {

    // Variables

    var $dtButtons = $('#provincesTable');


    // Methods

    function init($this) {

      // For more options check out the Datatables Docs:
      // https://datatables.net/extensions/buttons/

      var buttons = ["copy", "print"];

      // Basic options. For more options check out the Datatables Docs:
      // https://datatables.net/manual/options

      var options = {
        order: [0, 'asc'],
        lengthChange: !1,
        dom: 'Bfrtip',
        buttons: buttons,
        // select: {
        // 	style: "multi"
        // },
        language: {
          paginate: {
            previous: "<i class='fas fa-angle-left'>",
            next: "<i class='fas fa-angle-right'>"
          }
        },
        columnDefs: [
        {
            targets: 5,
            orderable: false,
            searchable: false,
        }
    ],
      };

      // Init the datatable

      var table = $this.on( 'init.dt', function () {
        $('.dt-buttons .btn').removeClass('btn-secondary').addClass('btn-sm btn-default');
        }).DataTable(options);
    }


    // Events

    if ($dtButtons.length) {
      init($dtButtons);
    }

  })();
  </script>
    
@endsection