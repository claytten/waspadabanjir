@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'accounts',
  'title' => 'Admin',
  'first_title' => 'Admin',
  'first_link' => route('admin.admin.index')
])

@section('plugins_css')
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
@endsection

@section('header-right')
@if(auth()->user()->can('admin-create'))
  <div class="col-lg-6 col-5 text-right">
    @if(auth()->user()->getRoleNames()[0] === 'Super Admin')
      <a href="javascript:void(0)" class="btn btn-sm btn-neutral" data-toggle="modal" data-target="#modal-change-admin">Pilih Admin Whatsapp</a>
    @else
      {{ !empty($statusWA['name']) ? $statusWA['name']  : ''}}
    @endif
    <a href="{{ route('admin.admin.create') }}" class="btn btn-sm btn-neutral">Buat Admin</a>
  </div>
@endif
@endsection

@section('content_body')
<div class="row">
  <div class="col">
    <div class="card">
      <!-- Card header -->
      <div class="card-header">
        <h3 class="mb-0">Admin Manajemen</h3>
      </div>
      <div class="table-responsive py-4">
        <table class="table table-flush" id="usersTable">
          <thead class="thead-light">
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Role</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Role</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </tfoot>
          <tbody>
            @foreach($users as $index => $item)
                <tr id="rows_{{ $item->id }}">
                  <th>{{ $index +1 }}</th>
                  <td>{{ ucwords($item->name) }}</td>
                  <td>{{ $item->email }}</td>
                  <td>{{ $item->role }}</td>
                  <td>{{ $item->status ?  'Aktif' : 'Nonaktif' }}</td>
                  <td>
                    <div class="dropdown">
                      <a class="btn btn-md btn-icon-only text-primary" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ni ni-settings-gear-65"></i>
                      </a>
                      <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                        @if(auth()->user()->can('admin-edit'))
                            <a href="{{ route('admin.admin.edit', $item->id) }}" class="dropdown-item text-warning">Ubah</a>
                        @endif
                        @if(auth()->user()->can('admin-delete'))
                          <button onclick="blockUser('{{ $item->id }}')" class="dropdown-item text-danger" id="block_{{ $item->id }}" {{ $item->status ? '' : 'style=display:none' }}>Hapus Sementara</button>
                          <button onclick="restoreUser('{{ $item->id }}')" class="dropdown-item text-success" id="restore_{{ $item->id }}" {{ $item->status ? 'style=display:none' : '' }}>Kembalikan</button>
                          <button onclick="deleteUser('{{ $item->id }}')" class="dropdown-item text-danger" id="block_{{ $item->id }}">Hapus Selamanya</button>
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
<div class="modal fade" id="modal-change-admin" tabindex="-1" role="dialog" aria-labelledby="modal-change-admin" aria-hidden="true">
  <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">>
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card bg-secondary border-0 mb-0">
          <div class="card-body px-lg-5 py-lg-5">
            <div class="text-center text-muted mb-4">
                <small id="form_title">Pilih Admin WhatsApp</small>
            </div>
            <div class="form-group">
              <div class="input-group input-group-merge input-group-alternative">
                <label class="form-control-label" for="statusWA">Pilih Admin</label>
                <select id="statusWA" class="form-control @error('statusWA') is-invalid @enderror" data-toggle="select" onchange="changeStatusWA(this)">
                  <option value=""></option>
                  @foreach($users as $index => $item)
                    <option value="{{ $item->id }}" {{ ($item->id === $statusWA['id']) ? 'selected' : '' }}>{{ ucwords($item->name) }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>
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
<script src="{{ asset('vendor/select2/dist/js/select2.min.js')}}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function() {
    $('#statusWA').select2({
        'placeholder': 'Pilih Admin Whatsapp',
    });
  });
  const usersTable = $("#usersTable").DataTable({
      lengthMenu: [ 5, 10, 25, 50, 75, 100 ],
      language: {
        "emptyTable": "Pilih urutkan atau cari data"
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
          title: 'Apakah Kamu Yakin?',
          text: "Data pengguna akan dihapus!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, Hapus!'
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
                text: 'Tolong Coba Lagi atau Muat Ulang Halaman!'
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
          title: 'Apakah Kamu Yakin?',
          text: "Status Pengguna ini akan diubah ke Nonaktif dan tidak bisa login!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, Nonaktif!'
      }).then((result) => {
          if(result.value){
            $.post("{{ route('admin.admin.index') }}/"+id, {'_token': "{{ csrf_token() }}", '_method': 'DELETE', 'user_action': 'block'}, function(result){
              // Append Alert Result
              const row_id = usersTable.row("#rows_"+id).index();
                
              if(result.user_status){
                  $("#block_"+id).show();
                  $("#restore_"+id).hide();
                  usersTable.cell({row: row_id, column: 4}).data('Aktif').draw();
              } else {
                  $("#block_"+id).hide();
                  $("#restore_"+id).show();
                  usersTable.cell({row: row_id, column: 4}).data('Nonaktif').draw();
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
                text: 'Tolong Coba Lagi atau Muat Ulang Halaman!'
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
          title: 'Apakah Kamu Yakin?',
          text: "Status Pengguna ini akan diubah ke Aktif dan bisa login kembali!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, Aktifkan!'
      }).then((result) => {
          $('#loading').show();
          if(result.value){
            $.post("{{ route('admin.admin.index') }}/"+id, {'_token': "{{ csrf_token() }}", '_method': 'DELETE', 'user_action': 'restore'}, function(result){
              const row_id = usersTable.row("#rows_"+id).index();

              // Append Alert Result
              if(result.user_status){
                $("#block_"+id).show();
                $("#restore_"+id).hide();
                usersTable.cell({row: row_id, column: 4}).data('Aktif').draw();
              } else {
                $("#block_"+id).hide();
                $("#restore_"+id).show();
                usersTable.cell({row: row_id, column: 4}).data('Nonaktif').draw();
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

  function changeStatusWA(input) {
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('admin.admin.index') }}?id=" + input.value,
      type : "GET",
      dataType : "json",
      error: function (xhr, status, error) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Terjadi Kesalahan!'
        });
      },
      success:function(result) {
        if(result) {
          Swal.fire({
            position: 'middle',
            icon: 'success',
            title: 'Ubah Admin WhatsApp ke ' + result.data['name'],
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            $('#statusWA').val(result.data['id']);
          });
        } else {
          console.log("Terdapat Kesalahan");
        }
      }
    });
  }
</script>
    
@endsection