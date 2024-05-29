var myMap = L.map('mi-map').setView([-17.5, -65.5], 7)

L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{
  maxZoom: 18,
  subdomains:['mt0','mt1','mt2','mt3']
}).addTo(myMap);

var marker;
var markers = L.layerGroup().addTo(myMap);

myMap.doubleClickZoom.disable();
myMap.on('dblclick', e => {
  var latLng = myMap.mouseEventToLatLng(e.originalEvent);
  var lat = latLng.lat;
  var lng = latLng.lng;

  marker = L.marker([lat, lng]);
  markers.addLayer(marker);
  myMap.addLayer(markers);
  actualizarInputCoordenadas(lat, lng);
});

var geojsonFeature = {
  "type": "Feature",
  "properties": {
      "name": "Coors Field",
      "amenity": "Baseball Stadium",
      "popupContent": "This is where the Rockies play!"
  },
  "geometry": {
      "type": "Point",
      "coordinates": [-104.99404, 39.75621]
  }
};
var campus = {
  "type": "Feature",
  "properties": {
      "popupContent": "This is the Auraria West Campus",
      "style": {
          weight: 2,
          color: "#999",
          opacity: 1,
          fillColor: "#B0DE5C",
          fillOpacity: 0.8
      }
  },
  "geometry": {
      "type": "MultiPolygon",
      "coordinates": [
          [
              [
                  [-105.00942707061768, 39.73989736613708],
                  [-105.00942707061768, 39.73910536278566],
                  [-105.00685214996338, 39.73923736397631],
                  [-105.00384807586671, 39.73910536278566],
                  [-105.00174522399902, 39.73903936209552],
                  [-105.00041484832764, 39.73910536278566],
                  [-105.00041484832764, 39.73979836621592],
                  [-105.00535011291504, 39.73986436617916],
                  [-105.00942707061768, 39.73989736613708]
              ]
          ]
      ]
  }
};

L.geoJSON(campus).addTo(myMap);


function agregarPunto(){
  var latitude = document.getElementById("latitude").value;
  var longitude = document.getElementById("longitude").value;

  if(validateLatitude(latitude) && validateLongitude(longitude)){
    var latlng = L.latLng(latitude, longitude);
    L.marker(latlng).addTo(myMap);
    myMap.setView(latlng, 15);
    actualizarInputCoordenadas(latitude, longitude);
    document.getElementById("latitude").value = "";
    document.getElementById("longitude").value = "";
  }else{
    alert('La Latitud y Longitud no son correctos.');
  }
}
function validateLatitude(lat) {
  var regexLat = new RegExp('^(\\+|-)?(?:90(?:(?:\\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\\.[0-9]{1,6})?))$');
  return regexLat.test(lat);
}

function validateLongitude(lon) {
  var regexLong = new RegExp('^(\\+|-)?(?:180(?:(?:\\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\\.[0-9]{1,6})?))$');
  return regexLong.test(lon);
}

function actualizarInputCoordenadas(lat, lon){
  var inputCoordenadas = document.getElementById("coordenadas").value;
  inputCoordenadas = inputCoordenadas + "("+lat+"|"+lon+")";
  document.getElementById("coordenadas").value = inputCoordenadas;
}

function limpiarCoordenadas(){
    //markers.clearLayers();
    myMap.eachLayer((layer) => {
      if (layer instanceof L.Marker)
         layer.remove();
    });
    document.getElementById("coordenadas").value = "";
}

function redimensionar(){
  L.Util.requestAnimFrame(myMap.invalidateSize,myMap,!1,myMap._container);
}

