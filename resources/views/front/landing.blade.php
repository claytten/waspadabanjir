@extends('layouts.front.app')

@section('plugins_css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/leaflet.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/easy-button.css')}}">
<link rel="stylesheet" type="text/css" rel='stylesheet' href="https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css">
<!-- Load Esri Leaflet Geocoder from CDN -->
<link rel="stylesheet" type="text/css" href="{{ asset('css/esri-leaflet-geocoder.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/front.css')}}">
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
    {{-- <li>
      <a class="btn-floating blue modal-trigger tooltipped" href="#aboutModal" data-position="left" data-tooltip="Tentang Situs Web">
        <i class="material-icons">help</i>
      </a>
    </li> --}}
    <li><a href="{{ route('form.report')}}" class="btn-floating red tooltipped" data-position="left" data-tooltip="Laporkan Banjir"><i class="material-icons">flag</i></a></li>
    <li>
      <a class="btn-floating green modal-trigger tooltipped" href="#subscriptionForm" data-position="left" data-tooltip="Langganan Berita Banjir di WhatsApp">
        <i class="material-icons">subscriptions</i>
      </a>
    </li>
    <li>
      <a class="btn-floating yellow modal-trigger tooltipped" href="#tableArea" data-position="left" data-tooltip="Data Banjir Dalam Bentuk Tabel">
        <i class="material-icons">room</i>
      </a>
    </li>
  </ul>
</div>

<div id="query">
  <p>{{ Carbon\Carbon::now()->format('d M Y') }}</p> 
</div>

{{-- Feature Discovery --}}
<div class="tap-target blue" data-target="menu">
  <div class="tap-target-content white-text">
    <h5>Selamat Datang</h5>
    <p>Kamu bisa melakukan langganan informasi dan pelaporan mengenai banjir seputar Kabupaten Klaten melalui Media Whatsapp disini. Happy Explore!</p>
  </div>
</div>

{{-- subscriptionForm --}}
<div id="subscriptionForm" class="modal">
  <div class="modal-content z-depth-3">
    <div class="row">
      <div class="col s12 right">
        <a href="javascript:void(0)" class="transparent right modal-close">
          <i class="material-icons center" style="color:black">cancel</i>
        </a>
      </div>
      <div class="col s12 center">
        <h4 style="text-transform: uppercase;">Panduan portal informasi banjir melalui WhatsApp</h4>
      </div>
      <div class="col s12">
        <div class="row">
          <ul class="collapsible collapsible-accordion" data-collapsible="accordion">
            <li>
              <div class="collapsible-header"><i class="material-icons">arrow_drop_down</i>1. Silahkan join ke portal informasi banjir terlebih dahulu</div>
              <div class="collapsible-body">
                <p>Terdapat 2 Pilihan yang bisa kamu gunakan</p>
                <p>- Tambahkan &nbsp;"<strong>join plain-fifteen</strong>"&nbsp; pada kolom pesan WhatsApp. Kemudian kirim pesan tersebut ke "+14155238886"</p>
                <p>- Scan Barcode di bawah ini atau <a href="http://wa.me/+14155238886?text=join%20plain-fifteen" target="_blank">Klik Disini</a>.
                  Maka kolom pesan WhatsApp akan terisi otomatis dengan kalimat &nbsp;"<strong>join slowly-happen</strong>"&nbsp;. Kemudian kirim pesan tersebut
                </p>
                <img class="materialboxed" width="200" src="{{ asset('images/step/whatsapp_scan_me.png') }}">
              </div>
            </li>
            <li>
              <div class="collapsible-header"><i class="material-icons">arrow_drop_down</i>2. Masukan nama dan alamat untuk melakukan registrasi awal.</div>
              <div class="collapsible-body">
                <p>- Silahkan ketik&nbsp;"<strong>bantuan</strong>"&nbsp;pada kolom pesan WhatsApp. Selanjutnya sistem akan memandu kamu untuk melakukan registrasi awal.</p>
                <p>Berikut ini pesan balasan yang akan kamu terima setelah melakukan proses tersebut.</p>
                <img class="materialboxed" width="200" src="{{ asset('images/step/step2.jpeg') }}">
              </div>
            </li>
            <li>
              <div class="collapsible-header"><i class="material-icons">arrow_drop_down</i>3. Ketik "menu" pada kolom pesan WhatsApp untuk melihat daftar layanan portal informasi banjir.</div>
              <div class="collapsible-body">
                <p>Berikut ini pesan balasan yang akan kamu terima setelah melakukan proses tersebut.</p>
                <img class="materialboxed" width="200" src="{{ asset('images/step/step3.jpeg') }}">
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- aboutModal --}}
<div id="aboutModal" class="modal">
  <div class="modal-content z-depth-15">
    <div class="row">
      <div class="col s12 right">
        <a href="javascript:void(0)" class="transparent right modal-close">
          <i class="material-icons center" style="color:black">cancel</i>
        </a>
      </div>
      <div class="col s12 center">
        <img src="{{ asset('images/diponegoro.png') }}" alt="Logo Diponegoro" height="160px">
        <h6>UNIVERSITAS DIPONEGORO</h6>
        <h6>PENGEMBANGAN SISTEM INFORMASI GEOGRAFIS BANJIR DI KABUPATEN KLATEN DENGAN MEMANFAATKAN MEDIA INFORMASI WEBSITE DAN WHATSAPP</h6>
        <br>
        <h6>TUGAS AKHIR</h6>
        <br>
        <h6>Diajukan sebagai salah satu syarat untuk memperoleh gelar Sarjana Teknik</h6>
        <br><br>
        <h6>WAHYU AJI SULAIMAN</h6>
        <h6>21120117140019</h6>
        <br><br>
        <h6>DEPARTEMEN TEKNIK KOMPUTER</h6>
        <h6>FAKULTAS TEKNIK</h6>
        <h6>UNIVERSITAS DIPONEGORO</h6>
        <h6>SEMARANG</h6>
        <h6>2022</h6>
      </div>
    </div>
  </div>
</div>

{{-- selectModal --}}
<div id="tableArea" class="modal">
  <div class="modal-content z-depth-3">
    <div class="row">
      <div class="col s12 right">
        <a href="javascript:void(0)" class="transparent right modal-close">
          <i class="material-icons center" style="color:black">cancel</i>
        </a>
      </div>
      <div class="col s12 center">
        <h4 style="text-transform: uppercase;">Tabel Data Banjir</h4>
      </div>
      <div class="col s12">
        <div class="table-responsive">
          <table class="table align-items-center table-flush tableField">
            <thead class="thead-light">
              <tr>
                <th>No</th>
                <th>Kecamatan</th>
                <th>Tanggal Awal Kejadian</th>
                <th>Tanggal Akhir Kejadian</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="tBodyField">
            </tbody>
          </table>
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
<script type="text/javascript" src="{{ asset('js/easy-button-minify.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/front-minify.js') }}" defer></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function() {
    $('#loading').hide();
    $('.fixed-action-btn').floatingActionButton();
    $('.modal').modal();
    // $('#aboutModal').modal('open');
    $('.tooltipped').tooltip();
    $('.collapsible').collapsible();
    $('.materialboxed').materialbox();
    $('.tap-target').tapTarget({
      onClose: () => {
        Cookies.set('discoveryClosed', true);
      }
    });
    Cookies.get('discoveryClosed') ? '' : $('.tap-target').tapTarget('open');
  });
</script>
@endsection