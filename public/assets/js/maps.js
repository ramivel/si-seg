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
    markers.clearLayers();
    document.getElementById("coordenadas").value = "";
}

function redimensionar(){
  L.Util.requestAnimFrame(myMap.invalidateSize,myMap,!1,myMap._container);
}

