@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'accounts',
  'title' => 'Users',
  'first_title' => 'Users',
  'first_link' => route('admin.admin.index')
])

@section('plugins_css')
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
@endsection

@section('header-right')
@if(auth()->user()->can('admin-create'))
  <div class="col-lg-6 col-5 text-right">
    <a href="{{ route('admin.admin.create') }}" class="btn btn-sm btn-neutral">New</a>
  </div>
@endif
@endsection

@section('content_body')
    <div class="row">
      <div class="col">
        <div class="card">
          <!-- Card header -->
          <div class="card-header">
            <h3 class="mb-0">Users Management</h3>
          </div>
          <div class="table-responsive py-4">
            <table class="table table-flush" id="usersTable">
              <thead class="thead-light">
                <tr>
                  <th>No</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Position</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>No</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </tfoot>
              <tbody>
                @foreach($users as $index => $item)
                    <tr id="rows_{{ $item->id }}">
                      @php
                          $role = $item->role;
                      @endphp
                      <th>{{ $index +1 }}</th>
                      <td>{{ ucwords($item->$role->name) }}</td>
                      <td>{{ $item->$role->email }}</td>
                      <td>{{ $item->getRoleNames()[0] }}</td>
                      <td>{{ $item->role }}</td>
                      <td>{{ $item->status ?  'Active' : 'Inactive' }}</td>
                      <td>
                        <div class="dropdown">
                          <a class="btn btn-md btn-icon-only text-primary" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ni ni-settings-gear-65"></i>
                          </a>
                          <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                            @if(auth()->user()->can('admin-edit'))
                                <a href="{{ route('admin.admin.edit', $item->id) }}" class="dropdown-item text-warning">Edit</a>
                            @endif
                            @if(auth()->user()->can('admin-delete'))
                              <button onclick="blockUser('{{ $item->id }}')" class="dropdown-item text-danger" id="block_{{ $item->id }}" {{ $item->status ? '' : 'style=display:none' }}>Block</button>
                              <button onclick="restoreUser('{{ $item->id }}')" class="dropdown-item text-success" id="restore_{{ $item->id }}" {{ $item->status ? 'style=display:none' : '' }}>Restore</button>
                              <button onclick="deleteUser('{{ $item->id }}')" class="dropdown-item text-danger" id="block_{{ $item->id }}">Delete</button>
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
<script type="text/javascript" src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  const usersTable = $("#usersTable").DataTable({
      lengthMenu: [ 5, 10, 25, 50, 75, 100 ],
      language: {
        "emptyTable": "Please select sort or search data"
      },
      pageLength: 5,
      columnDefs: [
        {
          target: 6,
          orderable: false,
          searchable: false
        }
      ],
      responsive: true,
  });

  function deleteUser(id){
      $(".alert-result").slideUp(function(){
          $(this).remove();
      });
      Swal.fire({
          title: 'Are you sure?',
          text: "This user status will be set to Destroy, and this user delete anymore!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
          if(result.value){
            $.post("{{ route('admin.admin.index') }}/"+id, {'_token': "{{ csrf_token() }}", '_method': 'DELETE', 'user_action': 'delete'}, function(result){
              // Append Alert Result
              usersTable.row("#rows_"+id).remove().draw();
              Swal.fire(
                result.status,
                result.message,
                'success'
              );
            }).fail(function(jqXHR, textStatus, errorThrown){
              Swal.fire({
                icon: 'error',
                title: 'Oops... ' + textStatus,
                text: 'Please Try Again or Refresh Page!'
              });
            });
          }
      });
  }
  
  function blockUser(id){
      $(".alert-result").slideUp(function(){
          $(this).remove();
      });
      
      Swal.fire({
          title: 'Are you sure?',
          text: "This user status will be set to Non-Active, and this user cannot login anymore!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, block it!'
      }).then((result) => {
          if(result.value){
            $.post("{{ route('admin.admin.index') }}/"+id, {'_token': "{{ csrf_token() }}", '_method': 'DELETE', 'user_action': 'block'}, function(result){
              // Append Alert Result
              const row_id = usersTable.row("#rows_"+id).index();
                
              if(result.user_status){
                  $("#block_"+id).show();
                  $("#restore_"+id).hide();
                  usersTable.cell({row: row_id, column: 5}).data('Active').draw();
              } else {
                  $("#block_"+id).hide();
                  $("#restore_"+id).show();
                  usersTable.cell({row: row_id, column: 5}).data('Inactive').draw();
              }
              Swal.fire(
                result.status,
                result.message,
                'success'
              );
            }).fail(function(jqXHR, textStatus, errorThrown){
              Swal.fire({
                icon: 'error',
                title: 'Oops... ' + textStatus,
                text: 'Please Try Again or Refresh Page!'
              });
            });
          }
      });
  }
  
  function restoreUser(id){
      $(".alert-result").slideUp(function(){
          $(this).remove();
      });
  
      
      Swal.fire({
          title: 'Are you sure?',
          text: "This user status will be set to Actived, and this user can login!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, Actived it!'
      }).then((result) => {
          $('#loading').show();
          if(result.value){
            $.post("{{ route('admin.admin.index') }}/"+id, {'_token': "{{ csrf_token() }}", '_method': 'DELETE', 'user_action': 'restore'}, function(result){
              const row_id = usersTable.row("#rows_"+id).index();

              // Append Alert Result
              if(result.user_status){
                $("#block_"+id).show();
                $("#restore_"+id).hide();
                usersTable.cell({row: row_id, column: 5}).data('Active').draw();
              } else {
                $("#block_"+id).hide();
                $("#restore_"+id).show();
                usersTable.cell({row: row_id, column: 5}).data('Inactive').draw();
              }
              Swal.fire(
                result.status,
                result.message,
                'success'
              );
            }).fail(function(jqXHR, textStatus, errorThrown){
              Swal.fire({
                icon: 'error',
                title: 'Oops... ' + textStatus,
                text: 'Please Try Again or Refresh Page!'
              });
            });
          }
      });
  }
  </script>
    
@endsection