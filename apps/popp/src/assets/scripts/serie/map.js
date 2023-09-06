import Masonry from 'masonry-layout';
import 'ol/ol.css';
import {Tile as TileLayer, Vector as VectorLayer, Group as LayerGroup} from 'ol/layer';
import {OSM, Vector as VectorSource, Stamen, WMTS} from 'ol/source';
import View from 'ol/View';
import Overlay from 'ol/Overlay';
import Map from 'ol/Map';
import {fromLonLat, get as getProjection}  from 'ol/proj';
import GeoJSON from 'ol/format/GeoJSON';
import Circle from 'ol/geom/Circle';
import {Circle as CircleStyle, Fill, Stroke, Style, Text, RegularShape, Icon} from 'ol/style';
import {defaults as defaultInteractions} from 'ol/interaction.js';
import {singleclick} from 'ol/events/condition';
import {toStringHDMS} from 'ol/coordinate';
import {toLonLat} from 'ol/proj';
import {Zoom, Attribution, defaults as defaultControls} from 'ol/control';
import LayerSwitcher from 'ol-layerswitcher';
import csv2geojson from 'csv2geojson';
import AnimatedCluster from 'ol-ext/layer/AnimatedCluster';

import { PARAMETRES } from '../custom/parametre';

import {createEmpty, extend, getWidth, getHeight, getCenter, getTopLeft} from 'ol/extent';
import Cluster from 'ol/source/Cluster.js';
import Select from 'ol/interaction/Select';
import Interaction from 'ol/interaction';
import 'ol-layerswitcher/src/ol-layerswitcher.css';
import WMTSTileGrid from 'ol/tilegrid/WMTS';
import { ATTRIBUTION } from 'ol/source/OSM';

var geojson = {"type":"Point","coordinates":[0,0]};
//var url = PARAMETRES.url + '/files/carto/ensembles_familles_paysages_4326.json';
var groupeLayersLocal = [];
//var layersLocal = [];
PARAMETRES.layers.map((layer, index) => {
    var url = PARAMETRES.url + layer.fileJsonPath;
    var famillesPaysagesSource = new VectorSource({
      url: url,
      format: new GeoJSON(),
      attributions: layer.attributions? layer.attributions: ""
    });
    var layersLocal = [];

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
    
    groupeLayersLocal.push( 
        new LayerGroup({
            title: layer.titleJsonLayer,
            layers: layersLocal
        })
    )
})

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

 export var photoSource = new VectorSource({
   url: PARAMETRES.url + '/get/series/geojson',
   format: new GeoJSON({extractStyles:false})
 });

var photoCluster = new Cluster({
    distance: 30,
    source: photoSource
});

var clusterLayer = new AnimatedCluster(
{	name: 'ClusterPhotoLayer',
    source: photoCluster,
    animationDuration: 700,
    style: styleFunction
});

var vectorLayer = new VectorLayer({
    name:'photoLayer',
    source: photoCluster,
    style: styleFunction
});


// Style for the clusters
var styleCache = {};
function getStyle (feature, resolution)
{	var size = feature.get('features').length;
    var style = styleCache[size];
    if (!style)
    {	var color = size>25 ? "248, 128, 0" : size>8 ? "248, 192, 0" : "128, 192, 64";
        var radius = Math.max(8, Math.min(size*0.75, 20));
        style = styleCache[size] =
            [ new Style(
                {	image: new CircleStyle(
                    {	radius: radius+2,
                        stroke: new Stroke(
                        {	color: "rgba("+color+",0.3)", 
                            width: 4
                        })
                    })
                }),
                new Style(
                {	image: new CircleStyle(
                    {	radius: radius,
                        fill: new Fill(
                        {	color:"rgba("+color+",0.6)"
                        })
                    }),
                    text: new Text(
                    {	text: size.toString(),
                        fill: new Fill(
                        {	color: '#000'
                        })
                    })
                })
            ];
    }
    return style;
}

photoSource.on('featuresloadend', function () {
    var ext=photoSource.getExtent();
    map.getView().fit(ext , {duration: 1000, padding: [50, 50, 50, 50]});
  });

//----------------------------Cluster-----------------------------------
//--Définition des éléments HTML--
const listPhoto = document.getElementById('listClickMapSeriePhoto');
const listPhotoTitle = document.getElementById('listClickMapSeriePhotoTitle');

//--Définition des styles--
var clusterFill = new Fill({
    color: 'rgba(255, 153, 0, 0.8)'
});
var clusterFillParticipatif = new Fill({
    color: 'rgba(255, 153, 0, 0.8)'
});
var clusterStroke = new Stroke({
  color: 'rgba(255, 204, 0, 0.2)',
  width: 1
});
var textFill = new Fill({
  color: '#fff'
});
var textStroke = new Stroke({
  color: 'rgba(0, 0, 0, 0.6)',
  width: 3
});
var invisibleFill = new Fill({
  color: 'rgba(255, 153, 0, 0.9)',
});

//Réécriture de la fonction du buffer d'OL (bug de celle-ci et ne convenait pas pour une extend d'un point
function buffer(extent, value) {
    return [
        extent[0] - value,
        extent[1] - value,
        extent[0] + value,
        extent[1] + value
    ];
}

//Calcul du cluster
var maxFeatureCount, vector;
function calculateClusterInfo(resolution) {
    maxFeatureCount = 0;
    var viewSerie = 0;
    var features = vectorLayer.getSource().getFeatures();
    var feature, radius;
    for (var i = features.length - 1; i >= 0; --i) {
        feature = features[i];
        var originalFeatures = feature.get('features');
        var extent = createEmpty();
        var j, jj;
        for (j = 0, jj = originalFeatures.length; j < jj; ++j) {
            extend(extent, originalFeatures[j].getGeometry().getExtent());
        }
        var centerCluster = getCenter(extent);
        maxFeatureCount = Math.max(maxFeatureCount, jj);
        var viewSerie = viewSerie + jj;
        var test = buffer(centerCluster, /**(getWidth(extent) + getHeight(extent))**/ (360*jj));
        //console.log(getWidth(extent) + getHeight(extent));
        if (PARAMETRES.clusterType == "sizeByExtent"){
            radius = ( 0.25 * (getWidth(extent) + getHeight(extent))) / resolution;
            if (radius < 10){
                radius = 14
            }
        }else{
            radius = 14 + (jj * 2) ;
            if (radius > 40){
                radius = 60 + ((jj-50) / 2) ;
            }
        }

        feature.set('radius', radius);
    }
}

//Sélection du style en fonction de l'événement déclenché au clic
function selectStyleFunction(feature) {
    listPhotoFct(feature);
    createPopup(feature);
    if (feature.get('features').length == 1){
        var styles = PARAMETRES.getIconSerieStyleSelected(feature)
    }else{
        //var radius = Math.max(8, Math.min(feature.get('features').length*0.75, 20));
        var radius = PARAMETRES.getRadiusStyle(feature.get('features').length);
        var styles = PARAMETRES.getClusterStyleSelected(feature, radius);
    }
    return styles;
}

//Attribution du style : s'il y a cluster, ou entité isolée
var currentResolution;
// Style for the clusters
var styleCache = {};
function styleFunction(feature, resolution) {
    /*calculateClusterInfo(resolution);
    currentResolution = resolution;*/
    var style = styleCache[size];
    //var style;
    var size = feature.get('features').length;
    //S'il y a cluster alors on applique ce style :
    
    if(!style){
        if (size > 1) {
            var radius = PARAMETRES.getRadiusStyle(size);//Math.max(8, Math.min(size*0.75, 20));
            style = styleCache[size] = PARAMETRES.getClusterStyle(feature, radius);
        }
        //Si l'entité est isolé on appelle le style définit par dessus
        else {
            var originalFeature = feature.get('features')[0];
            style = styleCache[size] = PARAMETRES.getIconSerieStyle(originalFeature);
        }
    }
    return style;
}


//Création de la division dédiée à la description de la première série si clic sur un cluster et dédié à la série en question si clic sur série 
function listPhotoFct(feature){
    $("#listClickMapSeriePhoto").html("");
    var serie = feature.values_.features[0].values_;
    if (serie.type == 'local'){
        var classOpp = "bgc-white";
    }else{
        var classOpp = "bgc-light-green-100";
    }

    var divMere = $('<div />', { 
        class: 'p-10 bd '  + classOpp
    });
    divMere.append('<div style="display:flex"><h6 class="c-grey-900">Résultat cartographique => ' + serie.title + '</h6><p class="mL-10"> #OPP ' + serie.type +'</p></div>');
    var divFille = $('<div />', { 
        class: 'mT-10'
    }).appendTo(divMere);
    var divPhotos = $('<div />', { 
        class: 'peers fxw-nw@lg+ ta-c gap-10',
        height: '110px'
    }).appendTo(divFille);
    for (let i = 0; i < serie.photosSerie.length; i++){
        var img = $('<img />', { 
            id: serie.photosSerie[i][0],
            src: PARAMETRES.url + "/files/miniature/" +  serie.photosSerie[i].url,
            alt: serie.titre
        });
        var divPhoto = $('<div />', {class: 'peer'}).append(img);

        divPhoto.append('<div class="c-grey-900">' +  serie.photosSerie[i].date + '</div>');
        divPhoto.append('<hr>');

        $(divPhoto).appendTo(divPhotos);
    }
    var d = document.createElement('div');

    $(d).addClass('masonry-item col-md-12 cur-p btn-outline-success')
        .html(divMere)
        .appendTo($("#listClickMapSeriePhoto")) //main div
        .click(function () {
            location.href = PARAMETRES.url + '/public/get/serie/' + serie.id;
            $(this).remove();
        })
        /*.hide()
        .slideToggle(300)
        .delay(50);*/

  new Masonry('.masonry', {
      itemSelector: '.masonry-item',
      columnWidth: '.masonry-sizer',
      percentPosition: true,
    });
};

//Popup HTML
var container = document.getElementById('popup');
var content = $('#popup-content');
var crossCloser =  $('#popup-closer');

function createPopup(feature){
    content.html('');
    //content.append('<strong>Séries :</strong>');
    var series = feature.values_.features;
    if ((series.length - 1) > 9){
        var length = 9;
    }else{
        var length = series.length - 1;
    }
    for (var i = 0; i <= length; ++i) {
        content.append("<div><a href=" + PARAMETRES.url + "/public/get/serie/" + series[i].values_.id + ">" + series[i].values_.title + "</a></div>"); 
        content.append("<small> " + series[i].values_.commune + "</a></small></div>"); 
    }

    overlay.setPosition(feature.values_.geometry.flatCoordinates);
    PARAMETRES.setOverlayPosition(overlay, feature);
    /*if(feature.values_.features.length == 1){
        overlay.setOffset([5,-30]);
    }else{
        overlay.setOffset([5,-10]);
    }*/
    
    var element = overlay.getElement();
    $(element).popover('dispose');
    $(element).popover({
      container: element,
      placement: 'top',
      animation: true,
      html: true,
      content : content
    });
    $(element).popover('show');
};

//Création de la popup
var overlay = new Overlay({
    element: container,
    autoPan: true,
    positioning: "bottom-left",
    autoPanAnimation: {
        duration: 250
    }
});

crossCloser.click(function() {
    overlay.setPosition(undefined);
    crossCloser.blur();
    return false;
});
var sourceStamen =  new Stamen({
    layer: 'watercolor',
    attributions: undefined
});
sourceStamen.setAttributions(undefined);

var groupLayers =  [
        new TileLayer({
            title: 'Stamen',
            type: 'base',
            source: sourceStamen
        }),
        new TileLayer({
            source : PARAMETRES.sourceLayerSat,
            title: 'Orthophoto',
            type: 'base',
        }),
        new TileLayer({
            title: 'OSM',
            type: 'base',
            source: new OSM({
                attributions: false
            }),
            
        }),
        //famillesPaysagesVector,
        clusterLayer
    ];

    //ajoute les couches local supplémentaire
    if(groupeLayersLocal){
        groupeLayersLocal.map(groupLayer => groupLayers.push(groupLayer));
    }


//Création de la map avec les intéractions nécessaires à l'intérieur
export var map = new Map({
    //layers: [raster, vectorLayer],
    layers: groupLayers,
    target: 'map',
    overlays: [overlay],
    interactions: defaultInteractions().extend([new Select({
        condition: function(evt) {
            return evt.type == 'singleclick' ;
        },
        filter: function(feature, layer){
            //si on est sur la couche des photos
            return layer.get('name') == 'ClusterPhotoLayer';
        },
        //Attribution du style au clic
        style: selectStyleFunction
    })]),
    view: new View({
        center: fromLonLat([PARAMETRES.mapLongitude, PARAMETRES.mapLatitude]),
        zoom: PARAMETRES.mapZoom,
        maxZoom: 19
    }),
    controls: [
            new Zoom(),
            new Attribution()
            //new ol.control.LayerSwitcher()
//            new ol.control.ScaleLine()
    ],
});

var layerSwitcher = new LayerSwitcher();
map.addControl(layerSwitcher);

map.on('click', function(event) {
    overlay.setPosition(undefined);
    var element = overlay.getElement();
    $(element).popover('dispose');
    $(element).popover({
      container: element,
      placement: 'top',
      animation: false,
      html: true,
      
    });
    $(element).popover('show');
});

map.on('pointermove', function(e) {
    if (e.dragging) {
        return;
    }
    var pixel = map.getEventPixel(e.originalEvent);
    var hit = map.hasFeatureAtPixel(pixel);
    if(!hit){
    $("#map").removeClass('cur-p');
    }else{
        var pointer = false;
        map.forEachFeatureAtPixel(pixel,
        function(feature, layer) {
            if(layer.get('name') == 'ClusterPhotoLayer'){
                pointer = true;
            }
        });
        if (pointer){
            $("#map").addClass('cur-p');
        }else{
            $("#map").removeClass('cur-p');
        }
    }
});
