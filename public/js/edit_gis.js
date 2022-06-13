
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
let polylayer = undefined;
let randCol = '#' + (function co(lor){   return (lor +=
    [0,1,2,3,4,5,6,7,8,9,'a','b','c','d','e','f'][Math.floor(Math.random()*16)])
    && (lor.length == 6) ?  lor : co(lor); })('');

// Searching Place
const searchControl = new L.esri.Controls.Geosearch().addTo(maps);
const results = new L.LayerGroup().addTo(maps);
searchControl.on('results', function(data){
  results.clearLayers();
});
setTimeout(function(){$('.pointer').fadeOut('slow');},3400);

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
        console.log(btn);
        btn.state('cancel-polyline');
        drawingState = true;
        maps.removeLayer(polylayer);
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
        polylayer.addTo(maps);
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

  startDrawingButton.enable();
  startDrawingButton.state('start-polyline');
  startDrawingButton.button.style.backgroundColor = "#fff";
  startDrawingButton.button.style.color = "#000";
  document.getElementById("mapid").style.cursor = "grab";
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


const drawArea = async () => {
  if(polyline === undefined) return;
  if(!validateArea()) return;

  drawingState = false;
  polygon = L.polygon([pols], {
    color: randCol,
    fillOpacity: 0.4
  }).addTo(maps);

  await Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!',
    allowOutsideClick: false,
    allowEscapeKey: false,
  }).then((result) => {
    if (result.isConfirmed) {
      updateGeoJSONData(randCol);
    }
    if (result.isDismissed) {
      cancelArea();
    }
  });

  drawingState = true;
  finishPolyline();
}
  

const cancelArea = () => {
  drawingState = true;
  maps.removeLayer(polygon);
  finishButton.enable();
  undoButton.enable();
  startDrawingButton.enable();
  startDrawingButton.state('cancel-polyline');
  polylayer.addTo(maps);
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
}
const onKeyDownEscape = () => {
  cancelPolyline();
}

// DOM & AJAX
const getGeoJSONData = () => {
  let data;

  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: linkEdit,
    type: 'GET',
    async: false,
    cache: false,
    error: function (xhr, status, error) {
      console.log(xhr.responseText);
    },
    success: function(response){
      data = response.data;
      console.log('this is response', response, url);
    }
  });

  return data;
}
const onEachFeatureCallback = (feature, layer) => {
    console.log(feature);
    if (feature.properties) {
        polygon = L.polygon([feature.geometry.coordinates], {
            color: feature.properties.color,
            fillOpacity: 0.4
        });
        
        // const setCenter = polygon.getBounds().getCenter();
        // maps.panTo(new L.LatLng(setCenter.lng, setCenter.lat));
    }
}

const updateGeoJSONData = (color) => {
  let polygonGeoJSON = polygon.toGeoJSON(15);
  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: link,
    type: 'PUT',
    async: false,
    cache: false,
    data: {
      color,
      area : JSON.stringify(polygonGeoJSON.geometry.coordinates)
    },
    error: function (xhr, status, error) {
      console.log(xhr.responseText);
    },
    success: function(response){
      console.log(response);
      if (response.status === 'success') {
        console.log('updateGeoJSON', polygon);
        polylayer = polygon;
        Swal.fire('Saved!', '', 'success');
      } else {
        maps.removeLayer(polygon);
        polylayer.addTo(maps);
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'Something went wrong!'
        });
      }
    }
  });
}

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
polylayer = L.geoJSON(getGeoJSONData(), {
  style: function(feature){
    return {color: feature.properties.color}
  },
  onEachFeature: onEachFeatureCallback
}).addTo(maps);

maps.fitBounds(polylayer.getBounds());