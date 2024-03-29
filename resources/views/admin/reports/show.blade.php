@extends('layouts.admin.app',[
'headers' => 'active',
'menu' => 'reports',
'title' => 'Laporan',
'first_title' => 'Laporan',
'first_link' => route('admin.reports.index'),
'second_title' => 'Rincian',
'second_link' => route('admin.reports.show', $report->id),
'third_title' => ucwords($report->name)
])

@section('plugins_css')
<link rel="stylesheet" href="{{ asset('vendor/lightcase/css/lightcase.css')}}">
@endsection

@section('inline_css')
@endsection

@section('content_body')
<div class="card-wrapper">
  <div class="card">
    <div class="row">
      <div class="col-lg-12">
        <div class="card-header">
          <div class="row align-items-center">
            <div class="col-lg-8 col-md-6">
              <h3 class="mb-0" id="form-map-title">Rincian Laporan dari {{ ucwords($report->name) }}</h3>
            </div>
            <div class="col-lg-4 col-md-6 d-flex justify-content-end">
              <a class="btn btn-info" href="{{ route('admin.reports.index') }}">Kembali</a>
              {{-- @if (auth()->user()->can('reports-edit'))
              <a href="{{ route('admin.reports.edit', $report->id)}}">
                <button type="button" class="btn btn-warning">Ubah</button>
              </a>
              @endif --}}
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
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Nama
                      Pelapor</label>
                    <div class="col-md-10">
                      <input class="form-control" name="name" type="text" value="{{ $report->name }}" id="name"
                        disabled>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Tipe
                      Laporan</label>
                    <div class="col-md-10">
                      @php
                      if($report->report_type === 'report') {
                      $report_type = 'Laporan Banjir';
                      } else if($report->report_type === 'suggest') {
                      $report_type = 'Kritik & Saran';
                      } else {
                      $report_type = 'Pertanyaan';
                      }
                      @endphp
                      <input class="form-control" name="report_type" type="text" value="{{ $report_type }}"
                        id="report_type" disabled>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Isi
                      Laporan</label>
                    <div class="col-md-10">
                      <textarea class="form-control" name="message" value="{{ $report->message }}" id="message"
                        disabled>{{ $report->message }}</textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card-body">
              <div class="row">
                @if ($report->report_type == 'report')
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Nomor HP</label>
                    <div class="col-md-10">
                      <input class="form-control" name="phone" type="text" value="{{ $report->phone }}" id="phone"
                        disabled>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Alamat</label>
                    <div class="col-md-10">
                      <input class="form-control" name="address" type="text" value="{{ $report->address }}" id="address"
                        disabled>
                    </div>
                  </div>
                </div>
                @endif
                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Status</label>
                    <div class="col-md-10">
                      <input class="form-control" name="status" type="text"
                        value="{{ ($report->status == 'accept') ? 'Terverifikasi' : ($report->status == 'process' ? 'Belum terverifikasi' : 'Laporan ditolak') }}"
                        id="status" disabled>
                    </div>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group row">
                    <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Foto</label>
                    <div class="col-md-10">
                      @forelse ($report->images as $item)
                      <a href="{{ url('/storage'.'/'.$item->src) }}" data-rel="lightcase:myCollection">
                        <img class="img-fluid rounded" src="{{ url('/storage'.'/'.$item->src) }}" width="150px;">
                      </a>
                      @empty
                      <span class="form-control">Tidak ada gambar</span>
                      @endforelse
                    </div>
                  </div>
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
<script type="text/javascript" src="{{ asset('vendor/lightcase/js/lightcase.js')}}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $(document).ready(function() {
    $('a[data-rel^=lightcase]').lightcase();
  });
</script>

@endsection