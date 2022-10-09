@extends('layouts.admin.app',[
'headers' => 'active',
'menu' => 'subscribers',
'title' => 'Subscribers',
'first_title' => 'Subscribers',
'first_link' => route('admin.subscribers.index')
])

@section('plugins_css')
<link rel="stylesheet" type="text/css"
  href="{{ asset('vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css"
  href="{{ asset('vendor/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
@endsection

@section('header-right')
<div class="col-lg-6 col-5 text-right">
  @if(auth()->user()->can('subscriber-create'))
  <a href="javascript:void(0)" class="btn btn-sm btn-neutral" onclick="resetForm()" data-toggle="modal"
    data-target="#modal-add-subscribers">Buat Subscriber</a>
  @endif
  @if (auth()->user()->getRoleNames()[0] === 'Super Admin' || auth()->user()->id === (!empty($cacheSub) ? $cacheSub->id
  : null))
  <a href="javascript:void(0)" class="btn btn-sm btn-neutral" data-toggle="modal"
    data-target="#modal-send-multiple">Siaran</a>
  @endif
</div>
@endsection

@section('content_body')
<div class="row">
  <div class="col">
    <div class="card">
      <!-- Card header -->
      <div class="card-header">
        <h3 class="mb-0">Manajemen Subscriber</h3>
      </div>
      <div class="table-responsive py-4">
        <table class="table table-flush" id="subscribeTable">
          <thead class="thead-light">
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Nomor HP</th>
              <th>Alamat</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Nomor HP</th>
              <th>Alamat</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </tfoot>
          <tbody>
            @foreach($subscribers as $index => $item)
            <tr id="rows_{{ $item->id }}">
              <td>{{ $index +1 }}</td>
              <td>{{ ucwords($item->name) }}</td>
              <td>
                <label id="phoneNumb{{$item->id}}" class="phoneNumb">{{ $item->phone }}</label>
                <button class="btn btn-info btn-sm" id="buttonPhoneNumb{{$item->id}}"
                  onclick="phoneNumbAct({{$item->id}})">
                  <i class="fa fa-eye" aria-hidden="true"></i>
                </button>
              </td>
              <td>
                {{$item->regency->name}},
                {{$item->regency->province->name}}</td>
              @if ($item->status)
              <td><button type="button" class="btn btn-success btn-sm"
                  onclick="changeStatus('{{$item->id}}', '{{ $index }}', '{{$item->status}}')">Aktif</button></td>
              @else
              <td><button type="button" class="btn btn-danger btn-sm"
                  onclick="changeStatus('{{$item->id}}', '{{ $index }}', '{{$item->status}}')">Nonaktif</button></td>
              @endif
              <td>
                @if(auth()->user()->can('subscriber-edit'))
                <button
                  onclick="editSubscribe('{{ $item['id']}}', '{{ $item['name'] }}', '{{ $item->phone }}', '{{ $item['status']}}', '{{ $index+1 }}')"
                  type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-add-subscribers">
                  Ubah
                </button>
                @endif
                @if(auth()->user()->can('subscriber-delete'))
                <button onclick="deleteSubscribe('{{ $item->id }}')" type="button"
                  class="btn btn-danger btn-sm">Hapus</button>
                @endif
                @if (auth()->user()->getRoleNames()[0] === 'Super Admin' || auth()->user()->id === (!empty($cacheSub) ?
                $cacheSub->id : null))
                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                  data-target="#modal-send-personal" onclick="$('#personal_phone_to').val('{{ $item->phone }}')">
                  <i class="fa fa-paper-plane" aria-hidden="true"></i>
                </button>
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

{{-- Create Subscribers --}}
<div class="modal fade" id="modal-add-subscribers" tabindex="-1" role="dialog" aria-labelledby="modal-add-subscribers"
  aria-hidden="true">
  <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card bg-secondary border-0 mb-0">
          <div class="card-body px-lg-5 py-lg-5">
            <div class="text-center text-muted mb-4">
              <small id="form_title">Tambahkan Subscriber</small>
            </div>
            <form id="addSubscribeForm">
              <input type="hidden" name="id" id="id" value="">
              <input type="hidden" id="idEdit" value="">
              <input type="hidden" name="_method" id="_method" value="POST">
              @csrf
              <div class="form-group">
                <label class="form-control-label" for="name">Nama</label>
                <input class="form-control @error('name') is-invalid @enderror" placeholder="Nama Lengkap" type="text"
                  name="name" value="{{ old('name') }}" id="name">
                @error('name')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
                @enderror
              </div>
              <div class="form-group">
                <label class="form-control-label" for="phone">Nomor HP</label>
                <input class="form-control @error('phone') is-invalid @enderror" name="phone" type="text" id="phone"
                  placeholder="contoh (+62xx/08xx)" onchange="phoneNumber(this)" onkeyup="phoneNumber(this)">
                @error('phone')
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $message }}</strong>
                </span>
                @enderror
              </div>
              <div class="form-group">
                <div class="input-group input-group-merge input-group-alternative">
                  <label class="form-control-label" for="provinces">Provinsi</label>
                  <select onchange="searchProvince()" id="provinces" class="form-control" data-toggle="select">
                    <option value="" disabled selected>--Pilih Provinsi--</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <div class="input-group input-group-merge input-group-alternative">
                  <label class="form-control-label" for="regencies">Kabupaten/Kota</label>
                  <select id="regencies" class="form-control" data-toggle="select" name="address">
                    <option value="" disabled selected>--Pilih Kabupaten/Kota--</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <div class="input-group input-group-merge input-group-alternative">
                  <label class="form-control-label" for="status">Status</label>
                  <select name="status" id="status" class="form-control @error('status') is-invalid @enderror"
                    data-toggle="select">
                    <option value="" disabled selected>--Pilih Status--</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                  </select>
                </div>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-primary my-4 btn-add-subscriber">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Send Message Personal --}}
<div class="modal fade" id="modal-send-personal" tabindex="-1" role="dialog" aria-labelledby="modal-send-personal"
  aria-hidden="true">
  <div class="modal-dialog modal modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card bg-secondary border-0 mb-0">
          <div class="card-body px-lg-4 py-lg-4">
            <div class="row">
              <div class="col-lg-12">
                <div class="text-center text-muted mb-4">
                  <small id="form_title">Kirim Pesan Pribadi</small>
                </div>
              </div>

              <div class="col-lg-12">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <div class="input-group-prepend">
                          <span class="input-group-text">dari</span>
                        </div>
                        <input class="form-control" style="padding-left: 10px" placeholder="Your phone from" type="text"
                          name="personal_phone_from" id="personal_phone_from" value="Admin" readonly>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <div class="input-group-prepend">
                          <span class="input-group-text">to</span>
                        </div>
                        <input class="form-control" style="padding-left: 10px" placeholder="Your phone to" type="text"
                          name="personal_phone_to" id="personal_phone_to" readonly>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-12">
                <div class="form-group">
                  <div class="input-group input-group-merge">
                    <textarea class="form-control" style="padding-left: 10px" placeholder="Isi Pesan"
                      name="personal_body_message" id="personal_body_message"></textarea>
                  </div>
                </div>
              </div>

              <div class="col-lg-12">
                <div class="text-center">
                  <button type="button" class="btn btn-primary my-4" onclick="sendPersonalMessage()">Submit</button>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Send Message Multiple --}}
<div class="modal fade" id="modal-send-multiple" tabindex="-1" role="dialog" aria-labelledby="modal-send-multiple"
  aria-hidden="true">
  <div class="modal-dialog modal modal-dialog-centered modal-lg" role="document">>
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card bg-secondary border-0 mb-0">
          <div class="card-body px-lg-4 py-lg-4">
            <div class="row">
              <div class="col-lg-12">
                <div class="text-center text-muted mb-4">
                  <small id="form_title">Siaran Pesan</small>
                </div>
              </div>

              <div class="col-lg-12" id="section-from">
                <div class="row">
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <div class="input-group-prepend">
                          <span class="input-group-text">from</span>
                        </div>
                        <input class="form-control" style="padding-left: 10px" placeholder="Your phone from" type="text"
                          name="multiple_phone_from" id="multiple_phone_from" value="Admin" readonly>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <select name="multiple_phone" id="multiple_phone" class="form-control" data-toggle="select"
                          onchange="changeSenders(this)">
                          <option value="" disabled selected>--Pilih Penerima--</option>
                          <option value="all">Semua Subscriber</option>
                          <option value="regency">Berdasarkan Kabupaten/Kota</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-lg-12">
                <div class="form-group">
                  <div class="input-group input-group-merge">
                    <textarea class="form-control" style="padding-left: 10px" placeholder="Isi Pesan"
                      name="multiple_body_message" id="multiple_body_message"></textarea>
                  </div>
                </div>
              </div>

              <div class="col-lg-12">
                <div class="text-center">
                  <button type="button" class="btn btn-primary my-4" onclick="sendMultipleMessage()">Submit</button>
                </div>
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
<script type="text/javascript" src="{{ asset('vendor/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}">
</script>
<script type="text/javascript" src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
<script src="{{ asset('vendor/select2/dist/js/select2.min.js')}}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function() {
    $("#provinces, #regencies, #status").select2({width: "100%"});
    $('#multiple_phone, #multiple_phone_regency').select2();
    $('#regencies').prop('disabled', true);
    
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('api.provinces.index') }}",
      type : "GET",
      dataType : "json",
      success:function(result) {
        if(result) {
          $.each(result.data, (key, value) => {
            $('#provinces').append(`
              <option value="${value['id']}">${value['name']}
              </option>`);
          });
        } else {
          console.log("Terjadi Kesalahan");
        }
      }
    });
  });
  
  $(`.phoneNumb`).toggle("fast");
  const subscribeTable = $("#subscribeTable").DataTable({
    lengthMenu: [ 5, 10, 25, 50, 75, 100 ],
    language: {
      "emptyTable": "Urutkan atau data"
    },
    pageLength: 5,
    columnDefs: [
      {
        target: 5,
        orderable: false,
        searchable: false
      }
    ],
    responsive: true,
  });

  const phoneRegex = /^(^\+62|62|^08)(\d{3,4}-?){2}\d{3,4}$/;

  function phoneNumber(input) {
    let inputPhone = input.value;

    if(phoneRegex.test(inputPhone)) {
      $('#phone').removeClass('is-invalid').next('span').remove();
    } else {
      $('#phone').next('span').remove();
      $('#phone').addClass('is-invalid').after(`
        <span class="invalid-feedback" role="alert">
            <strong>Tolong cek kembali nomor HP kamu (+62xx/08xx)</strong>
        </span>
      `);
    }
  }

  function deleteSubscribe(id){
    Swal.fire({
        title: 'Apakah Kamu Yakin?',
        text: "Data Subscribe ini akan dihapus!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!'
    }).then((result) => {
        if(result.value){
          $.post("{{ route('admin.subscribers.index') }}/"+id, {'_token': "{{ csrf_token() }}", '_method': 'DELETE'}, function(result){
            // Append Alert Result
            subscribeTable.row("#rows_"+id).remove().draw();
            Swal.fire(
              result.status,
              result.message,
              'success'
            );
          }).fail(function(jqXHR, textStatus, errorThrown){
            Swal.fire({
              icon: 'error',
              title: 'Oops... ' + textStatus,
              text: 'Tolong Coba lagi atau Muat Ulang Halaman!'
            });
          });
        }
    });
  }

  function changeStatus(id, no, status) {
    Swal.fire({
      title: `Apakah kamu ingin mengubah status subscriber menjadi ${status == '1' ? `nonaktif` : `aktif`}?`,
      showCancelButton: true,
      confirmButtonText: `Ya, Ubah!`,
      confirmButtonColor: '#2dce89',
      cancelButtonText: `Tidak, Batalkan!`,
      cancelButtonColor: '#fb6340'
    }).then((result) => {
      if (result.isConfirmed) {
        ajaxStatus(id, no, status == '1' ? 0 : 1);
      }
    })
  }

  function ajaxStatus(id, no, status) {
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('admin.subscribers.index') }}?id=" + id + '&status=' + status,
      type : "GET",
      dataType : "json",
      error: function (xhr, status, error) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Terjadi Kesalahan'
        });
      },
      success:function(result) {
        if(result) {
          Swal.fire({
            position: 'middle',
            icon: 'success',
            title: 'Status Telah di ubah menjadi '+ (result.data['status'] === "1" ? 'Aktif' : 'Nonaktif') +'',
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            const updateData = [
              parseInt(no)+1,
              result.data['name'],
              `
                <label id="phoneNumb${id}" class="phoneNumb">${result.data['phone']}</label>
                <button class="btn btn-info btn-sm" id="buttonPhoneNumb${id}" onclick="phoneNumbAct(${id})">
                  <i class="fa fa-eye" aria-hidden="true"></i>
                </button>
              `,
              `${result.address['regency_name']}, ${result.address['province_name']},`,
              '<button type="button" class="btn btn-'+ (result.data['status'] === "1" ? 'success' : 'danger')+' btn-sm" onclick="changeStatus('+id+', '+no+', '+result.data['status']+')">'+ (result.data['status'] === "1" ? 'Aktif' : 'Nonaktif') +'</button>',
              addActionOption(id, result.data['name'], result.data['phone'], result.data['status'], no),
            ];
            subscribeTable.row($("#rows_"+id)).data(updateData);
            phoneNumbAct(id);
          });
        } else {
          console.log("Terjadi Kesalahan");
        }
      }
    });
  }

  function addActionOption(id,name, phone, status, idEdit) {
    let result = '';
    let setName = "'"+name+"'";
    @if(auth()->user()->can('subscriber-edit')) {
      result += '<button onclick="editSubscribe('+id+', '+setName+', '+phone+', '+status+', '+idEdit+')"  type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-add-subscribers">Ubah</button>';
    }
    @endif

    @if(auth()->user()->can('subscriber-delete')) {
      result += '<button onclick="deleteSubscribe('+id+')"  type="button" class="btn btn-danger btn-sm">Hapus</button>';
    }
    @endif

    @if (auth()->user()->getRoleNames()[0] === 'Super Admin' || auth()->user()->id === (!empty($cacheSub) ? $cacheSub->id : null))
      result += `
        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modal-send-personal" onclick="$('#personal_phone_to').val('${phone}')">
          <i class="fa fa-paper-plane" aria-hidden="true"></i>
        </button>
      `;
    @endif

    result = result.replaceAll(':id', id);

    return result;
  }

  function editSubscribe(id,name,phone,status, idEdit) {
    $("#id").val(id);
    $("#idEdit").val(idEdit);
    $("#_method").val('PUT');
    $("#name").val(name);
    $('#phone').val(phone);
    $('#status').val(status).change();
    $('#regencies').val("").change();
    $('#regencies').empty();
    $('#regencies').append('<option value="" disabled selected>--Pilih Kabupaten/Kota--</option>');
    $('#regencies, #districts, #villages').prop('disabled', true);

    $("#form_title").text('Ubah Subscriber');
    $(".btn-add-subscriber").text("Update");
  }

  $("#addSubscribeForm").submit(function(e){
    e.preventDefault();
    let status = '';
    let message = '';
    $("input").removeClass('is-invalid');
    $(".invalid-feedback").remove();

    if(!$('#name').val() || !phoneRegex.test($('#phone').val())) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Tolong periksa lagi form yang telah diisi!'
      });
    } else {
      const id = $("#id").val();
      let link = "";
      const idEdit = $('#idEdit').val();
      if($("#_method").val() == "POST"){
          if(!$('#regencies').val()) {
            return Swal.fire({
              icon: 'error',
              title: 'Oops...',
              text: 'Tolong periksa lagi form yang telah diisi!'
            });
          }
          link = "{{ route('admin.subscribers.store') }}";
      } else {
          link = '{{ route('admin.subscribers.update', ':id') }}';
          link = link.replace(':id', id);
      }

      $.post(link, $(this).serialize(), function(result){
        console.log(result);
        const rows = (subscribeTable.rows().count() == 0) ? "1" : subscribeTable.row(':last').data()[0];
        if(result.status == 'error') {
          Swal.fire({
            position: 'middle',
            icon: result.status,
            title: result.message,
            showConfirmButton: false,
            timer: 1500
          });
          return false;
        }
        if($("#_method").val() == "POST"){
          // Store
          subscribeTable.row.add([
            parseInt(rows)+1,
            result.data['name'],
            `
              <label id="phoneNumb${result.data['id']}" class="phoneNumb">${result.data['phone']}</label>
              <button class="btn btn-info btn-sm" id="buttonPhoneNumb${result.data['id']}" onclick="phoneNumbAct(${result.data['id']})">
                <i class="fa fa-eye" aria-hidden="true"></i>
              </button>
            `,
            `${result.address['regency_name']}, ${result.address['province_name']}`,
            '<button type="button" class="btn btn-'+ (result.data['status'] === "1" ? 'success' : 'danger')+' btn-sm" onclick="changeStatus('+result.data['id']+', '+parseInt(rows)+1+', '+result.data['status']+')">'+ (result.data['status'] === "1" ? 'Aktif' : 'Nonaktif') +'</button>',
            addActionOption(result.data['id'],result.data['name'], result.data['phone'], result.data['status'], parseInt(rows)+1)
          ]).draw().node().id = "rows_"+result.data['id'];
          phoneNumbAct(result.data['id']);
          status = result.status;
          message = result.message;
        } else {
          // Update
          const newData = [
            idEdit,
            result.data['name'],
            `
              <label id="phoneNumb${result.data['id']}" class="phoneNumb">${result.data['phone']}</label>
              <button class="btn btn-info btn-sm" id="buttonPhoneNumb${result.data['id']}" onclick="phoneNumbAct(${result.data['id']})">
                <i class="fa fa-eye" aria-hidden="true"></i>
              </button>
            `,
            `${result.address['regency_name']}, ${result.address['province_name']},`,
              '<button type="button" class="btn btn-'+ (result.data['status'] === "1" ? 'success' : 'danger')+' btn-sm" onclick="changeStatus('+result.data['id']+', '+idEdit+', '+result.data['status']+')">'+ (result.data['status'] === "1" ? 'Aktif' : 'Nonaktif') +'</button>',
            addActionOption(result.data['id'], result.data['name'], result.data['phone'], result.data['status'], idEdit)
          ];
          subscribeTable.row($("#rows_"+$("#id").val())).data(newData);
          phoneNumbAct(result.data['id']);
          status = result.status;
          message = result.message;
        }

        if($('#_method').val() === "POST") {
          Swal.fire({
            position: 'middle',
            icon: status,
            title: message,
            showConfirmButton: false,
            timer: 1500
          });
        } else {
          Swal.fire({
            position: 'middle',
            icon: status,
            title: message,
            showConfirmButton: false,
            timer: 1500
          });
        }
        
        resetForm();
      }).fail(function(jqXHR, textStatus, errorThrown){
        console.log(jqXHR);
        Swal.fire({
          icon: 'error',
          title: 'Oops... ' + textStatus,
          text: 'Tolong Coba lagi atau Muat Ulang Halaman!'
        });
      });
    }
  });

  function resetForm() {   
    $('#addSubscribeForm').trigger('reset');
    $("input").removeClass('is-invalid');
    $(".invalid-feedback").remove();
    $('#status, #regencies').val("").change();
    $('#regencies').empty();
    $('#regencies').append('<option value="" disabled selected>--Pilih Kabupaten/Kota--</option>');
    $('#regencies').prop('disabled', true);
    $("#id").val('');
    $("#idEdit").val('');
    $("#_method").val('POST');
    

    $("#form_title").text('Tambahkan Subscriber');
    $(".btn-add-subscriber").text("Submit");
  }

  function searchProvince() {
    $('#regencies').empty();
    $('#regencies').append('<option value="" disabled selected>--Pilih Kabupaten--</option>');
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('api.regencies.index') }}?provinces=" + $('#provinces').val(),
      type : "GET",
      dataType : "json",
      success:function(result) {
        if(result) {
          $.each(result.data, (key, value) => {
            $('#regencies').append(`
              <option value="${value['id']}">${value['name']}
              </option>`);
          });
          $('#regencies').prop('disabled', false);
        } else {
          console.log("Terjadi Kesalahan");
        }
      }
    })
  }

  function sendPersonalMessage() {
    if($('#personal_body_message').val()) {
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('admin.subscribers.personal') }}",
        type : "POST",
        dataType : "json",
        data: {
          phoneTo : $('#personal_phone_to').val(),
          body    : $('#personal_body_message').val()
        },
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
              title: 'Pesan kepada '+ $('#personal_phone_to').val() +' telah terkirim',
              showConfirmButton: false,
              timer: 1500
            });
          } else {
            console.log("Terjadi Kesalahan");
          }
        }
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Check your body message again!'
      });
    }
  }

  function changeSenders(input) {
    if(input.value === 'regency') {
      $('#section-from').after(`
        <div class="col-lg-12" id="section-from-regency">
          <div class="form-group">
            <div class="input-group input-group-merge">
              <select name="multiple_phone_regency" id="multiple_phone_regency" class="form-control" data-toggle="select">
                <option value="" disabled selected>--Pilih Daerah--</option>
              </select>
            </div>
          </div>
        </div>
      `);
      $('#multiple_phone_regency').select2();
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('admin.subscribers.getRegency') }}",
        type : "GET",
        dataType : "json",
        error: (xhr, status, error) => console.log(xhr, status),
        success: function (result) {
          $.each(result.data, (key, value) => {
            $('#multiple_phone_regency').append(`
              <option value="${value['id']}">${value['name']} - ${value['province']}</option>
            `);
          })
        }
      });
    } else {
      $('#section-from-regency').remove();
    }
  }

  function sendMultipleMessage() {
    if($('#multiple_body_message').val()) {
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('admin.subscribers.multiple') }}",
        type : "POST",
        dataType : "json",
        data: {
          type       : $('#multiple_phone').val(),
          regency_id : $('#multiple_phone_regency').val(),
          body       : $('#multiple_body_message').val()
        },
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
              title: 'Pesan kepada '+ $('#multiple_phone_to').text() +' telah terkirim',
              showConfirmButton: false,
              timer: 1500
            });
            $('#section-from-regency').remove();
          } else {
            console.log("Terjadi Kesalahan");
          }
        }
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Tolong cek kembali isi pesan!'
      });
    }
  }

  function phoneNumbAct(index) {
    $(`#phoneNumb${index}`).toggle("fast", function() {
      let stateToggle = $(this).is(":hidden");
      if(stateToggle) {
        $(`#buttonPhoneNumb${index}`).empty().append('<i class="fa fa-eye" aria-hidden="true"></i>');
      } else {
        $(`#buttonPhoneNumb${index}`).empty().append('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
      }
    });
  }
</script>

@endsection