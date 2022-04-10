@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'reports',
  'title' => 'Laporan',
  'first_title' => 'Laporan',
  'first_link' => route('admin.reports.index')
])

@section('plugins_css')
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
@endsection

@section('header-right')
@if(auth()->user()->can('reports-create'))
  <div class="col-lg-6 col-5 text-right">
    <a href="{{ route('admin.reports.create') }}" class="btn btn-sm btn-neutral">Buat Laporan</a>
  </div>
@endif
@endsection

@section('content_body')
    <div class="row">
      <div class="col">
        <div class="card">
          <!-- Card header -->
          <div class="card-header">
            <h3 class="mb-0">Manajemen Laporan</h3>
          </div>
          <div class="table-responsive py-4">
            <table class="table table-flush" id="reportsTable">
              <thead class="thead-light">
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Tipe Laporan</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Tipe Laporan</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </tfoot>
              <tbody>
                @foreach($reports as $index => $item)
                    <tr id="rows_{{ $item->id }}">
                      <th>{{ $index +1 }}</th>
                      <td>{{ ucwords($item->name) }}</td>
                      @if($item->report_type === 'report')
                        <td>Laporan Banjir</td>
                      @elseif ($item->report_type === 'suggest')
                        <td>Kritik & Saran</td>
                      @else
                        <td>Pertanyaan</td>
                      @endif
                      @if(auth()->user()->can('reports-edit'))
                        @if ($item->status)
                          <td><button type="button" class="btn btn-success btn-sm" onclick="changeStatus('{{$item->id}}', '{{$index}}')">Terverifikasi</button></td>
                        @else
                          <td><button type="button" class="btn btn-danger btn-sm" onclick="changeStatus('{{$item->id}}', '{{$index}}')" >Belum terverifikasi</button></td>
                        @endif
                      @else
                        {{ $item->status ? 'Terverifikasi' : 'Belum terverifikasi' }}
                      @endif
                      <td>
                        {{-- @if(auth()->user()->can('reports-edit'))
                          <a href="{{ route('admin.reports.edit', $item->id) }}" class="btn btn-success btn-sm">Ubah</a>
                        @endif --}}
                          <a href="{{ route('admin.reports.show', $item->id)}}" class="btn btn-info btn-sm">Tampilkan</a>
                        @if(auth()->user()->can('reports-delete'))
                          <button onclick="deleteReport('{{ $item->id }}')"  type="button" class="btn btn-danger btn-sm">Hapus</button>
                        @endif
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
  const reportsTable = $("#reportsTable").DataTable({
      lengthMenu: [ 5, 10, 25, 50, 75, 100 ],
      language: {
        "emptyTable": "Urutan atau mencari data"
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

  function deleteReport(id){
    Swal.fire({
        title: 'Apakah Kamu Yakin?',
        text: "Laporan ini akan dihapus!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!'
    }).then((result) => {
        if(result.value){
          $.post("{{ route('admin.reports.index') }}/"+id, {'_token': "{{ csrf_token() }}", '_method': 'DELETE'}, function(result){
            // Append Alert Result
            reportsTable.row("#rows_"+id).remove().draw();
            Swal.fire(
              result.status,
              result.message,
              'success'
            );
          }).fail(function(jqXHR, textStatus, errorThrown){
            Swal.fire({
              icon: 'error',
              title: 'Oops... ' + textStatus,
              text: 'Tolong Coba Lagi dan Muat Ulang Halaman ini!'
            });
          });
        }
    });
  }

  function changeStatus(id, no) {
    Swal.fire({
      title: 'Ingin mengubah status laporan?',
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: `Verifikasi`,
      confirmButtonColor: '#2dce89',
      denyButtonText: `Jangan Verifikasi`,
      denyButtonColor: '#f5365c',
    }).then((result) => {
      if (result.isConfirmed) {
        ajaxStatus(id, no, 1);
      } else if (result.isDenied) {
        ajaxStatus(id, no, 0);
      }
    })
  }

  function ajaxStatus(id, no, status) {
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('admin.reports.index') }}?id=" + id + '&status=' + status,
      type : "GET",
      dataType : "json",
      error: function (xhr, status, error) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Terdapat Kesalahan!'
        });
      },
      success:function(result) {
        if(result) {
          Swal.fire({
            position: 'middle',
            icon: 'success',
            title: 'Status Laporan telah di ubah ke '+ (status === 1 ? 'Terverifikasi' : 'Belum terverifikasi') +'',
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            let reportResult = '';
            if(result.data['report_type'] === 'report') {
              reportResult = 'Laporan Banjir';
            } else if(result.data['report_type'] === 'suggest') {
              reportResult = 'Kritik & Saran';
            } else {
              reportResult = 'Pertanyaan'
            }
            const updateData = [
              no,
              result.data['name'],
              reportResult,
              '<button type="button" class="btn btn-'+ (status === 1 ? 'success' : 'danger')+' btn-sm" onclick="changeStatus('+id+', '+no+')">'+ (status === 1 ? 'Terverifikasi' : 'Belum terverifikasi') +'</button>',
              addActionOption(id),
            ];
            reportsTable.row($("#rows_"+id)).data(updateData);
          });
        } else {
          console.log("data trouble");
        }
      }
    });
  }

  function addActionOption(id) {
    let result = '';
    // @if(auth()->user()->can('reports-edit')) {
    //   result += '<a href="{{ route('admin.reports.edit', ':id') }}" class="btn btn-success btn-sm">Ubah</a>';
    // }
    // @endif

    result += '<a href="{{ route('admin.reports.show', ':id')}}" class="btn btn-info btn-sm">Tampilkan</a>';

    @if(auth()->user()->can('reports-delete')) {
      result += '<button onclick="deleteReport('+id+')"  type="button" class="btn btn-danger btn-sm">Hapus</button>';
    }
    @endif

    result = result.replaceAll(':id', id);

    return result;
  }
</script>
    
@endsection