@extends('layouts.front.app')

@section('plugins_css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/leaflet.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/easy-button.css')}}">
<link rel="stylesheet" type="text/css" rel='stylesheet' href="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css">
<!-- Load Esri Leaflet Geocoder from CDN -->
<link rel="stylesheet" type="text/css" href="{{ asset('css/esri-leaflet-geocoder.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/front.css')}}">
<link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
@endsection

@section('content_body')
<div class="wrap" id="loading">
  <div class="loading">
    <div class="bounceball"></div>
    <div class="text">NOW LOADING</div>
  </div>
</div>
<div id="mapid"></div>
<div class='pointer'></div>
<div class="fixed-action-btn">
  <a id="menu" class="btn-floating btn-large blue">
    <i class="large material-icons">message</i>
  </a>
  <ul>
    <li><a href="{{ route('form.report')}}" class="btn-floating red tooltipped" data-position="left" data-tooltip="Laporan"><i class="material-icons">report</i></a></li>
    <li>
      <a class="btn-floating green modal-trigger tooltipped" href="#subscriptionForm" data-position="left" data-tooltip="Whatsapp Subscriptions">
        <i class="material-icons">subscriptions</i>
      </a>
    </li>
  </ul>
</div>

{{-- Feature Discovery --}}
<div class="tap-target blue" data-target="menu">
  <div class="tap-target-content white-text">
    <h5>Selamat Datang Netizen</h5>
    <p>Kamu bisa melakukan langganan informasi dan pelaporan mengenai banjir seputar Kabupaten Klaten melalui Media Whatsapp disini. Happy Explore!</p>
  </div>
</div>

{{-- subscriptionForm --}}
<div id="subscriptionForm" class="modal">
  <div class="modal-content z-depth-3">
    <div class="row">
      <div class="col s12">
        <div class="col s12 m6 l6">
          <h4>Subscription Whatsapp</h4>
        </div>
        <div class="col s12 m6 l6 right-align header-modal">
          <button class="btn waves-effect waves-light btn-submit-form red modal-close" type="button">Cancel
            <i class="material-icons right">cancel</i>
          </button>
          <button class="btn waves-effect waves-light btn-submit-form" type="button" onclick="submitSubForm()">Submit
            <i class="material-icons right">send</i>
          </button>
        </div>
      </div>
      <div class="col s12">
        <div class="row">
          <div class="col s12">
            <div class="input-field col s12">
              <i class="material-icons prefix">account_circle</i>
              <input id="name" type="text" class="validate" name="name" required>
              <label for="name">Nama Lengkap</label>
              <span class="helper-text" data-error="Tolong Masukan Nama Dengan Benar"></span>
            </div>
          </div>
          <div class="col s12">
            <div class="input-field col s12">
              <i class="material-icons prefix">phone</i>
              <input id="phone" type="text" class="validate" name="phone" onblur="phoneNumber(this)" onfocus="phoneNumber(this)" onchange="phoneNumber(this)" onkeyup="phoneNumber(this)" required>
              <label for="phone">Nomor Telepon</label>
              <span class="helper-text" data-error="Tolong Masukan Nomor Telepon Dengan Benar (+62xx / 08xx)">Contoh Pengisian yang Benar (+62xx / 08xx)</span>
            </div>
          </div>
          <div class="col s12">
            <div class="col s1 m1 l1" style="width: 3rem">
              <i class="material-icons prefix">contacts</i>
            </div>
            <div class="input-field col s12 m2 l2">
              <select id="provinces" onchange="searchProvince()" required>
                <option value="" disabled selected>--Pilih Provinsi--</option>
              </select>
            </div>
            <div class="input-field col s12 m3 l3">
              <select id="regencies" onchange="searchRegency()" required>
                <option value="" disabled selected>--Pilih Kabupaten--</option>
              </select>
            </div>
            <div class="input-field col s12 m3 l3">
              <select id="districts" onchange="searchDistrict()" required>
                <option value="" disabled selected>--Pilih Kecamatan---</option>
              </select>
            </div>
            <div class="input-field col s12 m3 l3">
              <select id="villages" required>
                <option value="" disabled selected>--Pilih Kelurahan--</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('plugins_js')
<script type="text/javascript" src="{{ asset('js/leaflet.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/leaflet_fullscreen.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/esri-leaflet.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/esri-leaflet-geocoder.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/easy-button.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/front.js') }}" defer></script>
<script type="text/javascript" src="{{ asset('vendor/select2/dist/js/select2.min.js')}}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function() {
    $("#provinces, #regencies, #districts, #villages").select2({width: "100%"});
    $('#loading').hide();
    $('.fixed-action-btn').floatingActionButton();
    $('.modal').modal({dismissible:false});
    $('.tooltipped').tooltip();
    $('#regencies, #districts, #villages').prop('disabled', true);
    $('.tap-target').tapTarget({
      onClose: () => {
        Cookies.set('discoveryClosed', true);
      }
    });
    Cookies.get('discoveryClosed') ? '' : $('.tap-target').tapTarget('open');

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
    })
  });

  const getURL = "{{ route('home') }}";
  const phoneRegex = /^(^\+62|62|^08)(\d{3,4}-?){2}\d{3,4}$/;

  function phoneNumber(input) {
    let inputPhone = input.value;

    if(phoneRegex.test(inputPhone)) {
      $('#phone').removeClass('invalid').addClass('valid');
    } else {
      $('#phone').removeClass('valid').addClass('invalid');
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
          $('#districts, #villages').prop('disabled', true);
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
          $('#villages').prop('disabled', true);
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
        } else {
          console.log("data trouble");
        }
      }
    })
  }

  function submitSubForm() {
    if(!$('#name').val() || !phoneRegex.test($('#phone').val()) || !$('#villages').val()) {
      M.toast({html: 'Ada Kesalahan Dalam Pengisian Data', classes: 'red'})
    } else {
      let formData = new FormData();
      formData.append('name',$('#name').val());
      formData.append('address', $('#villages').val());
      formData.append('phone', $('#phone').val());
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('home.store') }}",
        type: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        error: function (xhr, status, error) {
          console.log(xhr.responseText);
          M.toast({html: 'Terdapat Kendala Silahkan ulangi pengisian', classes: 'red'});
        },
        success:function(result) {
          if(result) {
            console.log(result);
            M.toast({html: 'Telah berhasil berlangganan. silahkan cek pesan whatsapp untuk memulai penggunaan.', classes: 'green'});
          } else {
            console.log("data trouble");
          }
        }
      })
    }
  }
</script>
@endsection