@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'roles',
  'title' => 'Roles',
  'first_title' => 'Roles',
  'first_link' => route('admin.role.index')
])

@section('plugins_css')
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
@endsection

@section('header-right')
@if(auth()->user()->can('roles-create'))
  <div class="col-lg-6 col-5 text-right">
    <a href="{{ route('admin.role.create') }}" class="btn btn-sm btn-neutral">Buat Role</a>
  </div>
@endif
@endsection

@section('content_body')
    <div class="row">
      <div class="col">
        <div class="card">
          <!-- Card header -->
          <div class="card-header">
            <h3 class="mb-0">Manajeman Role</h3>
          </div>
          <div class="table-responsive py-4">
            <table class="table table-flush" id="rolesTable">
              <thead class="thead-light">
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Jumlah Pengguna</th>
                  <th>Jumlah Permission</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Jumlah Pengguna</th>
                  <th>Jumlah Permission</th>
                  <th>Aksi</th>
                </tr>
              </tfoot>
              <tbody>
                @foreach($roles as $index => $item)
                  <tr id="rows_{{ $item->id }}">
                    <td>{{ $index +1 }}</td>
                    <td>{{ucwords($item->name)}}</td>
                    <td>{{ $item->users_count }}</td>
                    <td>{{ $item->permissions_count }}</td>
                    <td>
                      <div class="dropdown">
                        <a class="btn btn-md btn-icon-only text-primary" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="ni ni-settings-gear-65"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                          @if(auth()->user()->can('roles-edit'))
                              <a href="{{ route('admin.role.edit', $item->id) }}" class="dropdown-item text-warning">Ubah</a>
                          @endif
                          @if(auth()->user()->can('roles-delete'))
                              <button onclick="deleteRole('{{ $item->id }}')" class="dropdown-item text-danger" id="block_{{ $item->id }}">Hapus</button>
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
  const rolesTable = $("#rolesTable").DataTable({
      lengthMenu: [ 5, 10, 25, 50, 75, 100 ],
      language: {
        "emptyTable": "Urutkan atau cari Data"
      },
      pageLength: 5,
      columnDefs: [
        {
          target: 4,
          orderable: false,
          searchable: false
        }
      ],
      responsive: true,
  });

  function deleteRole(id){
      $(".alert-result").slideUp(function(){
          $(this).remove();
      });
      Swal.fire({
          title: 'Apakah Kamu Yakin?',
          text: "Data Role ini akan dihapus!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, Hapus!'
      }).then((result) => {
          if(result.value){
            $.post("{{ route('admin.role.index') }}/"+id, {'_token': "{{ csrf_token() }}", '_method': 'DELETE'}, function(result){
              // Append Alert Result
              rolesTable.row("#rows_"+id).remove().draw();
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
  </script>
    
@endsection