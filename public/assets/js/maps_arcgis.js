require([
    "esri/layers/VectorTileLayer", "esri/Map", "esri/Basemap", "esri/widgets/BasemapToggle", "esri/views/SceneView",
    "esri/widgets/Search", "esri/widgets/CoordinateConversion"
  ], (VectorTileLayer, Map, Basemap, BasemapToggle, SceneView, Search, CoordinateConversion,) => {
    // Create a VectorTileLayer from a style URL
    const mapBaseLayer = new VectorTileLayer({
      url: "https://arcgis.com/sharing/rest/content/items/b5676525747f499687f12746441101ef/resources/styles/root.json"
    });
    const customBasemap = new Basemap({
      baseLayers: [mapBaseLayer],
      title: "Terrain",
      id: "terrain",
      thumbnailUrl: "https://arcgis.com/sharing/rest/content/items/b5676525747f499687f12746441101ef/info/thumbnail/ago_downloaded.png"
    });
    const map = new Map({
      basemap: "satellite",
      ground: "world-elevation"
    });
    const initCamera = {
      heading: 124.7,
      tilt: 82.9,
      position: {
        latitude: -15.058437,
        longitude: -68.195981,
        z: 1990
      }
    };
    const view = new SceneView({
      container: "mi-map",
      map: map,
      camera: initCamera
    });
    const searchWidget = new Search({
      view: view
    });
    const ccWidget = new CoordinateConversion({
      view: view
    });
    view.when(() => {
      view.ui.add(searchWidget, {
      position: "top-right"
      });
      const toggle = new BasemapToggle({
        visibleElements: {
          title: true
        },
        view: view,
        nextBasemap: customBasemap
      });
      view.ui.add(toggle, "top-right");
      //view.ui.add(ccWidget, "bottom-left");
    });
  });