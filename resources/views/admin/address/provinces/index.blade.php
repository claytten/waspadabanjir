@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'address',
  'title' => 'Provinsi',
  'first_title' => 'Provinsi',
  'first_link' => route('admin.provinces.index')
])

@section('plugins_css')
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
@endsection

@section('content_body')
<form id="provinceForm">
  {{ csrf_field() }}
  <input type="hidden" name="id" id="id" value="">
  <input type="hidden" id="idEdit" value="">
  <input type="hidden" name="_method" id="_method" value="POST">
  <div class="row">
    <div class="col-lg-4">
      <div class="card-wrapper">
        <!-- Input groups -->
        <div class="card">
          <!-- Card header -->
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col-lg-8 col-md-6">
                <h3 class="mb-0" id="form_title">Form Buat Data Provinsi</h3>
              </div>
              <div class="col-lg-4 col-md-6 d-flex justify-content-end">
                <button type="button" onclick="resetForm()" class="btn btn-danger" id="btn-reset">Atur Ulang</button>
                @if (auth()->user()->can('provinces-create'))
                  <button type="submit" class="btn btn-primary" id="btn-submit">Submit</button>
                @endif
              </div>
            </div>
          </div>
          <!-- Card body -->
          <div class="card-body">
              <!-- Input groups with icon -->
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="input-group input-group-merge">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-map-marker"></i></span>
                      </div>
                      <input class="form-control @error('name') is-invalid @enderror" placeholder="Nama Provinsi" type="text" name="name" value="{{ old('name')}}" id="name">
                      @error('name')
                          <div class="invalid-feedback">
                              {{ $message }}
                          </div>
                      @enderror
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card-wrapper">
        <!-- Roles -->
        <div class="card">
          <!-- Card header -->
          <div class="card-header">
            <h3 class="mb-0">Daftar Provinsi yang telah terdaftar</h3>
          </div>
          <!-- Card body -->
          <div class="card-body">
            <table class="table table-flush" id="provincesTable">
                <thead class="thead-light">
                  <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jumlah Kabupaten/Kota</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($provinces as $index => $item)
                  <tr id="rows_{{ $item['id'] }}">
                    <td>{{ $index +1 }}</td>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['regencies_count'] }}</td>
                    <td>
                      @if(auth()->user()->can('provinces-edit'))
                        <button type="button" onclick="editAction('{{ $item['id']}}', '{{ $item['name'] }}', '{{ $index +1 }}')" class="edit btn btn-success btn-sm">Ubah</button>
                      @endif
                      @if(auth()->user()->can('provinces-delete'))
                        <button type="button" onclick="deleteAction('{{$item['id']}}')" class="delete btn btn-danger btn-sm">Hapus</button>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jumlah Kabupaten/Kota</th>
                    <th>Aksi</th>
                  </tr>
                </tfoot>
              </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
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
  const provincesTable = $("#provincesTable").DataTable({
      lengthMenu: [ 5, 10, 25, 50, 75, 100 ],
      language: {
        "emptyTable": "Urutkan atau cari data"
      },
      pageLength: 5,
      columnDefs: [
        {
          target: 2,
          orderable: false,
          searchable: false
        }
      ],
      responsive: true,
  });

  $("#provinceForm").submit(function(e){
    e.preventDefault();

    $("input").removeClass('is-invalid');
    $(".invalid-feedback").remove();

    const id = $("#id").val();
    let link = "";
    const idEdit = $('#idEdit').val();
    if($("#_method").val() == "POST"){
        link = "{{ route('admin.provinces.store') }}";
    } else {
        link = '{{ route('admin.provinces.update', ':id') }}';
        link = link.replace(':id', id);
    }

    $.post(link, $(this).serialize(), function(result){
        console.log(result);
        const rows = (provincesTable.rows().count() == 0) ? "1" : provincesTable.row(':last').data()[0];
        if($("#_method").val() == "POST"){
          // Store
          provincesTable.row.add([
            parseInt(rows)+1,
            result.data['name'],
            result.data['regencies_count'],
            addActionOption(result.data['id'], result.data['name'], parseInt(rows)+1)
          ]).draw().node().id = "rows_"+result.data['id'];
        } else {
          // Update
          const newData = [
            idEdit,
            result.data['name'],
            result.data['regencies_count'],
            addActionOption(result.data['id'], result.data['name'], idEdit)
          ];
          provincesTable.row($("#rows_"+$("#id").val())).data(newData);
        }

        resetForm();
    
        // Append Alert Result
        Swal.fire(
          result.status,
          result.message,
          'success'
        );
    }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        Swal.fire({
          icon: 'error',
          title: 'Oops... ' + textStatus,
          text: 'Tolong Coba Lagi atau Muat Ulang Halaman!'
        });
    });
  });

  function editAction(id,name,idEdit) {
    $("#id").val(id);
    $("#idEdit").val(idEdit);
    $("#_method").val('PUT');
    $("#name").val(name);

    $("#form_title").text('Form Ubah Data Provinsi');
    $("#btn-submit").text("Update");
  }

  function deleteAction(id) {
    Swal.fire({
      title: 'Apakah Kamu Yakin',
      text: "Data ini akan terhapus!",
      type: 'info',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.value) {
            let link = "{{ route('admin.provinces.index') }}/"+id;
            $.post(link, {'_token': "{{ csrf_token() }}", '_method': 'DELETE'}, function(result){
              provincesTable.row("#rows_"+id).remove().draw();
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

  function addActionOption(id,name, idEdit) {
    let setName = "'"+name+"'";
    let editOption = '', deleteOption = '';
    @if(auth()->user()->can('provinces-edit')) {
      editOption = '<button type="button" onclick="editAction('+id+', '+setName+', '+idEdit+')" class="edit btn btn-success btn-sm">Ubah</button>';
    }
    @endif

    @if(auth()->user()->can('provinces-delete')) {
      deleteOption = '<button type="button" onclick="deleteAction('+id+')" class="delete btn btn-danger btn-sm">Hapus</button>';
    }
    @endif

    return editOption+deleteOption;
  }

  function resetForm() {    
    $("#id").val('');
    $("#idEdit").val('');
    $("#_method").val('POST');
    $("#name").val('');

    $("#form_title").text('Form Buat Data Provinsi');
    $("#btn-submit").text("Submit");
  }
</script>
    
@endsection