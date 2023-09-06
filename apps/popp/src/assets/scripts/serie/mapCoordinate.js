    
import {Tile as TileLayer, Vector as VectorLayer} from 'ol/layer';
import {OSM, Vector as VectorSource} from 'ol/source';
import View from 'ol/View';
import 'ol/ol.css';
import Map from 'ol/Map';
import {fromLonLat}  from 'ol/proj';
import MousePosition from 'ol/control/MousePosition';
import DrawInteraction from 'ol/interaction/Draw';
import { PARAMETRES } from '../custom/parametre';

var layersLocal = [];
PARAMETRES.layers.map((layer, index) => {
    var url = PARAMETRES.url + layer.fileJsonPath;
    var famillesPaysagesSource = new VectorSource({
      url: url,
      format: new GeoJSON()
    });
    //var layersLocal = [];

    layersLocal.push(
        new VectorLayer({
            name:'familleLayer' + index,
            source: famillesPaysagesSource,
            style: styleFamillesPaysagesFunction
        })
    );
    layersLocal.push(
        new VectorLayer({
            minZoom:9,
            name:'familleLayerLibelle' + index,
            source: famillesPaysagesSource,
            style: (function(feature) {
                var style = new Style({
                    text: new Text({
                        text :feature.get(layer.fileJsonTextField),
            
                        fill : new Fill({
                        color: 'grey'
                        }),
                        stroke : new Stroke({
                        color: 'rgba(0, 0, 0, 0.6)',
                        width: 1
                        }),
                        font : '15px Normal'
                    })
                });
                return style;
                    
            })
        })
    );
})

var famillesPaysagesVector = new LayerGroup({
    title: PARAMETRES.titleJsonLayer,
    layers: layersLocal
  });

  
function styleFamillesPaysagesFunction(feature, resolution) {
  var style = new Style({
      stroke: new Stroke({
        color: 'grey',
        width: 0.7
      }),
      fill: new Fill({
        color: 'rgba(0, 0, 0, 0)'
      })
  });
  return style;
}


var groupLayers =  [
  new TileLayer({
      title: 'Stamen',
      type: 'base',
      source: new Stamen({
          layer: 'watercolor'
      })
  }),
  new TileLayer({
      source : PARAMETRES.sourceLayerSat,
      title: 'Orthophoto',
      type: 'base',
  }),
  new TileLayer({
      title: 'OSM',
      type: 'base',
      source: new OSM()
  }),
  //groupeLayersLocal,
  famillesPaysagesVector
];

var map = new Map({
  target: 'mapSerieCoordinate',
  layers: groupLayers,
  /* [
    new TileLayer({
      source: new OSM()
    })
  ],*/
  view: new View({
    center: fromLonLat([-1.6777926, 48.117266]),
    zoom: 7
  })
});

map.addControl(new MousePosition({projection: 'EPSG:3857',}))

map.on('singleclick', function(evt) {
    var coordinates = fromLonLat(evt.coordinate);
    $("#photo_emplacement_longitude").val(coordinates[0]); 
    $("#photo_emplacement_latitude").val(coordinates[1]);
});


var source = new VectorSource();

var vectorSource = new VectorLayer({
  source: source
});

var draw = new DrawInteraction({
    source: vectorSource,
    maxPoints: 1,
    geometryName:"POI",
    freehand:true,
    type: "point",
});

$("#btnPositionnerCoord").focus(function position(){
    map.removeInteraction(draw);                                                                                
    map.addInteraction(draw);
});

