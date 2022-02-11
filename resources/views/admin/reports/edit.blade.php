@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'reports',
  'title' => 'Laporan',
  'first_title' => 'Laporan',
  'first_link' => route('admin.reports.index'),
  'second_title' => 'Ubah',
  'second_link' => route('admin.reports.show', $report->id),
  'third_title'  => ucwords($report->name)
])

@section('plugins_css')
<link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
@endsection

@section('content_body')
<form id="reportForm" action="{{ route('admin.reports.update', $report->id )}}" method="POST">
  @csrf
  @method('PUT')
  <div class="card-wrapper">
    <div class="card">
      <div class="row">
        <div class="col-lg-12">
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col-lg-8 col-md-6">
                <h3 class="mb-0" id="form-map-title">Ubah laporan dari {{ ucwords($report->name) }}</h3>
              </div>
              <div class="col-lg-4 col-md-6 d-flex justify-content-end">
                <button type="button" class="btn btn-danger" onclick="deleteReport('{{ $report->id }}')">Delete</button>
                <button type="button" class="btn btn-warning" onclick="resetReport()" >Reset</button>
                <button type="submit" class="btn btn-primary" id="btn-submit" >Submit</button>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-12">
          <div class="row">
            <div class="col-lg-6">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group row">
                      <label for="name" class="col-md-2 col-form-label form-control-label @error('name') is-invalid @enderror">Nama Pelapor</label>
                      <div class="col-md-10">
                        <input class="form-control @error('name') is-invalid @enderror" name="name" type="text" id="name" value="{{$report->name}}" required>
                        @error('name')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 report-input">
                    <div class="form-group row">
                      <label for="report_type" class="col-md-2 col-form-label form-control-label">Tipe Laporan</label>
                      <div class="col-md-10">
                        <select name="report_type" onchange="changeType()" id="report_type" class="form-control @error('report_type') is-invalid @enderror" data-toggle="select" required>
                          <option value=""></option>
                          <option value="ask" {{ ($report->report_type === 'ask') ? 'selected' : '' }}>Pertanyaan</option>
                          <option value="suggest" {{ ($report->report_type === 'suggest') ? 'selected': '' }}>Kritik & Saran</option>
                          <option value="report" {{ ($report->report_type === 'report') ? 'selected': '' }}>Laporan Banjir</option>
                        </select>
                        @error('report_type')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                      </div>
                    </div>
                  </div>

                  @if ($report->report_type === 'report')
                    <div class="col-md-12 phone">
                      <div class="form-group row">
                        <label for="phone" class="col-md-2 col-form-label form-control-label">Nomor HP</label>
                        <div class="col-md-10">
                          <input class="form-control @error('phone') is-invalid @enderror" name="phone" type="text" id="phone" placeholder="Contoh (+62xx/08xx)" onblur="phoneNumber(this)" onfocus="phoneNumber(this)" onchange="phoneNumber(this)" onkeyup="phoneNumber(this)" value="{{ $report->phone}}">
                          @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                          @enderror
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 address">
                      <div class="form-group row">
                        <label for="address" class="col-md-2 col-form-label form-control-label">Alamat Pelapor</label>
                        <div class="col-md-10 input-group input-group-merge">
                          <input type="text" id="address" class="form-control @error('address') is-invalid @enderror" name="address" value="{{$report->address}}">
                          <div class="input-group-append">
                            <span class="input-group-text" data-toggle="modal" data-target="#modal-add-address"><i class="fas fa-plus"></i></span>
                          </div>
                          @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                          @enderror
                        </div>
                      </div>
                    </div>
                  @endif

                  <div class="col-md-12">
                    <div class="form-group row">
                      <label for="message" class="col-md-2 col-form-label form-control-label">Isi Laporan</label>
                      <div class="col-md-10">
                        <textarea class="form-control @error('message') is-invalid @enderror" name="message" id="message" value="{{$report->message}}" required>{{$report->message}}</textarea>
                        @error('message')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="card-body">
                <div class="form-group row">
                  <label for="status" class="col-md-2 col-form-label form-control-label">Status</label>
                  <div class="col-md-10">
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" data-toggle="select">
                      <option value=""></option>
                      <option value="0" {{ ($report->status) ? '': 'selected' }}>Belum Terverifikasi</option>
                      <option value="1" {{ ($report->status) ? 'selected': '' }}>Terverifikasi</option>
                    </select>
                    @error('status')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

{{-- Modal set Address --}}
<div class="modal fade" id="modal-add-address" tabindex="-1" role="dialog" aria-labelledby="modal-add-address" aria-hidden="true">
  <div class="modal-dialog modal- modal-dialog-centered modal-sm" role="document">>
    <div class="modal-content">
      <div class="modal-body p-0">
        <div class="card bg-secondary border-0 mb-0">
          <div class="card-body px-lg-5 py-lg-5">
            <div class="text-center text-muted mb-4">
                <small>Tambahkan Alamat</small>
            </div>
            <div class="form-group">
              <div class="input-group input-group-merge input-group-alternative">
                <label class="form-control-label" for="input-address">Provinsi</label>
                <select onchange="searchProvince()" id="provinces" class="form-control" data-toggle="select">
                  <option value="" disabled selected>--Pilih Provinsi--</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <div class="input-group input-group-merge input-group-alternative">
                <label class="form-control-label" for="input-address">Kabupaten/Kota</label>
                <select onchange="searchRegency()" id="regencies" class="form-control" data-toggle="select">
                  <option value="" disabled selected>--Pilih Kabupaten/Kota--</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <div class="input-group input-group-merge input-group-alternative">
                <label class="form-control-label" for="input-address">Kecamatan</label>
                <select onchange="searchDistrict()" id="districts" class="form-control" data-toggle="select">
                  <option value="" disabled selected>--Pilih Kecamatan--</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <div class="input-group input-group-merge input-group-alternative">
                <label class="form-control-label" for="input-address">Kelurahan</label>
                <select name="village_id" id="villages" class="form-control @error('villages') is-invalid @enderror" data-toggle="select" onchange="searchVillage()">
                  <option value="" disabled selected>--Pilih Kelurahan--</option>
                </select>
              </div>
            </div>
            
            <div class="text-center">
              <button type="button" onclick="addAddress()" class="btn btn-primary my-4 btn-add-address">Submit</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('plugins_js')
<script src="{{ asset('vendor/select2/dist/js/select2.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function() {
    $('#report_type').select2({
        'placeholder': 'Select Report Type',
    });
    $('#status').select2({
        'placeholder': 'Select Status',
    });
    $("#provinces, #regencies, #districts, #villages").select2({width: "100%"});
    $('#regencies, #districts, #villages, .btn-add-address').prop('disabled', true);

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
          console.log("data trouble");
        }
      }
    });
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
          <strong>Tolong cek kembali nomor HP yang diberikan (+62xx/08xx)</strong>
        </span>
      `);
    }
  }

  function addAddress() {
    const result = `${$('#villages option:selected').text().trim()}, ${$('#districts option:selected').text().trim()}, ${$('#regencies option:selected').text().trim()}, ${$('#provinces option:selected').text().trim()}`;
    $('#address').val('').val(result);
  }

  function changeType() {
    if($('#report_type').val() === 'report') {
      $('#phone, #address').remove();
      $('.report-input').after(`
        <div class="col-md-12 phone">
          <div class="form-group row">
            <label for="phone" class="col-md-2 col-form-label form-control-label">Nomor HP</label>
            <div class="col-md-10">
              <input class="form-control @error('phone') is-invalid @enderror" name="phone" type="text" id="phone" placeholder="contoh (+62xx/08xx)" onblur="phoneNumber(this)" onfocus="phoneNumber(this)" onchange="phoneNumber(this)" onkeyup="phoneNumber(this)" value="{{$report->phone}}">
              @error('phone')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
          </div>
        </div>
        <div class="col-md-12 address">
          <div class="form-group row">
            <label for="address" class="col-md-2 col-form-label form-control-label">Alamat</label>
            <div class="col-md-10 input-group input-group-merge">
              <input type="text" id="address" class="form-control @error('address') is-invalid @enderror" name="address" value="{{$report->address}}">
              <div class="input-group-append">
                <span class="input-group-text" data-toggle="modal" data-target="#modal-add-address"><i class="fas fa-plus"></i></span>
              </div>
              @error('address')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
              @enderror
            </div>
          </div>
        </div>
      `);
    } else {
      $('#reportForm').append(`<input id="address" type="text" name="address" value="null" disabled style="display:none">`).append(`<input type="text" id="phone" name="phone" value="null" disabled style="display:none">`);
      $('.phone, .address').remove();
    }
  }

  function deleteReport(id) {
    let link = '{{ route('admin.reports.destroy', ':id') }}';
    link = link.replace(':id', id);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: link,
      type: 'DELETE',
      async: false,
      cache: false,
      error: function (xhr, status, error) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Terdapat Kesalahan!'
        });
      },
      success: function(response){
        if(response.status === 'success') {
          Swal.fire({
            position: 'middle',
            icon: 'success',
            title: 'Data Berhasil Diubah',
            showConfirmButton: false,
            timer: 1500
          }).then(() => window.location.href = response.redirect_url);
        } else {
          console.log(response);
        }
      }
    });
  }

  function resetReport() {
    $('#reportForm')[0].reset();
    $("input").removeClass('is-invalid');
    $(".invalid-feedback").remove();
  }

  function searchProvince() {
    $('#regencies, #districts, #villages').empty();
    $('#regencies').append('<option value="" disabled selected>--Select Regency--</option>');
    $('#districts').append('<option value="" disabled selected>--Select District--</option>');
    $('#villages').append('<option value="" disabled selected>--Select Village--</option>');
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
          $('#districts, #villages, .btn-add-address').prop('disabled', true);
        } else {
          console.log("data trouble");
        }
      }
    })
  }

  function searchRegency() {
    $('#districts, #villages').empty();
    $('#districts').append('<option value="" disabled selected>--Select District--</option>');
    $('#villages').append('<option value="" disabled selected>--Select Village--</option>');
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('api.districts.index') }}?regencies=" + $('#regencies').val(),
      type : "GET",
      dataType : "json",
      success:function(result) {
        if(result) {
          $.each(result.data, (key, value) => {
            $('#districts').append('<option value="'+ value['id'] +'">'+ value['name'] +'</option>')
          });
          $('#districts').prop('disabled', false);
          $('#villages, .btn-add-address').prop('disabled', true);
        } else {
          console.log("data trouble");
        }
      }
    })
  }

  function searchDistrict() {
    $('#villages').empty();
    $('#villages').append('<option value="" disabled selected>--Select Village--</option>');
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "{{ route('api.villages.index') }}?districts=" + $('#districts').val(),
      type : "GET",
      dataType : "json",
      success:function(result) {
        if(result) {
          $.each(result.data, (key, value) => {
            $('#villages').append('<option value="'+ value['id'] +'">'+ value['name'] +'</option>')
          });
          $('#villages').prop('disabled', false);
          $('.btn-add-address').prop('disabled', true);
        } else {
          console.log("data trouble");
        }
      }
    })
  }
  
  function searchVillage() {
    $('.btn-add-address').prop('disabled', false);
  }
</script>
    
@endsection