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
      }).addTo(maps);
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
      url: getURL,
      type: 'GET',
      async: false,
      cache: false,
      error: function (xhr, status, error) {
      console.log(xhr.responseText);
      },
      success: function(response){
      data = response.data;
      console.log(data);
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
        <td>: <a href="${url}maps/${field.id}/show">Klik Disini</a></td>
      </tr>
    </table>
  `
}

const onEachFeatureCallback = (feature, layer) => {
  if (feature.properties && feature.properties.popupContent) {
    let { id, total_victims, total_village,date_in, date_in_time, date_out, date_out_time } = feature.properties.popupContent;
    let content = {id, total_victims, total_village, date_in, date_in_time, date_out, date_out_time};

    layer.bindPopup(getPopupContent(content));
  }
}

L.geoJSON(getGeoJSONData(), {
  style: function(feature){
      return {color: feature.properties.color}
  },
  onEachFeature: onEachFeatureCallback
}).addTo(maps);