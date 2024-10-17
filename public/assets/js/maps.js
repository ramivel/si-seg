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
  var latitude = parseFloat(document.getElementById("latitude").value);
  var longitude = parseFloat(document.getElementById("longitude").value);
  var validacion = true;

  if (isNaN(latitude) || latitude > 90 || latitude < -90) {
    alert("La Latitud no es correcta.");
    validacion = false;
    return;
  }

  if (isNaN(longitude) || longitude > 180 || longitude < -180) {
    alert("La Longitud no es correcta.");
    validacion = false;
    return;
  }

  if(validacion){
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

function agregarPuntoUTM(){
  var zona = parseInt(document.getElementById("zona_utm").value);
  var southern = parseFloat(document.getElementById("hemisferio_utm").selectedIndex) == 0;
  var este = parseFloat(document.getElementById("este_utm").value);
  var norte = parseFloat(document.getElementById("norte_utm").value);
  var validacion = true;

  if (isNaN(este) || isNaN(norte)) {
      alert("Tanto el este como el norte deben ser números de punto flotante válidos.");
      validacion = false;
      return;
  }

  if (isNaN(zona)) {
      alert("La zona debe ser un número entero válido.");
      validacion = false;
      return;
  }

  if (zona < 1 || zona > 60) {
      alert("La zona de longitud debe estar entre 1 y 60.");
      validacion = false;
      return;
  }

  if (norte < 0 || norte > 10000000) {
      alert("El norte debe estar entre 0 y 10000000");
      validacion = false;
      return;
  }

  if (este < 160000 || este > 834000) {
      alert("La coordenada este cruza los límites de la zona.");
      validacion = false;
      return;
  }

  if(validacion){
    var item = L.utm({x: este, y: norte, zone: zona, southHemi: southern});
    var coord = item.latLng();
    L.marker(coord).addTo(myMap);
    myMap.setView(coord, 15);
    actualizarInputCoordenadas(coord.lat, coord.lng);
    document.getElementById("zona_utm").value = "";
    document.getElementById("este_utm").value = "";
    document.getElementById("norte_utm").value = "";
  }
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

