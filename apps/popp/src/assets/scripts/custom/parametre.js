
import {OSM, Vector as VectorSource, Stamen, WMTS} from 'ol/source';
import WMTSTileGrid from 'ol/tilegrid/WMTS';
import {fromLonLat, get as getProjection}  from 'ol/proj';
import {createEmpty, extend, getWidth, getHeight, getCenter, getTopLeft} from 'ol/extent';
import TileWMS from 'ol/source/TileWMS';
import {Circle as CircleStyle, Fill, Stroke, Style, Text, RegularShape, Icon} from 'ol/style';

var projection = getProjection('EPSG:3857');
var tileSizePixels = 256;
var tileSizeMtrs = getWidth(projection.getExtent()) / tileSizePixels;
var matrixIds = [];
var resolutions = [];
for (var i = 0; i <= 14; i++) {
  matrixIds[i] = 'EPSG:3857:' +  i;
  resolutions[i] = tileSizeMtrs / Math.pow(2, i);
}
var tileGrid = new WMTSTileGrid({
  origin: getTopLeft(projection.getExtent()),
  resolutions: resolutions,
  matrixIds: matrixIds
});

export const PARAMETRES = {
    //url : "/poppv2",
    url : "",
    dataTableFrancais : {
        "sProcessing":     "Traitement en cours...",
        "sSearch":         "Rechercher&nbsp;:",
        "sLengthMenu":     "Afficher _MENU_ &eacute;l&eacute;ments",
        "sInfo":           "Résultats _START_ &agrave; _END_ sur _TOTAL_ ",
        "sInfoEmpty":      "0 &eacute;l&eacute;ment(s)",
        "sInfoFiltered":   "filtr&eacute; (total _MAX_ résultats)",
        "sInfoPostFix":    "",
        "sLoadingRecords": "Chargement en cours...",
        "sZeroRecords":    "Aucun &eacute;l&eacute;ment &agrave; afficher",
        "sEmptyTable":     "Aucune donn&eacute;e disponible dans le tableau",
        "oPaginate": {
                "sFirst":      "Premier",
                "sPrevious":   "Pr&eacute;c&eacute;dent",
                "sNext":       "Suivant",
                "sLast":       "Dernier"
            },
        "oAria": {
                "sSortAscending":  ": activer pour trier la colonne par ordre croissant",
                "sSortDescending": ": activer pour trier la colonne par ordre d&eacute;croissant"
            }
    },
    mapLongitude: -2.50,
    mapLatitude: 48.05,
    mapZoom: 8,
    autoLoadSeries: false,
    linkSelectCommune: false,
    linkSelectUnitePaysage: false,
    clusterType: "normal",
    /*fileJsonPath: "/files/carto/ensembles_familles_paysages_4326.geojson",
    titleJsonLayer: "Familles Paysagères",
    fileJsonTextField: "f1",*/
    layers: [{
      fileJsonPath: "/files/carto/ensembles_familles_paysages_4326.json",
      titleJsonLayer: "Familles Paysagères",
      fileJsonTextField: "f1",
      attributions: ""
    }
  ],
    sourceLayerSat: new WMTS ({
        url: 'https://tile.geobretagne.fr/gwc02/service/wmts',
        layer: 'satellite',
        format: 'image/jpeg',
        matrixSet: 'EPSG:3857',
        tileGrid: tileGrid,
        style: 'default',
        dimensions: {
            'threshold': 100
        },
    }),
    linkSelectStructureOpp: false,
    getIconSerieStyle(feature){
        var oppType = feature.values_.type;
        return new Style({
            //geometry: feature.getGeometry(),
            image: new CircleStyle({
                radius:5,
                fill: oppType === "local" ? new Fill({color: 'rgba(255, 153, 0, 0.8)'}) : new Fill({color: 'rgba(255, 153, 0, 0.8)'}),
                stroke: new Stroke({color: 'rgba(255, 204, 1, 0.2)', width: 1})
              })
        })
    },
    getIconSerieStyleSelected(feature){  
         return  new Style({
            //geometry: feature.getGeometry(),
            image: new CircleStyle({
                radius:5,
                fill: new Fill({
                    color: [255, 113, 9, 0.8 ]
                }),
                stroke: new Stroke({
                    color: [255, 113, 9, 1 ],
                    width: 1
                })
            })
        })
    },
    getClusterStyleSelected(feature, radius){
      return new Style({
          image: new CircleStyle({
              radius: radius,
              
              fill: new Fill({
                  color: [255, 113, 9, 0.8 ]
              }),
              stroke: new Stroke({
                  color: [255, 113, 9, 1 ],
                  width: 1
              })
          })
      })
    },
    getClusterStyle(feature, radius){
        return new Style({
            image: new CircleStyle({
                radius: radius,
                fill: new Fill({
                    color: [255, 113, 9, 0.4 ]
                }),
                stroke: new Stroke({
                    color: [255, 113, 9, 1 ],
                    width: 1
                })
            }),
            text: new Text({
                text: feature.get('features').length.toString(),
                fill: new Fill({
                    color: '#fff'
                  }),
                stroke: new Stroke({
                    color: 'rgba(0, 0, 0, 0.6)',
                    width: 3
                  })
            })
        });
    },
    setOverlayPosition(overlay, feature){
    },
    getRadiusStyle(size){
        return Math.max(8, Math.min(size*0.75, 20)) + 2;
    }
};