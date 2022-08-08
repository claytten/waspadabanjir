"use strict"
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
let polygon = [];

const refreshButton = L.easyButton({
  id: 'refresh-view-button',
  states: [{
      icon: 'fas fa-sync-alt',
      title: 'Refresh',
      stateName: 'refresh-view',
      onClick: async () => {
      $('#loading').show();
      await maps.eachLayer(function (layer) {
          if (!!layer.toGeoJSON) {
          maps.removeLayer(layer);
          }
      });
      await L.geoJSON(getGeoJSONData(), {
          style: function(feature){
          return {color: feature.properties.color}
          },
          onEachFeature: onEachFeatureCallback
      }).addTo(maps).openPopup();
      $('#loading').fadeOut();
      }
  }]
}).addTo(maps);

// Searching Place
const searchControl = new L.esri.Controls.Geosearch().addTo(maps);
const results = new L.LayerGroup().addTo(maps);
searchControl.on('results', function(data){
  results.clearLayers();
});
setTimeout(function(){$('.pointer').fadeOut('slow');},3400);

const getGeoJSONData = () => {
  let data;

  $.ajax({
      headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: window.location.protocol + '//' + window.location.host,
      type: 'GET',
      async: false,
      cache: false,
      error: function (xhr, status, error) {
        console.log(xhr.responseText);
      },
      success: function(response){
        data = response.data;
      }
  });

  return data;
}

const getPopupContent = (field) => {
  return `
    <table class="pop-table">
      <tr>
        <th>Jumlah Korban</th>
        <td>: ${field.total_victims} orang</td>
      </tr>
      <tr>
        <th>Tanggal Awal Kejadian</th>
        <td>: ${field.date_in_time} WIB, ${field.date_in}</td>
      </tr>
      <tr>
        <th>Tanggal Akhir Kejadian</th>
        <td>: ${(field.date_out === false ? 'Sedang Berlangsung' : field.date_out_time + ' WIB, '+ field.date_out)}</td>
      </tr>
      <tr>
        <th>Jumlah Kelurahan yang terdampak</th>
        <td>: ${field.total_village} Kelurahan</td>
      </tr>
      <tr>
        <th>Untuk lebih jelas</th>
        <td>: <a href="${field.url}">Klik Disini</a></td>
      </tr>
    </table>
  `
}

const onEachFeatureCallback = (feature, layer) => {
  if (feature.properties && feature.properties.popupContent) {
    let { id, total_victims, total_village,date_in, date_in_time, date_out, date_out_time, url } = feature.properties.popupContent;
    let content = {id, total_victims, total_village, date_in, date_in_time, date_out, date_out_time, url};

    var helpPolygon = layer.bindPopup(getPopupContent(content));

    polygon.push(helpPolygon);
  }
}

var geoJsonLayer = L.geoJSON(getGeoJSONData(), {
  style: function(feature){
    return {
      color: feature.properties.color
    }
  },
  onEachFeature: onEachFeatureCallback
}).addTo(maps);

const showPath = (id) => {
  for (var i in polygon){
    var polygonID = polygon[i].feature.properties.popupContent.id;
    if (polygonID == id){
      maps.fitBounds(polygon[i].getBounds());
      polygon[i].openPopup();
    };
  }
}

let counting = 0;
geoJsonLayer.eachLayer(function(layer) {
  console.log(layer);
  let item = layer.feature.properties.popupContent;
  counting += 1;
  $('#tBodyField').append(`
    <tr>
      <td>
        <b>${counting}</b>
      </td>
      <td>
        <p>${item.address}</p>
      </td>
      <td>
        <span class="text-muted">${item.date_in}, Pukul ${item.date_in_time}</span>
      </td>
      <td>
        <span class="text-muted">${(item.date_out === false ? 'Sedang Berlangsung' : item.date_out_time + ' WIB, '+ item.date_out)}</span>
      </td>
      <td class="table-actions">
        <button type="button" class="waves-effect waves-light btn tooltipped modal-close" data-position="top" data-tooltip="Tunjuk Arah Pada Peta" onclick="showPath('${item.id}')">
          <i class="material-icons">navigation</i>
        </button>
        <a href="${item.url}" class="waves-effect waves-light btn tooltipped" data-position="top" data-tooltip="Tampilkan Data Banjir Lebih Jelas">
          <i class="material-icons">open_in_new</i>
        </a>
      </td>
    </tr>
  `);
});