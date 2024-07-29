var myMap = L.map('mi-map').setView([-17.5, -65.5], 6);
var marker;
var markers = L.layerGroup().addTo(myMap);
var municipioLayer = L.geoJSON().addTo(myMap);
const colorMunicipioPoligono = "#378AFE";
const styleMunicipio = {
  "color": colorMunicipioPoligono,
  "weight": 3,
  "opacity": 0.25
};
var areasMinerasLayer = L.geoJSON().addTo(myMap);
const colorAreasMinerasPoligono = "#C5CA00";
const styleAreasMineras = {
  "color": colorAreasMinerasPoligono,
  "weight": 3,
  "opacity": 0.25
};

L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}',{
  maxZoom: 18,
  subdomains:['mt0','mt1','mt2','mt3']
}).addTo(myMap);

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

const legend = L.control.Legend({
  position: "bottomleft",
  title: "Capas",
  opacity: 0.75,
  symbolWidth: 30,
  legends: [
    {
      label: " Municipio",
      type: "polygon",
      sides: 4,
      color: "#000",
      fillColor: colorMunicipioPoligono,
      layers: municipioLayer,
    },
    {
      label: "Área(s) Minera(s)",
      type: "polygon",
      sides: 4,
      color: "#000",
      fillColor: colorAreasMinerasPoligono,
      layers: areasMinerasLayer,
    },
  ]
})
.addTo(myMap);

function onEachFeature(feature, layer) {
  let popupContent = `<p>I started out as a GeoJSON ${feature.geometry.type}, but now I'm a Leaflet vector!</p>`;

  if (feature.properties && feature.properties.popupContent) {
    popupContent += feature.properties.popupContent;
  }

  layer.bindPopup(popupContent);
}

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

function agregarPoligonoMunicipio(geometry){
  municipioLayer.clearLayers();
  var municipioPoligono = {
    "type": "Feature",
    "geometry": geometry,
  };
  municipioLayer.addData(municipioPoligono);
  municipioLayer.setStyle(styleMunicipio);
  //municipioLayer.bindTooltip(nombre, {permanent: true, direction: "center", className: "my-labels"});
}

function actualizarAreasMinerasLayer(){
  areasMinerasLayer.clearLayers();
  let areasMineras = document.getElementsByName('id_areas_mineras[]');
  areasMineras.forEach( function(areaMinera, indice) {
    $.ajax({
      url: "/garnet/mineria_ilegal/ajax_datos_area_minera_mineria_ilegal",
      type: "POST",
      data: {
        id: areaMinera.value,
      },
      dataType: "json",
      success: function (result) {
        if(result.estado == 'success' && result.poligono)
          agregarPoligonoAreasMineras($.parseJSON(result.poligono), result.codigo_unico);
        else
          console.log(result);
      },
    });
  });
  
}

function agregarPoligonoAreasMineras(geometry, label){
  var areaMineraPoligono = {
    "type": "Feature",
    "properties": {
        "name": label,
    },
    "geometry": geometry,
  };
  areasMinerasLayer.addData(areaMineraPoligono);
  areasMinerasLayer.setStyle(styleAreasMineras);
  areasMinerasLayer.eachLayer(function (layer) {
    layer.bindTooltip(layer.feature.properties.name, {permanent: true, direction: "center", className: "my-labels"});
  });
}

