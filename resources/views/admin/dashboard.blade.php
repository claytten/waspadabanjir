@extends('layouts.admin.app',[
  'headers' => 'active',
  'menu' => 'dashboard',
  'title' => 'Dashboard',
  'first_title' => 'Dashboard',
  'first_link' => route('admin.dashboard')
])

@section('plugins_css')
<link rel="stylesheet" href="{{ asset('vendor/select2/dist/css/select2.min.css') }}">
@endsection

@section('inline_css')
<style>
  .select2-selection__rendered, .select2-selection__placeholder {
    color: #172b4d !important;
    font-weight: 600 !important;
  }
  .select2.select2-container {
    width: 242.56px !important;
  }
</style>
@endsection

@section('content_body')
  <!-- Card stats -->
  <div class="row">
    <div class="col-xl-3 col-md-6">
      <div class="card card-stats">
        <!-- Card body -->
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h5 class="card-title text-uppercase text-muted mb-0">Jumlah kejadian banjir</h5>
              <span class="h2 font-weight-bold mb-0" id="field-count">0</span>
            </div>
            <div class="col-auto">
              <div class="icon icon-shape bg-gradient-primary text-white rounded-circle shadow">
                <i class="ni ni-map-big"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card card-stats">
        <!-- Card body -->
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h5 class="card-title text-uppercase text-muted mb-0">Jumlah laporan banjir</h5>
              <span class="h2 font-weight-bold mb-0" id="report-count">0</span>
            </div>
            <div class="col-auto">
              <div class="icon icon-shape bg-gradient-red text-white rounded-circle shadow">
                <i class="ni ni-chat-round"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card card-stats">
        <!-- Card body -->
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h5 class="card-title text-uppercase text-muted mb-0">Jumlah kelurahan yang terdampak</h5>
              <span class="h2 font-weight-bold mb-0" id="village-count">0</span>
            </div>
            <div class="col-auto">
              <div class="icon icon-shape bg-gradient-red text-white rounded-circle shadow">
                <i class="fas fa-city"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card card-stats">
        <!-- Card body -->
        <div class="card-body">
          <div class="row">
            <div class="col">
              <h5 class="card-title text-uppercase text-muted mb-0">Jumlah korban meninggal/luka/hilang</h5>
              <span class="h2 font-weight-bold mb-0" id="victim-count">0</span>
            </div>
            <div class="col-auto">
              <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                <i class="ni ni-single-02"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xl-12 col-md-12">
      <div class="card bg-default">
        <div class="card-header bg-transparent">
          <div class="row align-items-center">
            <div class="col">
              <h6 class="text-light text-uppercase ls-1 mb-1">Overview</h6>
              <h5 class="h3 text-white mb-0 header-stat">Statistik Banjir Tahun 2021</h5>
            </div>
            <div class="col">
              <ul class="nav nav-pills justify-content-end">
                <li class="nav-item mr-4 mr-md-2">
                  <select name="fieldStat" id="fieldStat" class="nav-link py-4 px-6" onchange="onChangeYear()">
                    <option value=""></option>
                    @for ($i=1990; $i<2040; $i++)
                      <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                  </select>
                  {{-- <a href="#" class="nav-link py-2 px-3" data-toggle="tab">
                    <span class="d-none d-md-block">Tahun</span>
                    <span class="d-md-none">Y</span>
                  </a> --}}
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="card-body">
          <!-- Chart -->
          <div class="chart">
            <!-- Chart wrapper -->
            <canvas id="chart-sales-dark" class="chart-canvas"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('plugins_js')
<script src="{{ asset('vendor/chart.js/dist/Chart.min.js') }}"></script>
<script src="{{ asset('vendor/chart.js/dist/Chart.extension.js') }}"></script>
<script src="{{ asset('vendor/select2/dist/js/select2.min.js')}}"></script>
@endsection

@section('inline_js')
<script>
  "use strict"
  $('#fieldStat').select2({
    'placeholder': 'Filter Berdasarkan Tahun',
  });
  let fieldsChart = new Chart($('#chart-sales-dark'), {
    type: 'line',
    options: {
      scales: {
        yAxes: [{
          gridLines: {
            color: Charts.colors.gray[700],
            zeroLineColor: Charts.colors.gray[700]
          },
          ticks: {
            beginAtZero: true,
            padding: 5,
          }
        }]
      },
    },
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: [{
        label: 'Kejadian',
        data: [0,0,0,0,0,0,0,0,0,0,0,0]
      }]
    }
  });

  function getFieldData(year) {
    let url = "{{ route('admin.dashboard.countMapEachMonth', ":id") }}";
    url = url.replace(':id', year);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url,
      type : "GET",
      error: function (xhr, status, error) {
        console.log(xhr.responseText);
      },
      success:function(result) {
        console.log(Object.keys(result.chart).map((key) => result.chart[key]));
        fieldsChart.data.datasets[0].data = Object.keys(result.chart).map((key) => result.chart[key]);
        fieldsChart.update();

        $('#field-count').text(result.fieldCount);
        $('#report-count').text(result.reportCount);
        $('#village-count').text(result.villageCount);
        $('#victim-count').text(result.victimCount);
      }
    });
  }

  function onChangeYear() {
    $('.header-stat').text('Statistik Banjir Tahun ' + $('#fieldStat').val());
    getFieldData($('#fieldStat').val());
  }


  $(document).ready(function() {
    // Save to jQuery object
    getFieldData(new Date().getFullYear())
  });
</script>
@endsection