@extends('layouts.front.app')

@section('plugins_css')
<link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">
@endsection

@section('inline_css')
<style>
body { margin: 0; }
html,
body,
.form-box {
  height: 100%;
}
body {
  background-color: #C0E3C2;
}

@media only screen and (min-width: 993px) {
  .container-modal {
    width: 25% !important;
  }
}

@media only screen and (max-width : 601px) {
  .form-box {
    height: auto;
  }
  .container-modal {
    width: 90% !important;
  }
}

.container-modal {
  width: 25%;
}

.address-box {
  display: none;
  position: fixed;
  left: 0;
  right: 0;
  padding: 0;
  height: auto;
  margin: auto;
  overflow-y: auto;
  border-radius: 2px;
  will-change: top, opacity;
}
.address-box:focus {
  border: none;
  outline: none;
}
</style>
@endsection

@section('content_body')
<div class="container form-box valign-wrapper" style="justify-content: center">
  <div class="section valign">    
    <div id="contact-page" class="card hoverable">
        <div class="card-content">
            <div class="row">
              <div class="col s12 m6">
                <div class="row">
                  <div class="col s12">
                    <h5>Form Pelaporan</h5>
                  </div>
                </div>
                <form id="reportForm">
                  <div class="row">
                    <div class="input-field col s12">
                      <input id="name" type="text" class="validate" name="name" required>
                      <label for="name">Nama Lengkap</label>
                      <span class="helper-text" data-error="Tolong Masukan Nama Dengan Benar"></span>
                    </div>
                  </div>
                  <div class="row report-input">
                    <div class="input-field col s12">
                      <select id="reporting" onchange="reportingAction()" required>
                        <option value="" disabled selected></option>
                        <option value="ask">Pertanyaan</option>
                        <option value="suggest">Kritik & Saran</option>
                        <option value="report">Laporan Banjir</option>
                      </select>
                      <label>Jenis Pelaporan</label>
                    </div>
                  </div>
                  <div class="row">
                    <div class="input-field col s12">
                      <textarea id="message" class="materialize-textarea" data-length="120" required></textarea>
                      <label for="message">Isi Laporan</label>
                      <span class="helper-text" data-error="Jumlah Karakter yang digunakan melebihi batas"></span>
                    </div>
                  </div>
                </form>
                <div class="row">
                  <div class="input-field col s12" style="text-align: right">
                    <a href="{{ route('home') }}" class="btn waves-effect waves-light red">Back
                      <i class="material-icons left">arrow_back</i>
                    </a>
                    <button class="btn waves-effect waves-light orange" type="button" onclick="resetForm()">Reset</button>
                    <button class="btn waves-effect waves-light" type="button" onclick="submitSubForm()">Send
                      <i class="material-icons right">send</i>
                    </button>
                  </div>
                </div>
              </div>                      
              <div class="col s12 m6">
                <ul class="collapsible collapsible-accordion" data-collapsible="accordion">
                  <li class="active">
                    <div class="collapsible-header active"><i class="material-icons right">live_help</i> Petunjuk</div>
                    <div class="collapsible-body" style="">
                      <p>Berikut beberapa cara untuk bertanya/melaporkan situasi banjir di daerah Kabupaten Klaten.</p>
                      <p>- Melalui form laporan pada halaman ini.</p>
                      <p>- Melalui email (<strong>waspadabanjirklaten@gmail.com</strong>).</p>
                      <p>- Melalui berlangganan layanan WhatsApp (<a href="http://wa.me/+14155238886?text=join%20plain-fifteen" target="_blank">Klik Disini</a>)</p>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
            <footer style="text-align: center">
              <span>Copyright © 2022 <a class="blue-text text-darken-2" href="{{ route('home') }}" target="_blank">{{ (!empty(config('app.name')) ? config('app.name') : 'Laravel') }}</a> All rights reserved.</span>
            </footer>
        </div>
    </div>            
  </div>
</div>

{{-- Address Modal --}}
<div id="add_address" class="address-box container-modal form-box valign-wrapper">
  <div class="section valign">    
    <div id="contact-page" class="card hoverable">
      <div class="card-content">
        <span class="card-title center">Tambah Alamat</span>
        <div class="row">
          <div class="input-field col s12 ">
            <select id="provinces" onchange="searchProvince()" >
              <option value="" disabled selected>--Pilih Provinsi--</option>
            </select>
          </div>
          <div class="input-field col s12">
            <select id="regencies" onchange="searchRegency()" >
              <option value="" disabled selected>--Pilih Kabupaten--</option>
            </select>
          </div>
          <div class="input-field col s12">
            <select id="districts" onchange="searchDistrict()" >
              <option value="" disabled selected>--Pilih Kecamatan---</option>
            </select>
          </div>
          <div class="input-field col s12">
            <select id="villages" onchange="searchVillage()">
              <option value="" disabled selected>--Pilih Kelurahan--</option>
            </select>
          </div>
        </div>
      </div>
      <div class="card-action right-align">
        <button type="button" class="btn red waves-effect waves-light white-text modal-close">Batalkan</button>
        <button type="button" class="btn green waves-effect waves-light white-text btn-add-address" onclick="addAddress()">Tambahkan</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('plugins_js')
<script type="text/javascript" src="{{ asset('vendor/select2/dist/js/select2.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function(){
    $('.collapsible').collapsible();
    $('textarea#message').characterCounter();
    $('.address-box').modal({dismissible:false});
    $('.tooltipped').tooltip();
    $("#provinces, #regencies, #districts, #villages").select2({width: "100%"});
    $('#regencies, #districts, #villages, .btn-add-address').prop('disabled', true);
    $('#reporting').formSelect();

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
      $('#phone').removeClass('invalid').addClass('valid');
    } else {
      $('#phone').removeClass('valid').addClass('invalid');
    }
  }

  function addAddress() {
    const result = `${$('#villages option:selected').text().trim()}, ${$('#districts option:selected').text().trim()}, ${$('#regencies option:selected').text().trim()}, ${$('#provinces option:selected').text().trim()}`;
    console.log(result);
    console.log(`hehe ${$('#regencies option:selected').text()}`);
    $('#address').val('').val(result);
    $("label[for='address']").addClass("active");
  }

  function submitSubForm() {
    if(!$('#name').val() || !$('#message').val() || !$('#reporting').val()) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Silahkan Lengkapi Data yang belum terisi'
      });
    } else if($('#reporting_type').val() === 'report') {
      console.log('hehe');
      if(!$('#phone').val() || !$('#address').val()) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Silahkan Lengkapi Data yang belum terisi'
        });
      }
    } else {
      let formData = new FormData();
      formData.append('name',$('#name').val());
      formData.append('message', $('#message').val());
      formData.append('report_type', $('#reporting').val());
      if ($('#reporting').val() === 'report') {
        formData.append('phone', $('#phone').val());
        formData.append('address', $('#address').val());
      }
      console.log(formData);
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('form.report.store') }}",
        type: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Telah Terjadi kesalahan pada server. Silahkan kirim ulang'
          });
        },
        success:function(result) {
          if(result) {
            console.log(result);
            Swal.fire({
              position: 'middle',
              icon: 'success',
              title: 'Laporan Anda Telah Terkirim',
              showConfirmButton: false,
              timer: 1500
            }).then(() => window.location.href = result.redirect_url);
          } else {
            console.log("data trouble");
          }
        }
      })
    }
  }

  function reportingAction() {
    if($('#reporting').val() === 'report')  {
      $('.report-input').after(`
        <div class="row phone-input">
          <div class="input-field col s12">
            <input id="phone" type="text" class="validate" name="phone" onblur="phoneNumber(this)" onfocus="phoneNumber(this)" onchange="phoneNumber(this)" onkeyup="phoneNumber(this)" required>
            <label for="phone">Nomor Telepon</label>
            <span class="helper-text" data-error="Tolong Masukan Nomor Telepon Dengan Benar (+62xx / 08xx)">Contoh Pengisian yang Benar (+62xx / 08xx)</span>
          </div>
        </div>
        <div class="row address-input">
          <div class="input-field col s11">
            <textarea id="address" class="materialize-textarea" required></textarea>
            <label for="address">Alamat Pelapor</label>
          </div>
          <div class="input-field col s1" style="padding-left: 0">
            <a class="btn waves-effect waves-light modal-trigger tooltipped" href="#add_address" data-position="left" data-tooltip="Tambahkan Alamat">
              <i class="material-icons">add</i>
            </a>
          </div>
        </div>
      `);
    } else {
      $('.address-input, .phone-input').remove();
    }
  }

  function searchProvince() {
    $('#regencies, #districts, #villages').empty();
    $('#regencies').append('<option value="" disabled selected>--Pilih Kabupaten--</option>');
    $('#districts').append('<option value="" disabled selected>--Pilih Kecamatan--</option>');
    $('#villages').append('<option value="" disabled selected>--Pilih Kelurahan--</option>');
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
    $('#districts').append('<option value="" disabled selected>--Pilih Kecamatan--</option>');
    $('#villages').append('<option value="" disabled selected>--Pilih Kelurahan--</option>');
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
    $('#villages').append('<option value="" disabled selected>--Pilih Kelurahan--</option>');
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

  function resetForm() {
    $('#reportForm')[0].reset();
    $('.address-input, .phone-input').remove();
  }
</script>
@endsection