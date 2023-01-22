@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'address',
  'title' => 'Kelurahan',
  'first_title' => 'Kelurahan',
  'first_link' => route('admin.villages.index')
])

@section('plugins_css')
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
@endsection

@section('content_body')
<form id="villageForm">
  {{ csrf_field() }}
  <input type="hidden" name="id" id="id" value="">
  <input type="hidden" id="idEdit" value="">
  <input type="hidden" name="_method" id="_method" value="POST">
  <div class="row">
    <div class="col-lg-5">
      <div class="card-wrapper">
        <!-- Input groups -->
        <div class="card">
          <!-- Card header -->
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col-lg-8 col-md-6">
                <h3 class="mb-0" id="form_title">Form Buat Data Kelurahan</h3>
              </div>
              <div class="col-lg-4 col-md-6 d-flex justify-content-end">
                <button type="button" onclick="resetForm()" class="btn btn-danger" id="btn-reset">Atur Ulang</button>
                @if (auth()->user()->can('villages-create'))
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
                    <select onchange="searchProvince()" id="provinces" class="form-control @error('provinces') is-invalid @enderror" data-toggle="select">
                      <option value=""></option>
                      @foreach($provinces as $item)
                          <option value="{{$item['id']}}">{{$item['name']}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="input-group input-group-merge">
                      <select onchange="searchRegency()" id="regencies" class="form-control @error('regencies') is-invalid @enderror" data-toggle="select">
                          <option value=""></option>
                      </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="input-group input-group-merge">
                    <select onchange="searchDistrict()" name="district_id" id="districts" class="form-control @error('districts') is-invalid @enderror" data-toggle="select">
                      <option value=""></option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="input-group input-group-merge">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-map-marker"></i></span>
                    </div>
                    <input class="form-control @error('name') is-invalid @enderror" placeholder="Nama Kelurahan" type="text" name="name" value="{{ old('name')}}" id="name" required>
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
    <div class="col-lg-7">
      <div class="card-wrapper">
        <!-- Roles -->
        <div class="card">
          <!-- Card header -->
          <div class="card-header">
            <h3 class="mb-0">Daftar Kelurahan yang telah terdaftar</h3>
          </div>
          <!-- Card body -->
          <div class="card-body">
            <table class="table table-flush" id="villagesTable">
              <thead class="thead-light">
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>AKsi</th>
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
<script src="{{ asset('vendor/select2/dist/js/select2.min.js')}}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function() {
    $('#provinces').select2({
      'placeholder': 'Urutkan Berdasarkan Provinsi',
    });

    $('#regencies').select2({
      'placeholder': 'Urutkan Berdasarkan Kabupaten/Kota',
    }).attr('disabled', true);

    $('#districts').select2({
      'placeholder': 'Urutkan Berdasarkan Kecamatan',
    }).attr('disabled', true);
    $("#name").attr('disabled', true);
    $('#btn-submit').attr('disabled', true);
  });

  let tableVillages = $("#villagesTable").DataTable({
    lengthMenu: [ 5, 10, 25, 50, 75, 100 ],
    language: {
      "emptyTable": "Urutkan atau cari Data"
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

  $("#villageForm").submit(function(e){
    e.preventDefault();

    $("input").removeClass('is-invalid');
    $(".invalid-feedback").remove();
    $("#btn-submit").attr('disabled', true);

    const id = $("#id").val();
    let link = "";
    const idEdit = $('#idEdit').val();
    if($("#_method").val() == "POST"){
        link = "{{ route('admin.villages.store') }}";
    } else {
        link = '{{ route('admin.villages.update', ':id') }}';
        link = link.replace(':id', id);
    }

    $("#btn-submit").text("Loading..");
    
    $.post(link, $(this).serialize(), function(result){
        console.log(result);
        const rows = (tableVillages.rows().count() == 0) ? "1" : tableVillages.row(':last').data()[0];
        if($("#_method").val() == "POST"){
          // Store
          tableVillages.row.add([
            (parseInt(rows) == 1 ? 1 : parseInt(rows)+1),
            result.data['name'],
            addActionOption(result.data['id'], result.data['name'], (parseInt(rows) == 1 ? 1 : parseInt(rows)+1))
          ]).draw().node().id = "rows_"+result.data['id'];
        } else {
          // Update
          const newData = [
            idEdit,
            result.data['name'],
            addActionOption(result.data['id'], result.data['name'], idEdit)
          ];
          tableVillages.row($("#rows_"+$("#id").val())).data(newData);
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
        $('#name').attr('disabled', false);
        resetForm();
        Swal.fire({
          icon: 'error',
          title: 'Oops... ' + textStatus,
          text: 'Tolong Coba Lagi atau Muat Ulang Halaman!'
        });
    });
  });

  function searchProvince() {
    $('#regencies').empty();
    $("#name").attr('disabled', true);
    $('#btn-submit').attr('disabled', true);
    tableVillages.clear().draw();
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('admin.regencies.index') }}?provinces=" + $('#provinces').val(),
      type : "GET",
      dataType : "json",
      success:function(result) {
        if(result) {
          $("#regencies").append('<option value=""></option>');
          $.each(result.data, (key, value) => {
            $('#regencies').append('<option value="'+ value['id'] +'">'+ value['name'] +'</option>')
          });
          $('#regencies').select2({
            'placeholder': 'Urutkan Berdasarkan Kabupaten/Kota',
          }).attr('disabled', false);
          $("#districts").empty().attr('disabled', true);
          $('#btn-submit').attr('disabled', true);
        } else {
          console.log("Terjadi Kesalahan");
        }
      }
    })
  }

  function searchRegency() {
    $('#districts').empty();
    $("#name").attr('disabled', true);
    $('#btn-submit').attr('disabled', true);
    tableVillages.clear().draw();
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('admin.districts.index') }}?regencies=" + $('#regencies').val(),
      type : "GET",
      dataType : "json",
      success:function(result) {
        if(result) {
          $("#districts").append('<option value=""></option>');
          $.each(result.data, (key, value) => {
            $('#districts').append('<option value="'+ value['id'] +'">'+ value['name'] +'</option>')
          });
          $('#districts').attr('disabled', false);
          $('#districts').select2({
            'placeholder': 'Urutkan Berdasarkan Kecamatan',
          }).attr('disabled', false);
          $('#btn-submit').attr('disabled', true);
        } else {
          console.log("Terjadi Kesalahan");
        }
      }
    })
  }

  function searchDistrict() {
    tableVillages.clear().draw();
    $(".dataTables_empty").text("Menunggu data...");
    $("#name").attr('disabled', true);
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('admin.villages.index') }}?districts=" + $('#districts').val(),
      type : "GET",
      dataType : "json",
      success:function(result) {
        if(result) {
          let counting = 0;
          if(result.data.length == 0) {
            $(".dataTables_empty").text("Tidak ada data.");
          }
          $.each(result.data, (key,value) => {
            tableVillages.row.add([
              counting += 1,
              value['name'],
              addActionOption(value['id'], value['name'], counting)
            ]).draw().node().id="rows_"+value['id'];
          });
          $("#name").attr('disabled', false);
          $('#btn-submit').attr('disabled', false);
        } else {
          console.log("Terjadi KEsalahan");
        }
      }
    })
  }

  function addActionOption(id, name, idEdit) {
    let setName = "'"+name+"'";
    let editOption = '', deleteOption = '';
    @if(auth()->user()->can('districts-edit')) {
      editOption = '<button type="button" onclick="editAction('+id+', '+setName+', '+idEdit+')" class="edit btn btn-success btn-sm">Ubah</button>';
    }
    @endif

    @if(auth()->user()->can('districts-delete')) {
      deleteOption = '<button type="button" onclick="deleteAction('+id+')" class="edit btn btn-danger btn-sm">Hapus</button>';
    }
    @endif

    return editOption+deleteOption;
  }

  function editAction(id,name,idEdit) {
    $("#id").val(id);
    $("#idEdit").val(idEdit);
    $("#_method").val('PUT');
    $("#name").val(name);

    $("#form_title").text('Form Ubah Data Kelurahan');
    $("#btn-submit").text("Update").attr('disabled', false);
    $("#name").attr('disabled', false);
    $("#provinces, #regencies, #districts").attr('disabled', true);
  }

  function deleteAction(id) {
    Swal.fire({
      title: 'Are you sure?',
      text: "This data will be deleted!",
      type: 'info',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.value) {
            let link = "{{ route('admin.villages.index') }}/"+id;
            $.post(link, {'_token': "{{ csrf_token() }}", '_method': 'DELETE'}, function(result){
              tableVillages.row("#rows_"+id).remove().draw();
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

  function resetForm() {    
    $("#id").val('');
    $("#idEdit").val('');
    $("#_method").val('POST');
    $("#name").val('');

    $("#form_title").text('Form Buat Data Kelurahan');
    $("#btn-submit").text("Submit").attr('disabled', true);
    $('#provinces').attr('disabled', false);
    $('#regencies, #districts').empty().attr('disabled', true);
    $("#name").attr('disabled', true);
  }
</script>
    
@endsection