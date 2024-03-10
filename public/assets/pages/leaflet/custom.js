var myMap = L.map('mi-map').setView([-17.5, -65.5], 7)

L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{
  maxZoom: 18,
  subdomains:['mt0','mt1','mt2','mt3']
}).addTo(myMap);

L.Control.geocoder().addTo(myMap);

var marker;
var markers = L.layerGroup().addTo(myMap);

myMap.doubleClickZoom.disable();
myMap.on('dblclick', e => {
  var inputCoordenadas = document.getElementById("coordenadas").value;  

  var latLng = myMap.mouseEventToLatLng(e.originalEvent);
  var lat = latLng.lat;
  var lng = latLng.lng;

  marker = L.marker([lat, lng]);
  markers.addLayer(marker);
  myMap.addLayer(markers);
  
  inputCoordenadas = inputCoordenadas + "("+lat+"|"+lng+")";
  document.getElementById("coordenadas").value = inputCoordenadas;  
});

function limpiarCoordenadas(){
    markers.clearLayers();
    document.getElementById("coordenadas").value = "";
}