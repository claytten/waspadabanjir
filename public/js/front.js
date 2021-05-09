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
      <table>
      <tr>
          <th>Nama Daerah</th>
          <td>: ${field.name}</td>
      </tr>
      <tr>
          <th>Tanggal</th>
          <td>: ${field.date}, ${field.time}</td>
      </tr>
      <tr>
          <th>Lokasi</th>
          <td>: ${field.locations}</td>
      </tr>
      <tr>
          <th>Detail</th>
          <td>: <a href="${url}maps/${field.id}/show">Klik Disini</a></td>
      </tr>
      </table>
  `
}

const onEachFeatureCallback = (feature, layer) => {
  if (feature.properties && feature.properties.popupContent) {
      let { id, name,locations,date,time, status } = feature.properties.popupContent;
      time = new Date('1970-01-01T' + time + 'Z')
      .toLocaleTimeString({},
      {timeZone:'UTC',hour12:true,hour:'numeric',minute:'numeric'}
      );
      let content = {id, name, locations, date, time, status};

      layer.bindPopup(getPopupContent(content));
  }
}

L.geoJSON(getGeoJSONData(), {
  style: function(feature){
      return {color: feature.properties.color}
  },
  onEachFeature: onEachFeatureCallback
}).addTo(maps);