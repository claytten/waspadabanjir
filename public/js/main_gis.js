
"use strict"


/*
* Table of Contents
*
* 1. Klaten State
* 2. Singleton State
* 3. Searching Place
* 4. Start Drawing Button
* 5. Undo Drawing Button
* 6. Finish Button
* 7. Clearing Maps
* 8. Start Drawing Maps
* 9. Event KeyDown
* 10.Event Listeners
* 11.DOM & AJAX
*/
let url = window.location.origin + "/";
let maps = L.map(
  'mapid', 
  {
    fullscreenControl: true,
    center: new L.LatLng(-7.694512268978755, 110.67233986914458),
    zoom: 12
  }
);
L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',{
    maxZoom: 60,
    subdomains:['mt0','mt1','mt2','mt3']
}).addTo(maps);

/*
* Singleton Variable
*/
let startPolylineFlag = false;
let polyline = undefined;
let pols = [];
let polygon = undefined;
let helpLine = undefined;
let helpPolygon = undefined;
let color = undefined;
let firstPoint = L.circleMarker();
let drawingState = false;

// Start Drawing Button
const startDrawingButton = L.easyButton({
  id: 'start-drawing-button',
  states: [{
    icon: 'fa fa-pen',
    title: 'Mulai Menggambar',
    stateName: 'start-polyline',
    onClick: (btn, map) => {
      btn.button.style.backgroundColor = "#f00";
      btn.button.style.color = "#fff";
      document.getElementById("mapid").style.cursor = "crosshair";

      btn.state('cancel-polyline');
      drawingState = true;
    }
  }, {
    icon: 'fa fa-times',
    title: 'Batalkan Menggambar',
    stateName: 'cancel-polyline',
    onClick: (btn, map) => {
      btn.button.style.backgroundColor = "#fff";
      btn.button.style.color = "#000";
      document.getElementById("mapid").style.cursor = "grab";

      btn.state('start-polyline');
      cancelPolyline();

    }
  }]
});
startDrawingButton.addTo(maps);

// Undo Drawing Button
const undoButton = L.easyButton({
  id: 'undo-polyline',
  states: [{
    icon: 'fa fa-undo',
    ttle: 'Batalkan titik terakhir',
    stateName: 'undo-polyline',
    onClick: (btn, map) => {
      undoPoint();
    }
  }]
});
undoButton.addTo(maps);
undoButton.disable();

const undoPoint = () => {
  if(!drawingState) return;
  if(pols.length == 0) return;

  pols.pop();

  polyline.setLatLngs(pols);
  helpPolygon.setLatLngs(pols);

  if(!validateArea()){
    finishButton.disable();
  }

  if(pols.length == 0){
    finishPolyline();
    undoButton.disable();
  }
}

// Finish Button
const finishButton = L.easyButton({
  id: 'finish-polyline',
  states: [{
    icon: 'fas fa-map',
    title: 'Selesai Menggambar',
    stateName: 'finish-polyline',
    onClick: (btn, map) => {
      drawingState = true;
      drawArea();
      finishButton.disable();
      undoButton.disable();
      startDrawingButton.disable();
    }
  }]
});
finishButton.addTo(maps);
finishButton.disable();

// Searching Place
const searchControl = new L.esri.Controls.Geosearch().addTo(maps);
const results = new L.LayerGroup().addTo(maps);
searchControl.on('results', function(data){
  results.clearLayers();
});
setTimeout(function(){$('.pointer').fadeOut('slow');},3400);

const cancelPolyline = () => {
  if(polyline === undefined) return;

  removeMapLayers();
  finishPolyline();
}

const finishPolyline = () => {
  removeMapLayers();

  startPolylineFlag = false;
  pols = [];
  polygon = undefined;
  polyline = undefined;
  helpLine = undefined;
  helpPolygon = undefined;

  finishButton.disable();
  undoButton.disable();
}

// Clearing maps
const removeMapLayers = () => {
  maps.removeLayer(polyline);
  maps.removeLayer(helpLine);
  maps.removeLayer(helpPolygon);
  maps.removeLayer(firstPoint);
}

// Start Drawing Action
const onMapClick = (e) => {
  if(!drawingState) return;

  if(startPolylineFlag != true){
    startPolyline(e.latlng);
    pols.push([e.latlng["lat"], e.latlng["lng"]]);
    polyline = L.polyline(pols, {
      color: '#ee3'
    }).addTo(maps);
  } else {
    pols.push([e.latlng["lat"], e.latlng["lng"]]);
    polyline.addLatLng(e.latlng);
    undoButton.enable();

    if(validateArea()){
      drawHelpArea();
      finishButton.enable();
    }
  }
}
const onMapMouseMove = (e) => {
  if(!drawingState || pols.length < 1) return;

  let latlangs = [pols[pols.length - 1], [e.latlng.lat, e.latlng.lng]];

  if (helpLine) {
    helpLine.setLatLngs(latlangs);
  } else {
    helpLine = L.polyline(latlangs, {
      color: 'grey',
      weight: 2,
      dashArray: '7',
      className: 'help-layer'
    });
    helpLine.addTo(maps);
  }
}

const startPolyline = (latlang) => {
  placeFirstPoint(latlang);
  startPolylineFlag = true;
}

const placeFirstPoint = (latlang) => {
  let icon = L.divIcon({
    className: 'first-point',
    iconSize: [10,10],
    iconAnchor: [5,5]
  });

  firstPoint = L.marker(latlang, {icon, icon});
  firstPoint.addTo(maps);
  firstPoint.on('click', () => {
    if(validateArea()) {
      drawArea();
    }
  })
}

const validateArea = () => {
  console.log(pols);
  if(pols.length > 2) {
    return true;
  }
  return false;
}


const drawArea = () => {
  if(polyline === undefined) return;
  if(!validateArea()) return;

  drawingState = false;
  
  let randCol = '#' + (function co(lor){   return (lor +=
    [0,1,2,3,4,5,6,7,8,9,'a','b','c','d','e','f'][Math.floor(Math.random()*16)])
    && (lor.length == 6) ?  lor : co(lor); })('');

  polygon = L.polygon([pols], {
    color: randCol,
    fillOpacity: 0.4
  }).addTo(maps);
  let polygonGeoJSON = polygon.toGeoJSON(15);
  $(`
  <form id="mapsForm" action="${urlPOST}" method="POST" enctype='multipart/form-data'>
  <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
  <div class="card-wrapper">
    <div class="card">
      <div class="row">
        <div class="col-lg-12">
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col-lg-8 col-md-6">
                <h3 class="mb-0" id="form-map-title">Buat Data Peta</h3>
              </div>
              <div class="col-lg-4 col-md-6 d-flex justify-content-end">
                <button type="button" class="btn btn-danger" onclick="cancelArea()">Batalkan</button>
                <button type="button" class="btn btn-warning" onclick="resetForm()" >Atur Ulang</button>
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
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fas fa-book-dead"></i></span>
                        </div>
                        <input type="number" id="deaths" class="form-control" name="deaths" placeholder="Jumlah korban yang meninggal" step="1" min="0">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fas fa-user-injured"></i></span>
                        </div>
                        <input type="number" id="injured" class="form-control" name="injured" placeholder="Jumlah korban yang mengalami luka kecil/berat" step="1" min="0">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fas fa-user-slash"></i></span>
                        </div>
                        <input type="number" id="losts" class="form-control" name="losts" placeholder="Jumlah korban yang hilang" step="1" min="0">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12 btn-date-out-field">
                    <div class="row">
                      <div class="col-md-8">
                        <div class="form-group">
                          <div class="input-group input-group-merge">
                            <div class="input-group-prepend">
                              <span class="input-group-text"><i class="fa fa-calendar" aria-hidden="true"></i></span>
                            </div>
                            <input class="form-control" placeholder="Pilih tanggal awal kejadian" type="text" name="date_in" id="date_in" onchange="setDateOut()">
                          </div>
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="form-group">
                          <div class="input-group input-group-merge">
                            <div class="input-group-prepend">
                              <span class="input-group-text"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                            </div>
                            <input class="form-control" type="time" value="00:00:00" id="example-time-input" name="date_in_time">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <button type="button" class="btn btn-info" data-toggle="tooltip" data-placement="top" title="Button ini berfungsi mengaktifkan tanggal berakhir banjir atau tidak" onclick="setDateOutField()" id="btn-set-date-out">Atur tanggal berakhirnya banjir</button>
                    </div>
                  </div>
                  <div class="col-md-12 btn-detail-locations">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal-add-address">Tambahkan Rincian Lokasi</button>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <div class="input-group input-group-merge">
                        <div class="input-group-prepend">
                          <span class="input-group-text"><i class="fas fa-info"></i></span>
                        </div>
                        <textarea class="form-control" placeholder="Kronologi" name="description" id="description"></textarea>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12" id="status-form">
                    <div class="form-group row">
                      <label for="example-text-input" class="col-md-2 col-form-label form-control-label">Status Penerbitan</label>
                      <div class="col-md-10">
                        <select name="status" id="status" class="form-control" data-toggle="select" onchange="statusAction()">
                          <option value=""></option>
                          <option value="1">Terbitkan</option>
                          <option value="0">Draft</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <div class="dropzone dropzone-multiple" data-toggle="dropzone" data-dropzone-multiple data-dropzone-url="#">
                        <div class="fallback">
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="customFileUploadMultiple" multiple>
                            <label class="custom-file-label" for="customFileUploadMultiple">Choose file</label>
                          </div>
                        </div>
                        <ul class="dz-preview dz-preview-multiple list-group list-group-lg list-group-flush">
                          <li class="list-group-item px-0">
                            <div class="row align-items-center">
                              <div class="col-auto">
                                <div class="avatar">
                                  <img class="avatar-img rounded" src="..." alt="..." data-dz-thumbnail>
                                </div>
                              </div>
                              <div class="col ml--3">
                                <h4 class="mb-1" data-dz-name>...</h4>
                                <p class="small text-muted mb-0" data-dz-size>...</p>
                              </div>
                              <div class="col-auto">
                                <div class="dropdown">
                                  <a href="#" class="dropdown-ellipses dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fe fe-more-vertical"></i>
                                  </a>
                                  <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item" data-dz-remove>
                                      Remove
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </li>
                        </ul>
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
  </form>
  `).appendTo($("#formtable")).slideDown("slow", "swing");
  $('#btn-set-date-out').prop('disabled', true).tooltip();
  $('#mapsForm').append(`<input type="text" class="form-control" id="area" name="area" style="display:none">`);
  $('#mapsForm').append('<input type="text" class="form-control" id="color" name="color" style="display:none" value="'+ randCol +'">');
  $('#status').select2({
    'placeholder': 'Select Status',
  });
  $('#date_out').select2({
    'placeholder': 'Select Date Out'
  });
  $('#area').val(JSON.stringify(polygonGeoJSON.geometry.coordinates));

  (function() {
    const $dropzone = $('[data-toggle="dropzone"]');
    const $dropzonePreview = $('.dz-preview');
  
    function init($this) {
      const multiple = ($this.data('dropzone-multiple') !== undefined) ? true : false;
      const preview = $this.find($dropzonePreview);
      let currentFile = undefined;
  
      // Init options
      const options = {
        url: $this.data('dropzone-url'),
        autoProcessQueue: false,
        uploadMultiple: true,
        thumbnailWidth: null,
        thumbnailHeight: null,
        previewsContainer: preview.get(0),
        previewTemplate: preview.html(),
        maxFiles: (!multiple) ? 1 : null,
        acceptedFiles: (!multiple) ? 'image/*' : null,
        init: function(e) {
          this.on("addedfile", function(file) {
            if (!multiple && currentFile) {
              this.removeFile(currentFile);
              console.log('this is while remove item ', file)
            }
            currentFile = file;
            $('#mapsForm').append(`<input type="file" class="custom-file-input images" id="${file.upload.uuid}" name="images[]" style="display:none">`);
            const datTransfer = new DataTransfer();
            const fileInput = document.getElementById(file.upload.uuid);
            datTransfer.items.add(file);
            fileInput.files = datTransfer.files;
          });

          this.on("removedfile", function(file) {
            $('#'+file.upload.uuid).remove();
          });
        }
      }
      // Clear preview html
      preview.html('');
      // Init dropzone
      $this.dropzone(options)
    }
  
    function globalOptions() {
      Dropzone.autoDiscover = false;
    }
    if ($dropzone.length) {
      // Set global options
      globalOptions();
      // Init dropzones
      $dropzone.each(function() {
        init($(this));
      });
    }
  
  
  })();

  (function() {
    const $datepicker = $('#date_in');

    function init($this) {
      const options = {
        disableTouchKeyboard: true,
        autoclose: false
      };
  
      $this.datepicker(options);
    }
  
  
    // Events
    if ($datepicker.length) {
      $datepicker.each(function() {
        init($(this));
      });
    }
  
  })();
}

const cancelArea = () => {
  drawingState = true;
  maps.removeLayer(polygon);
  finishButton.enable();
  undoButton.enable();
  startDrawingButton.enable();
  $('#mapsForm').remove();
}

const drawHelpArea = () => {
  if(polyline === undefined) return;
  if(!validateArea()) return;

  if (helpPolygon) {
    helpPolygon.setLatLngs(pols);
  } else {
    helpPolygon = L.polygon([pols], {
      color: '#ee0',
      stroke: false,
      className: 'help-layer'
    });
    helpPolygon.addTo(maps);
  }
}

// Event KeyDown
const onKeyDownEnter = () => {
  drawArea();
  finishButton.disable();
  undoButton.disable();
  startDrawingButton.disable();
}
const onKeyDownEscape = () => {
  cancelPolyline();
}

// DOM & AJAX

// Event Listeners
maps.on('click', onMapClick);
maps.addEventListener('mousemove', onMapMouseMove);
document.onkeydown = (e) => {
  if(!drawingState) return;

  switch(e.keyCode) {
    case 13: onKeyDownEnter(); break;
    case 27: onKeyDownEscape(); break;
  }
};

L.geoJSON(getGeoJSONData(), {
  style: function(feature){
    return {color: feature.properties.color}
  },
  onEachFeature: onEachFeatureCallback
}).addTo(maps);