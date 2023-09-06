import { PARAMETRES } from '../custom/parametre';
import smartWizard from 'smartwizard';
import 'smartwizard/dist/css/smart_wizard_theme_dots.css';

import 'chosen-js';
import 'chosen-js/chosen.min.css';

import Map from 'ol/Map';
import {Tile as TileLayer, Vector as VectorLayer, Group as LayerGroup} from 'ol/layer';
import {OSM, Vector as VectorSource, Stamen, WMTS} from 'ol/source';
import View from 'ol/View';
import Feature from 'ol/Feature';
import {fromLonLat, toLonLat}  from 'ol/proj';
import Draw from 'ol/interaction/Draw';
import Point from 'ol/geom/Point'
import 'ol/ol.css';
import GeoJSON from 'ol/format/GeoJSON';
import {Circle as CircleStyle, Fill, Stroke, Style, Text, RegularShape, Icon} from 'ol/style';
import LayerSwitcher from 'ol-layerswitcher';
import {Zoom} from 'ol/control';

import 'jstree';
//import 'jstree/dist/themes/default/style.min.css';
import 'ol-layerswitcher/src/ol-layerswitcher.css';

import Inputmask from 'inputmask';
import { flashMessage } from '../flashMessage/flashMessage';

var map = false;
var draw, source;
var tablePhotosSerie;
var newIdPhoto = 0;
var newIdSon = 0;
var newIdDocument = 0;
var newIdLien = 0;
/*
//JE SAIS PAS A QUOI CA SERT
$.extend( $.fn.dataTableExt.oSort, {
    "date-eu-pre": function ( date ) {
        date = date.replace(" ", "");
         
        if ( ! date ) {
            return 0;
        }
 
        var year;
        var eu_date = date.split(/[\.\-\/]/);
 
        if ( eu_date[2] ) {
            year = eu_date[2];
        }
        else {
            year = 0;
        }
 
        var month = eu_date[1];
        if ( month.length == 1 ) {
            month = 0+month;
        }
 
        var day = eu_date[0];
        if ( day.length == 1 ) {
            day = 0+day;
        }
 
        return (year + month + day) * 1;
    },
 
    "date-eu-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },
 
    "date-eu-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
    }
} );*/

$(function() {

    //$("#photo_thesaurus").jstree();
    //$("#document_ref_heure").inputmask({"mask": "99:99"});
    var heureMask = new Inputmask("99:99");
    heureMask.mask($("#document_ref_heure"));
    heureMask.mask($("#photo_heure"));
    heureMask.mask($("#son_heure"));    
    //.inputmask({"mask": "99:99"});


    var tableSeriesUpdateFile = $('#dataTableSeriesUpdateFile').DataTable({
        "paging":   false,
        "ordering": false,
        "info":     false,
        searching: false,
        rowId: 0,
        "columnDefs": [
            {
                //"targets": [ 0, 3, 4, 5 ],
                "visible": false,
                "searchable": true
            }
        ]
    });

    $('#dataTableSeriesUpdateFile a.modify, #dataTableSeriesUpdateFile a.add').click(function(){
        $("#modalSerieFile").modal('toggle');
        
        $('#serieFileType').val($(this).parent().data("file-type"));
        updateSerieDropzoneForm.dropzone.removeAllFiles(true);
    });

    $('#dataTableSeriesUpdateFile a.remove').click(function(){
        var serieFileType = $(this).parent().data("file-type");
        removeFileSerie(serieFileType);
    });

    $('#saveSerieFile').click(function(){
        var serieFileType = $("#serieFileType").val();
        if(updateSerieDropzoneForm.dropzone.files.length === 0){
            removeFileSerie(serieFileType);
            $('#modalSerieFile').modal('hide');
            return;
        }
        var fileDropZone = JSON.parse(updateSerieDropzoneForm.dropzone.files[0].xhr.response);
        if(fileDropZone.status == 'error'){
            removeFileSerie(serieFileType);
            $('#modalSerieFile').modal('hide');
            return;
        }
        
        //Création du tableau de parametre d'envoi 
        var insertionChamp = {};
        var fileDropZone = JSON.parse(updateSerieDropzoneForm.dropzone.files[0].xhr.response);

        var fileId = "new";

        /*var fileName = insertionChamp["serie" + serieFileType + "Name"] = fileDropZone.fileName;
        var fileURL = insertionChamp["serie" + serieFileType + "URI"] = fileDropZone.fileURI;
        var filePath = insertionChamp["serie" + serieFileType + "Path"] = fileDropZone.filepath;
        var fileSize = insertionChamp["serie" + serieFileType + "Size"] = fileDropZone.fileSize;
        var fileFormat = insertionChamp["serie" + serieFileType + "Format"] = fileDropZone.fileFormat;
        var fileStatut = insertionChamp["serie" + serieFileType + "Statut"] = fileDropZone.fileStatut;
        var fileDate = insertionChamp["serie" + serieFileType + "Date"] = updateSerieDropzoneForm.dropzone.files[0].lastModified;*/
        var fileName = fileDropZone.fileName;
        var fileURL = fileDropZone.fileURI;
        var filePath = fileDropZone.filepath;
        var fileSize = fileDropZone.fileSize;
        
        var ligneFile = $('#' + serieFileType);
        $(ligneFile).find(".fileId").val(fileId);
        $(ligneFile).find(".fileDownload").html("");
        
        //on réinitialise avant ajout du lien
        var a = document.createElement('a');
        $(a).attr('href', fileURL)
            .attr('target', "_blank")
            .html(fileName)
            .appendTo($(ligneFile).find(".fileDownload")) ;

        $(ligneFile).find(".fileTitre").val(fileName);
        $(ligneFile).find(".fileUrl").val(filePath);
        $(ligneFile).find(".filePoids").val(fileSize);

        $(ligneFile).find('.remove, .modify').show();
        $(ligneFile).find('.add').hide();
        $('#modalSerieFile').modal('hide');

    });

    function removeFileSerie(serieFileType){
        var ligneFile = $('#' + serieFileType);
        $(ligneFile).find(".fileId").val('delete');
        $(ligneFile).find(".fileDownload").html("");
        $(ligneFile).find(".fileTitre").val("");
        $(ligneFile).find(".fileUrl").val("");
        $(ligneFile).find(".filePoids").val("");
        $(ligneFile).find('.remove, .modify').hide();
        $(ligneFile).find('.add').show();
    }

    
    $('#createSerie').on('click', e => {
        var insertionChamp = {};
        //on boucle sur les champs pour détecter les erreurs et remplir le tableau du POST
        var series = $(".serie");
        $(".serie").each(function(index){
            if(($(this).val() == "" || $(this).val() == null) && this.required){
                $(this).addClass('is-invalid');
                $(window).scrollTop($(this).position().top);
                //$('body').animate({ scrollTop: $(this).position().top }, 500);
                return;
            }
            $(this).removeClass('is-invalid');
            var id = $(this).attr('id');
            var value = $(this).val();
            insertionChamp[id] = value;
        });

        insertionChamp['serie_desc_fine_edit'] = myEditor.getData();

        //On boucle sur chaque ligne du tableau
        $("#dataTableSeriesUpdateFile tr").each(function(index){
            var ligne = $(this);
            var typeFile = $(this).attr('id');
            if(typeFile !== undefined){
                var fileId = $(ligne).find(".fileId").val();
                var fileTitre = $(ligne).find(".fileTitre").val();
                var fileUrl = $(ligne).find(".fileUrl").val();
                var filePoids = $(ligne).find(".filePoids").val();
                insertionChamp[typeFile] = {
                    "fileId" : fileId,
                    "fileTitre" : fileTitre,
                    "fileUrl" : fileUrl,
                    "filePoids" : filePoids,
                }
            }
        });

        //enregistrement des photos
        var tablePhoto = [];
        for (var i=0;i<tablePhotosSerie.data().length;i++) {
            tablePhoto[i] = { 
                photo_id:               tablePhotosSerie.data()[i].photo_id,
                photo_titre :           tablePhotosSerie.data()[i].photo_titre,
                photo_auteur:           tablePhotosSerie.data()[i].photo_auteur,
                photo_thesaurus :       tablePhotosSerie.data()[i].photo_thesaurus,
                photo_thesaurus_facult :       tablePhotosSerie.data()[i].photo_thesaurus_facult,
                photo_desc:             tablePhotosSerie.data()[i].photo_desc,
                photo_date_desc:        tablePhotosSerie.data()[i].photo_date_desc,
                photo_date_prise:       tablePhotosSerie.data()[i].photo_date_prise ,
                photo_format:           tablePhotosSerie.data()[i].photo_format,
                photo_iden:             tablePhotosSerie.data()[i].photo_iden,
                photo_licence:          tablePhotosSerie.data()[i].photo_licence,
                photo_fiche_licence:    tablePhotosSerie.data()[i].photo_fiche_licence,
                photo_heure:            tablePhotosSerie.data()[i].photo_heure,
                photo_type_appareil:    tablePhotosSerie.data()[i].photo_type_appareil,
                photo_focale:           tablePhotosSerie.data()[i].photo_focale,
                photo_ouverture:        tablePhotosSerie.data()[i].photo_ouverture,
                photo_type_film:        tablePhotosSerie.data()[i].photo_type_film,
                photo_iso:              tablePhotosSerie.data()[i].photo_iso,
                photo_poids_ori:        tablePhotosSerie.data()[i].photo_poids_ori,
                photo_inclinaison:      tablePhotosSerie.data()[i].photo_inclinaison,
                photo_hauteur:          tablePhotosSerie.data()[i].photo_hauteur,
                photo_orientation:      tablePhotosSerie.data()[i].photo_orientation,
                photo_altitude:         tablePhotosSerie.data()[i].photo_altitude,
                photo_coef_mer:         tablePhotosSerie.data()[i].photo_coef_mer,
                photo_file_action:      tablePhotosSerie.data()[i].photo_file_action,
                photo_file_name:        tablePhotosSerie.data()[i].photo_file_name,
                photo_file_size:        tablePhotosSerie.data()[i].photo_file_size,
                photo_file_url:         tablePhotosSerie.data()[i].photo_file_url,
                photo_action:           tablePhotosSerie.data()[i].photo_action 
            };
        };
        insertionChamp['photos'] = tablePhoto;

        //enregistrement des sons
        var tableSon = [];
        for (var i=0;i<tableSonsSerie.data().length;i++) {
            tableSon[i] = { 
                son_id:             tableSonsSerie.data()[i].son_id,
                son_titre :         tableSonsSerie.data()[i].son_titre,
                son_auteur:         tableSonsSerie.data()[i].son_auteur,
                son_presentation :  tableSonsSerie.data()[i].son_presentation,
                son_lien_paysage:   tableSonsSerie.data()[i].son_lien_paysage,
                son_date:           tableSonsSerie.data()[i].son_date,
                son_type:           tableSonsSerie.data()[i].son_type ,
                son_format:         tableSonsSerie.data()[i].son_format,
                son_heure:          tableSonsSerie.data()[i].son_heure,
                son_type_mat:       tableSonsSerie.data()[i].son_type_mat,
                son_traitement:     tableSonsSerie.data()[i].son_traitement,
                son_protocole:      tableSonsSerie.data()[i].son_protocole,
                son_contexte:       tableSonsSerie.data()[i].son_contexte,
                son_condition_meteo: tableSonsSerie.data()[i].son_condition_meteo,
                son_num_photo:      tableSonsSerie.data()[i].son_num_photo,
                son_lieu:           tableSonsSerie.data()[i].son_lieu,
                son_duree:          tableSonsSerie.data()[i].son_duree,
                son_langue_id:      tableSonsSerie.data()[i].son_langue_id,
                son_licence_id:     tableSonsSerie.data()[i].son_licence_id,
                son_struct_resp_id: tableSonsSerie.data()[i].son_struct_resp_id,
                son_file_action:    tableSonsSerie.data()[i].son_file_action,
                son_file_name:      tableSonsSerie.data()[i].son_file_name,
                son_file_size:      tableSonsSerie.data()[i].son_file_size,
                son_file_url:       tableSonsSerie.data()[i].son_file_url,
                son_action:         tableSonsSerie.data()[i].son_action 
            };
        };
        insertionChamp['sons'] = tableSon;

        //enregistrement des documents
        var tableDocument = [];
        for (var i=0;i<tableDocumentsSerie.data().length;i++) {
            tableDocument[i] = { 
                document_id:             tableDocumentsSerie.data()[i].document_id,
                document_titre :         tableDocumentsSerie.data()[i].document_titre,
                document_legende:        tableDocumentsSerie.data()[i].document_legende,
                document_file_action:    tableDocumentsSerie.data()[i].document_file_action,
                document_file_name:      tableDocumentsSerie.data()[i].document_file_name,
                document_file_size:      tableDocumentsSerie.data()[i].document_file_size,
                document_file_url:       tableDocumentsSerie.data()[i].document_file_url,
                document_action:         tableDocumentsSerie.data()[i].document_action 
            };
        };
        insertionChamp['documents'] = tableDocument;
        insertionChamp['seriePublie'] = $("#seriePublie").is(':checked');

        //enregistrement des liens ext
        var tableLienExt = [];
        for (var i=0;i<tableLienExtSerie.data().length;i++) {
            tableLienExt[i] = { 
                lienext_id:             tableLienExtSerie.data()[i].lienext_id,
                lienext_value :         tableLienExtSerie.data()[i].lienext_value,
            };
        };
        insertionChamp['liensExt'] = tableLienExt;

        if($(".is-invalid").length ==  0){
            $.ajax({
                url : PARAMETRES.url + '/gestion/serie/insertDb',
                type : 'POST',
                cache: true,
                data : insertionChamp,
                success: function (data) {
                    if(data.status == 'ok'){
                        //Dans le cas ou on à déja enregistré le fichier
                        $("#dataTableSeriesUpdateFile tr").each(function(index){
                            var ligne = $(this);
                            if($(ligne).find(".fileId").val() == 'new'){
                                $(ligne).find(".fileId").val('updated');
                            }
                        });
                        for (var i=0;i<tablePhotosSerie.data().length;i++) {
                            if (tablePhotosSerie.data()[i].photo_action == 'add' || tablePhotosSerie.data()[i].photo_action == 'updated'){
                                tablePhotosSerie.data()[i].photo_action = 'ras';
                                var ligne = tablePhotosSerie.row(i).data();
                                tablePhotosSerie.row(i).data(ligne).invalidate();
                            }
                            if (tablePhotosSerie.data()[i].photo_action == 'delete'){
                                tablePhotosSerie.row(i).remove().draw( false );
                            }
                        }
                        for (var i=0;i<tableSonsSerie.data().length;i++) {
                            if (tableSonsSerie.data()[i].son_action == 'add' || tableSonsSerie.data()[i].son_action == 'updated'){
                                tableSonsSerie.data()[i].son_action = 'ras';
                                var ligne = tableSonsSerie.row(i).data();
                                tableSonsSerie.row(i).data(ligne).invalidate();
                            }
                            if (tableSonsSerie.data()[i].son_action == 'delete'){
                                tableSonsSerie.row(i).remove().draw( false );
                            }
                        }
                        $("#messageConfirmSerie")
                            .removeClass('alert-danger')
                            .addClass('alert alert-success ta-c w-100')
                            .html("L'élement à été enregistrée.")
                            .fadeIn(1000)
                            .delay(2000)
                            .fadeOut(1000);

                        window.location.href = PARAMETRES.url + '/public/get/serie/' + data.serieId;
                    }else if(data.status == 'erreur'){
                        $("#messageConfirmSerie")
                            .addClass('alert alert-danger ta-c w-100')
                            .html(data.message)
                            .fadeIn(500);
                    }

                },
                error : function (){
                    $("#messageConfirmSerie")
                        .addClass('alert alert-danger ta-c w-100')
                        .html("Une erreur a été rencontrée lors de l'enregistrement.")
                        .fadeIn(500);
                }
            });
        }
    });

    
    /* WIZARD DEBUT */

    $("#smartwizard").on("leaveStep", function(e, anchorObject, stepNumber, stepDirection) {
        var elmForm = $("#step-" + (stepNumber+1));
        var returnBool = true;
        var numStepPhoto = $("#stepPhotos").val() - 1;
        elmForm.find(".serie").each(function(index){
            var elem = $(this);
            if(($(this).val() == "" || $(this).val() == null) && this.required){
                //en mode multichoix
                if($(this).hasClass('chosen-select')){
                    $(this).next().addClass('is-invalid');
                }
                $(this).addClass('is-invalid');
                $(window).scrollTop($(this).position().top);
                returnBool = false;
            }else{
                if($(this).hasClass('chosen-select')){
                    $(this).next().removeClass('is-invalid');
                }
                $(this).removeClass('is-invalid');
            }
        });
        if(stepNumber == numStepPhoto && stepDirection == 'forward'){
            //Une photo minimum
            returnBool = false;
            for (var i=0; i<tablePhotosSerie.data().length; i++) {
                if (tablePhotosSerie.data()[i].photo_action != 'delete') {
                    returnBool = true;
                }
            }
            if (returnBool == false){
                $("#messageConfirmSerie")
                    .addClass('alert alert-danger ta-c w-100')
                    .html("Aucune photo ajoutée, la photo est obligatoire.")
                    .fadeIn(1000)
                    .delay(2000)
                    .fadeOut(1000);
            }
        }
        return returnBool;
    });
    
    $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
        $('.sw-btn-next').prop("disabled", false);
        $('.sw-btn-next').show();

        var numStepObjet = $("#stepObjet").val() - 1;
        var numStepGeographie = $("#stepGeographie").val() - 1;
        var numStepEmplacement = $("#stepEmplacement").val() - 1;
        //Au premier lancement de la carto => on l'initialise
        if(stepNumber == numStepEmplacement && !map){
            createMapDraw();
        }
        //Si on est sur la dernière slide, on active le bouton
        if(stepNumber == 5){
            $("#createSerie").prop("disabled", false)
            .removeClass('btn-secondary')
            .addClass('btn-primary');
            $('.sw-btn-next').prop("disabled", true);
            $('.sw-btn-next').hide();
        }
        //Si on active le multi select à l'affichage du slide
        if(stepNumber == numStepObjet){
            $("#serie_objet_thematique").chosen({disable_search_threshold: 10});
        }
        //Si on active le multi select à l'affichage du slide
        if(stepNumber == numStepGeographie){
            $("#serie_coverage_com").chosen({disable_search_threshold: 10});
            $("#serie_coverage_unit_paysage_local").chosen({disable_search_threshold: 10});
        }
        
     });

    $('#smartwizard').smartWizard({
        //transitionEffect: 'fade',
        selected: 0,
        contentCache : false,
        keyNavigation: false,
        lang: {  // Language variables
            next: 'Suivant', 
            previous: 'Précédent'
        }
    });

    $('.sw-btn-next, .sw-btn-prev').removeClass('btn-secondary disabled');

    $(".contentButtonWizard").append($('.sw-btn-next, .sw-btn-prev'));
    $('.sw-btn-next').addClass('btn-primary cur-p m-10');
    $('.sw-btn-prev').addClass('btn-primary cur-p m-10');
    
    /* WIZARD FIN */

    /*CARTE DEBUT*/

    function createMapDraw(){
        var groupeLayersLocal = [];
        PARAMETRES.layers.map((layer, index) => {
            var url = PARAMETRES.url + layer.fileJsonPath;
            var famillesPaysagesSource = new VectorSource({
            url: url,
            format: new GeoJSON()
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

        var raster = new TileLayer({
            source: new OSM()
        });

        source = new VectorSource();
        var vector = new VectorLayer({
          source: source
        });
        
        var groupLayers = [
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
            vector
        ];
        
        //ajoute les couches local supplémentaire
        if(groupeLayersLocal){
            groupeLayersLocal.map(groupLayer => groupLayers.push(groupLayer));
        }

        map = new Map({
            target: 'mapSerieCoordinate',
            layers: groupLayers,
            view: new View({
                center: fromLonLat([PARAMETRES.mapLongitude, PARAMETRES.mapLatitude]),
                zoom: PARAMETRES.mapZoom
            }),
            controls: [
                new Zoom(),
            ],
          });

        draw = new Draw({
            source: source,
            type: 'Point'
        });
        var layerSwitcher = new LayerSwitcher();
        map.addControl(layerSwitcher);

        //A la fin du dessin, on sauvegarde les coordonnées
        draw.on("drawend", function (e) {                                                                             
            map.removeInteraction(draw);
            var coord = toLonLat(e.feature.getGeometry().getCoordinates());
            
            //TODO, ajouter plus de décimal
            $("#serie_emplacement_longitude").val(coord[0].toFixed(5));
            $("#serie_emplacement_latitude").val(coord[1].toFixed(5));
            $("#serie_emplacement_longitude, #serie_emplacement_latitude").removeClass('is-invalid')
                .addClass('is-valid')
                .next('span.formErrors')
                .html('');
        });

        if($("#serie_emplacement_longitude").val() != '' && $("#serie_emplacement_latitude").val() != ''){
            source.clear();
            source.addFeature(
                new Feature({ 
                    geometry: new Point(fromLonLat([$("#serie_emplacement_longitude").val(), $("#serie_emplacement_latitude").val()]))
                })
            );
        }
    }
    
    //on active la fonction Draw sur le clique du bouton
    $("#btnPositionner").on('click', e => {                                                                            
        map.addInteraction(draw);
        source.refresh();
    });

    //On vérifie les coordonnées et on repositionne le point lorsque l'on sort de l'input
    $("#serie_emplacement_longitude, #serie_emplacement_latitude").on('blur', function(){
        if($(this).val() != ""){
            var coord = parseInt($(this).val());
            if(coord > 90 || coord < -90){
                $(this).addClass('is-invalid')
                    .removeClass('is-valid')
                    .next('span.formErrors')
                    .html('Coordonnées incorrectes');
                return;
            }
        }
        $(this).removeClass('is-invalid')
            .addClass('is-valid')
            .next('span.formErrors')
            .html('');

        if($("#serie_emplacement_longitude").hasClass("is-valid") && $("#serie_emplacement_latitude").hasClass("is-valid")){
            source.clear();
            source.addFeature(
                new Feature({ 
                    geometry: new Point(fromLonLat([$("#serie_emplacement_longitude").val(), $("#serie_emplacement_latitude").val()]))
                })
            );
        }
    });

    /*CARTE FIN*/

    /* GESTION DE L'ONGLET PHOTO*/
    $("#addPhotoSerie").click(function(){
        chargeModalPhoto(null);
        $("#modalSeriePhoto").modal('toggle');
    });

    function chargeModalPhoto(data){
        var evolution_delete = $("#evolution_delete").val();
        var firstPhoto = true;
        //En mode création
        if(data == null && !tablePhotosSerie.data().count()){
            //Si on est sur la premiere ligne
            firstPhoto = true;
        }else if(data == null){
            firstPhoto = false;
        }else if(data == tablePhotosSerie.data()[0]){
            firstPhoto = true;
        }else{
            firstPhoto = false;
        }

        $("#photo_thesaurus, #photo_thesaurus_facult").jstree("destroy");
        $("#photo_thesaurus")
            .bind('loaded.jstree', function(e, d) {
                if(data){
                    var tabThesaurus = data.photo_thesaurus.split(',');
                    for(var i = 0; i < tabThesaurus.length; i++){
                        $("#photo_thesaurus").jstree(true).select_node(tabThesaurus[i]);
                    }
                }else{
                    //on est en création => héritage
                    //si il y a des données dans le tableau
                    if(tablePhotosSerie.data().count() > 0){
                        //on prend la dernière ligne
                        var tabThesaurusHeritage = tablePhotosSerie.data()[tablePhotosSerie.data().count() -1].photo_thesaurus.split(',');
                        for(var i = 0; i < tabThesaurusHeritage.length; i++){
                            //On associe à chaque thésaurus la notion de stabilité
                            var positionEvolution = tabThesaurusHeritage[i].indexOf("_");
                            var evolutionId = tabThesaurusHeritage[i].substring(positionEvolution +1);
                            if (evolutionId != evolution_delete){
                                var evolutionStable = tabThesaurusHeritage[i].substring(0, positionEvolution) + '_1';
                                $("#photo_thesaurus").jstree(true).select_node(evolutionStable);

                            }
                        }
                    }
                }
            })
            .jstree({
            'core' : {
                'data' : {
                    'url' : PARAMETRES.url + '/get/tree/thesaurus/' + firstPhoto,
                    "dataType" : "json"
                },
                "themes":{
                    "icons":false,
                    "dots" : false,
                }
            },
            "checkbox" : {"keep_selected_style" : false, three_state: false},
            "plugins": ["checkbox"]
       
        });
        
        $("#photo_thesaurus_facult")
            .bind('loaded.jstree', function(e, d) {
                if(data){
                    var tabThesaurus = data.photo_thesaurus_facult.split(',');
                    for(var i = 0; i < tabThesaurus.length; i++){
                        $("#photo_thesaurus_facult").jstree(true).select_node(tabThesaurus[i]);
                    }
                }else{
                    //on est en création => héritage
                    //si il y a des données dans le tableau
                    if(tablePhotosSerie.data().count() > 0){
                        var tabThesaurusFacultatifHeritage = tablePhotosSerie.data()[tablePhotosSerie.data().count() -1].photo_thesaurus_facult.split(',');
                        for(var i = 0; i < tabThesaurusFacultatifHeritage.length; i++){
                            //On associe à chaque thésaurus la notion de stabilité
                            var positionEvolution = tabThesaurusFacultatifHeritage[i].indexOf("_");
                            var evolutionId = tabThesaurusFacultatifHeritage[i].substring(positionEvolution +1);
                            if (evolutionId != evolution_delete){
                                var evolutionStable = tabThesaurusFacultatifHeritage[i].substring(0, positionEvolution) + '_1';
                                $("#photo_thesaurus_facult").jstree(true).select_node(evolutionStable);
                            }
                        }
                    }
                }
            })
            .jstree({
            'core' : {
                'data' : {
                    'url' : PARAMETRES.url + '/get/tree/thesaurusFacult/' + firstPhoto,
                    "dataType" : "json"
                },
                "themes":{
                    "icons":false,
                    "dots" : false,
                }
            },
            "checkbox" : {"keep_selected_style" : false, three_state: false},
            "plugins": ["checkbox"]
       
        });
        if (data == null) {
            $("input.form-photo, select.form-photo, textarea.form-photo").val('');
            //Valeur par défault
            //Format standard
            $("#photo_format").val(3);
            addPhotoDropzone.dropzone.removeAllFiles(true);
        }else{
            $("#photo_id").val(data.photo_id);
            $("#photo_titre").val(data.photo_titre);
            $("#photo_auteur").val(data.photo_auteur);
            //$("#photo_thesaurus").val(data.photo_thesaurus);
            $("#photo_desc").val(data.photo_desc);
            $("#photo_date_desc").val(data.photo_date_desc);
            $("#photo_date_prise").val(data.photo_date_prise);
            $("#photo_format").val(data.photo_format);
            $("#photo_iden").val(data.photo_iden);
            $("#photo_licence").val(data.photo_licence);
            $("#photo_fiche_licence").val(data.photo_fiche_licence);
            $("#photo_heure").val(data.photo_heure);
            $("#photo_type_appareil").val(data.photo_type_appareil);
            $("#photo_focale").val(data.photo_focale);
            $("#photo_ouverture").val(data.photo_ouverture);
            $("#photo_type_film").val(data.photo_type_film);
            $("#photo_iso").val(data.photo_iso);
            $("#photo_poids_ori").val(data.photo_poids_ori);
            $("#photo_inclinaison").val(data.photo_inclinaison);
            $("#photo_hauteur").val(data.photo_hauteur);
            $("#photo_orientation").val(data.photo_orientation);
            $("#photo_altitude").val(data.photo_altitude);
            $("#photo_coef_mer").val(data.photo_coef_mer);
            $("#photo_file_action").val(data.photo_file_action);
            $("#photo_file_name").val(data.photo_file_name);
            $("#photo_file_size").val(data.photo_file_size);
            $("#photo_file_url").val(data.photo_file_url);
            addPhotoDropzone.dropzone.removeAllFiles(true);
            var mockFile = {name : data.photo_file_name, size : data.photo_file_size, accepted: true, kind: "image", dataURL : PARAMETRES.url + '/files/' + data.photo_file_url};
            addPhotoDropzone.dropzone.files.push(mockFile);
            addPhotoDropzone.dropzone.emit("addedfile", mockFile);
            addPhotoDropzone.dropzone.createThumbnailFromUrl(mockFile, 
                addPhotoDropzone.dropzone.options.thumbnailWidth, 
                addPhotoDropzone.dropzone.options.thumbnailHeight,
                addPhotoDropzone.dropzone.options.thumbnailMethod, true, function (thumbnail) 
                    {
                        addPhotoDropzone.dropzone.emit('thumbnail', mockFile, thumbnail);
                    });
            
            addPhotoDropzone.dropzone.emit('complete', mockFile);
            
        }
    }

    tablePhotosSerie = $("#dataTablePhotosSerie").DataTable( {
        dom: "",
        paging:false,
        searching: false,
        language : PARAMETRES.dataTableFrancais,
        order: [[ 7, "asc" ]],
        columns: [  
            {data: "photo_id"}, {data: "photo_titre"}, {data: "photo_auteur"}, {data: "photo_thesaurus"}, {data: "photo_thesaurus_facult"},  
            {data: "photo_desc"}, {data: "photo_date_desc"}, {data: "photo_date_prise"}, {data: "photo_format"},  
            {data: "photo_iden"}, {data: "photo_licence"}, {data: "photo_fiche_licence"}, {data: "photo_heure"},   
            {data: "photo_type_appareil"}, {data: "photo_focale"}, {data: "photo_ouverture"}, {data: "photo_type_film"},   
            {data: "photo_iso"}, {data: "photo_poids_ori"}, {data: "photo_inclinaison"}, {data: "photo_hauteur"}, 
            {data: "photo_orientation"}, {data: "photo_altitude"}, {data: "photo_coef_mer"}, {data: "photo_file_action"}, 
            {data: "photo_file_name"}, {data: "photo_file_size"}, {data: "photo_file_url"}, {data: "photo_action"}, {data: "action", width: "120px"}
        ],
        columnDefs: [
            { 
                targets: [ 0, 2, 3, 4 ,5, 6, 8, 9, 10, 11, 12, 13 ,14 ,15 ,16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28 ], 
                visible: false, 
                searchable: false
            },
            {
                type: 'date-eu', 
                targets: 7 , 
                visible: false, 
                searchable: false
            },
            { 
                targets: [ 1, 29 ] ,
                orderable: false
            }
        ],
        drawCallback: function ( settings ) {
            }
    } );

    $('#saveSeriePhoto').on('click', e => {
        var insertionChamp = {};
        //on boucle sur les champs pour détecter les erreurs et remplir le tableau du POST
        $(".form-photo").each(function(index){
            if(($(this).val() == "" || $(this).val() == null) && this.required){
                $(this).addClass('is-invalid');
                $(window).scrollTop($(this).position().top);
                return;
            }
            $(this).removeClass('is-invalid');
            var id = $(this).attr('id');
            var value = $(this).val();
            insertionChamp[id] = value;
        });
        
        if(addPhotoDropzone.dropzone.files.length === 0){
            $("#addPhotoDropzone").next('span.formErrors')
            .html('La photo est obligatoire');
            return;
        }

        if($("#photo_file_action").val() == "loaded" && addPhotoDropzone.dropzone.files[0].xhr == undefined){
            //On a pas touché à la photo
            var fileParam = {
                action : 'loaded',
                fileName: $("#photo_file_name").val(),
                fileSize: $("#photo_file_size").val(),
                fileURI : $("#photo_file_url").val(),
            }
        }else{
            if(addPhotoDropzone.dropzone.files[0].xhr == undefined){
                //photo nouvellement créer et mise à jour ensuite sans modifier la photo
                var fileParam = {
                    action : $("#photo_file_action").val(),
                    fileName: $("#photo_file_name").val(),
                    fileSize: $("#photo_file_size").val(),
                    fileURI : $("#photo_file_url").val(),
                }
            }else{
                //la photo a été modifiée
                var photoDropZone = JSON.parse(addPhotoDropzone.dropzone.files[0].xhr.response);
                if(photoDropZone.status == 'error'){
                    $("#addPhotoDropzone").next('span.formErrors')
                    .html('Une erreur a été rencontrée lors de l\'envoi de la photo');
                    return;
                };
                //La photo a été ajoutée ou modifiée
                var fileParam = {
                    action : 'new',
                    fileName: photoDropZone.fileName,
                    fileSize: photoDropZone.fileSize,
                    fileURI: photoDropZone.filePath,
                }
                $("#addPhotoDropzone").next('span.formErrors')
                    .html('');
            }
        }
        
        //Récupération des thesaurus du treeView
        var dataThesaurus = [];
        var dataThesaurusTwice = [];
        var thesaurusTwice = false;
        var tabThesaurus = $("#photo_thesaurus").jstree("get_selected", true);
        tabThesaurus.forEach(function(item) {
            if(item.id.includes('_')){
                dataThesaurus.push(item.id);
                //ajout d'un controle de saisie en double
                if (dataThesaurusTwice.includes(item.id.split('_')[0])){
                    $("#photo_thesaurus").next('span.formErrors').html('2 éléments ou plus sont sélectionnés sur un thésaurus.');
                    $(window).scrollTop($("#photo_thesaurus").position().top);
                    thesaurusTwice = true;
                    return;
                }
                dataThesaurusTwice.push(item.id.split('_')[0]);
            }
        });
        
        var dataThesaurusFacult = [];
        dataThesaurusTwice = [];
        var tabThesaurusFacult = $("#photo_thesaurus_facult").jstree("get_selected", true);
        tabThesaurusFacult.forEach(function(item) {
            if(item.id.includes('_')){
                dataThesaurusFacult.push(item.id);
                //ajout d'un controle de saisie en double
                if (dataThesaurusTwice.includes(item.id.split('_')[0])){
                    $("#photo_thesaurus_facult").next('span.formErrors').html('2 éléments ou plus sont sélectionnés sur un thésaurus.');
                    $(window).scrollTop($("#photo_thesaurus_facult").position().top);
                    thesaurusTwice = true;
                    return;
                }
                dataThesaurusTwice.push(item.id.split('_')[0]);
            }
        });
        
        if (thesaurusTwice){
            $(window).scrollTop($("#photo_thesaurus").position().top);
            return;
        }
        //Recette 7 - ne plus rendre obligatoire la saisie 
        /*if(dataThesaurus.length + tabThesaurusFacult.length < 2){
            $("#addPhotoDropzone").next('span.formErrors')
            .html('Au minimum deux éléments de paysage doivent être sélectionnés');
            return;
        }*/

        if($(".is-invalid").length ==  0){
            var bTrouve = false;
            //newIdPhoto
            var photo_fiche_licence_id = $("#photo_fiche_licence option:selected").val();
            if(photo_fiche_licence_id == undefined){
                photo_fiche_licence_id = '';
            }
            for (var i=0; i<tablePhotosSerie.data().length; i++) {
                if (tablePhotosSerie.data()[i].photo_id == $("#photo_id").val()) {
                    tablePhotosSerie.data()[i].photo_titre = $("#photo_titre").val();
                    tablePhotosSerie.data()[i].photo_auteur = $("#photo_auteur").val();
                    tablePhotosSerie.data()[i].photo_thesaurus = dataThesaurus.join(",");
                    tablePhotosSerie.data()[i].photo_thesaurus_facult = dataThesaurusFacult.join(",");
                    tablePhotosSerie.data()[i].photo_desc = $("#photo_desc").val();
                    tablePhotosSerie.data()[i].photo_date_desc = $("#photo_date_desc").val();
                    tablePhotosSerie.data()[i].photo_date_prise = $("#photo_date_prise").val();
                    tablePhotosSerie.data()[i].photo_format = $("#photo_format option:selected").val();
                    tablePhotosSerie.data()[i].photo_iden = $("#photo_iden").val();
                    tablePhotosSerie.data()[i].photo_licence = $("#photo_licence option:selected").val();
                    tablePhotosSerie.data()[i].photo_fiche_licence = photo_fiche_licence_id;
                    tablePhotosSerie.data()[i].photo_heure = $("#photo_heure").val();
                    tablePhotosSerie.data()[i].photo_type_appareil = $("#photo_type_appareil").val();
                    tablePhotosSerie.data()[i].photo_focale = $("#photo_focale").val();
                    tablePhotosSerie.data()[i].photo_ouverture = $("#photo_ouverture").val();
                    tablePhotosSerie.data()[i].photo_type_film = $("#photo_type_film").val();
                    tablePhotosSerie.data()[i].photo_iso = $("#photo_iso").val();
                    tablePhotosSerie.data()[i].photo_poids_ori = $("#photo_poids_ori").val();
                    tablePhotosSerie.data()[i].photo_inclinaison = $("#photo_inclinaison").val();
                    tablePhotosSerie.data()[i].photo_hauteur = $("#photo_hauteur").val();
                    tablePhotosSerie.data()[i].photo_orientation = $("#photo_orientation").val();
                    tablePhotosSerie.data()[i].photo_altitude = $("#photo_altitude").val();
                    tablePhotosSerie.data()[i].photo_coef_mer = $("#photo_coef_mer").val();
                    tablePhotosSerie.data()[i].photo_file_action = fileParam.action;
                    tablePhotosSerie.data()[i].photo_file_name = fileParam.fileName;
                    tablePhotosSerie.data()[i].photo_file_size = fileParam.fileSize;
                    tablePhotosSerie.data()[i].photo_file_url = fileParam.fileURI;
                    if(tablePhotosSerie.data()[i].photo_action != 'add'){
                        tablePhotosSerie.data()[i].photo_action = 'updated';
                    }
                    
                    // mise à jour du tableau
                    var ligne = tablePhotosSerie.row(i).data();
                    tablePhotosSerie.row(i).data(ligne).invalidate();
                    
                    bTrouve = true;
                }
            }
            if (!bTrouve){
                var aModif = $("<a>", {
                    class:"modify"
                });
                $("<i>", {
                    class: "c-light-blue-500 cur-p ti ti-pencil"
                }).appendTo(aModif)

                var aDel = $("<a>", {
                    class:"remove"
                }).jConfirm().on('confirm', function(e){
                    var elem = e.currentTarget;
                    var ligneIndex = $(elem).parent().parent();
                    var data = tablePhotosSerie.row( ligneIndex ).data();
                    //si c'est une photo ajouter, alors on la supprime
                    if(data.photo_action == "add" ){
                        tablePhotosSerie.row(ligneIndex).remove().draw( false );
                    }else{
                        flashMessage("success", "Succès", "La photo sera supprimée à la l'enregistrement");
                        data.photo_action = 'delete';
                        // mise à jour du tableau
                        tablePhotosSerie.row(ligneIndex).data(data).invalidate();
                        ligneIndex.addClass('rowDelete');
                    }
                });
                $("<i>", {
                    class: "c-red-500 cur-p ti ti-trash"
                }).appendTo(aDel)
                
                var id = Math.floor((1 + Math.random()) * 0x10000)
                .toString(16)
                .substring(1);
                var divAction = $("<div>", {
                    class: "",
                    id: id
                })/*.append(aModif)
                .append(aDel)*/
                
                tablePhotosSerie.row.add({
                    "photo_id": 'new' + newIdPhoto,
                    "photo_titre": $("#photo_titre").val(),
                    "photo_auteur": $("#photo_auteur").val(),
                    "photo_thesaurus": dataThesaurus.join(","),
                    "photo_thesaurus_facult": dataThesaurusFacult.join(","),
                    "photo_desc": $("#photo_desc").val(),
                    "photo_date_desc": $("#photo_date_desc").val(),
                    "photo_date_prise": $("#photo_date_prise").val(),
                    "photo_format": $("#photo_format option:selected").val(),
                    "photo_iden": $("#photo_iden").val(),
                    "photo_licence": $("#photo_licence option:selected").val(),
                    "photo_fiche_licence": photo_fiche_licence_id,
                    "photo_heure": $("#photo_heure").val(),
                    "photo_type_appareil": $("#photo_type_appareil").val(),
                    "photo_focale": $("#photo_focale").val(),
                    "photo_ouverture": $("#photo_ouverture").val(),
                    "photo_type_film": $("#photo_type_film").val(),
                    "photo_iso": $("#photo_iso").val(),
                    "photo_poids_ori": $("#photo_poids_ori").val(),
                    "photo_inclinaison": $("#photo_inclinaison").val(),
                    "photo_hauteur": $("#photo_hauteur").val(),
                    "photo_orientation": $("#photo_orientation").val(),
                    "photo_altitude": $("#photo_altitude").val(),
                    "photo_coef_mer": $("#photo_coef_mer").val(),
                    "photo_file_action": fileParam.action,
                    "photo_file_name": fileParam.fileName,
                    "photo_file_size": fileParam.fileSize,
                    "photo_file_url": fileParam.fileURI,
                    "photo_action": 'add',
                    "action": "<div id='"+id+"'></div>"
                    }
                    //"action": '<a class="modify"><i class="c-light-blue-500 cur-p ti ti-pencil"></i></a><a class="remove"><i class="c-red-500 cur-p ti ti-trash"></i></a>'}
                ).draw();
                newIdPhoto++;

                $("#" + id).append(aModif)
                .append(aDel)
            }
            $("#modalSeriePhoto").modal('toggle');
        }
    });

    $('#dataTablePhotosSerie tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        var data = tablePhotosSerie.row( $(elem).parent().parent() ).data();
        chargeModalPhoto(data);
        $("#modalSeriePhoto").modal('toggle');
    });

    $('#dataTablePhotosSerie tbody a.remove').jConfirm().on('confirm', function(e){
        var elem = e.currentTarget;
        var ligneIndex = $(elem).parent().parent();
        var data = tablePhotosSerie.row( ligneIndex ).data();
        if(data.photo_action == "add" ){
            tablePhotosSerie.row(ligneIndex).remove().draw( false );
        }else{
            flashMessage("success", "Succès", "La photo sera supprimée à l'enregistrement");
            data.photo_action = 'delete';
            // mise à jour du tableau
            tablePhotosSerie.row(ligneIndex).data(data).invalidate();
            ligneIndex.addClass('rowDelete');
        }
    });

    /* FIN DE GESTION DE L'ONGLET PHOTO*/
    
    /*DOC REFERENCE*/

    $("#addRefDoc").click(function(){
        chargeModalDocRef('new');
        $("#modalSerieRefDoc").modal('toggle');
    });

    $("#editRefDoc").click(function(){
        chargeModalDocRef('update');
        $("#modalSerieRefDoc").modal('toggle');
    });

    $("#removeRefDoc").click(function(){
        $(".form-serie-refdoc").val('');
        $("#editRefDoc, #removeRefDoc").hide();
        $("#addRefDoc").show();
        $("#serie_document_ref_download").html('');
        $("#serie_document_ref_action").val('delete');    
    });
    
    function chargeModalDocRef(action){
        $("#document_ref_action").val(action);
        $("#document_ref_id").val($("#serie_document_ref_id").val());
        $("#document_ref_identifiant_int").val($("#serie_document_ref_identifiant_int").val());
        $("#document_ref_auteur").val($("#serie_document_ref_auteur").val());
        $("#document_ref_desc").val($("#serie_document_ref_desc").val());
        $("#document_ref_date").val($("#serie_document_ref_date").val());
        $("#document_ref_commentaire_date").val($("#serie_document_ref_commentaire_date").val());
        $("#document_ref_type").val($("#serie_document_ref_type").val());
        $("#document_ref_format").val($("#serie_document_ref_format").val());
        $("#document_ref_source").val($("#serie_document_ref_source").val());
        $("#document_ref_site").val($("#serie_document_ref_site").val());
        //$("#document_ref_nom").val($("#serie_document_ref_nom").val());
        $("#document_ref_sous_titre").val($("#serie_document_ref_sous_titre").val());
        $("#document_ref_heure").val($("#serie_document_ref_heure").val());
        $("#document_ref_periode").val($("#serie_document_ref_periode").val());
        $("#document_ref_moment").val($("#serie_document_ref_moment").val());
        $("#document_ref_lieu_conservation").val($("#serie_document_ref_lieu_conservation").val());
        $("#document_ref_orientation").val($("#serie_document_ref_orientation").val());
        $("#document_ref_altitude").val($("#serie_document_ref_altitude").val());
        $("#document_ref_coef_maree").val($("#serie_document_ref_coef_maree").val());
        $("#document_ref_cote_doc").val($("#serie_document_ref_cote_doc").val());
        if(action=='new'){
            //Valeur par défault
            $("#document_ref_langue_id").val(2);
        }else{
            $("#document_ref_langue_id").val($("#serie_document_ref_langue_id").val());
        }
        $("#document_ref_licence_id").val($("#serie_document_ref_licence_id").val());
        $("#document_ref_file_id").val($("#serie_document_ref_file_id").val());
        $("#document_ref_file_action").val($("#serie_document_ref_file_action").val());
        $("#document_ref_file_titre").val($("#serie_document_ref_file_titre").val());
        $("#document_ref_file_url").val($("#serie_document_ref_file_url").val());
        $("#document_ref_file_poids").val($("#serie_document_ref_file_poids").val());

        
        if (action == 'new') {
            addDocRefDropzone.dropzone.removeAllFiles(true);
        }else{
            addDocRefDropzone.dropzone.removeAllFiles(true);
            var mockFile = {
                name : $("#serie_document_ref_file_titre").val(), 
                size : $("#serie_document_ref_file_poids").val(), 
                accepted: true, 
                kind: "image", 
                dataURL : $("#serie_document_ref_file_url").val()
            };
            addDocRefDropzone.dropzone.files.push(mockFile);
            addDocRefDropzone.dropzone.emit("addedfile", mockFile);
            addDocRefDropzone.dropzone.createThumbnailFromUrl(mockFile, 
                addDocRefDropzone.dropzone.options.thumbnailWidth, 
                addDocRefDropzone.dropzone.options.thumbnailHeight,
                addDocRefDropzone.dropzone.options.thumbnailMethod, true, function (thumbnail) 
                    {
                        addDocRefDropzone.dropzone.emit('thumbnail', mockFile, thumbnail);
                    });
            
            addDocRefDropzone.dropzone.emit('complete', mockFile);
            
        }
    }
    $("#saveSerieDocRef").click(function(){
        $(".form-refdoc").each(function(index){
            if(($(this).val() == "" || $(this).val() == null) && this.required){
                $(this).addClass('is-invalid');
                $(window).scrollTop($(this).position().top);
                return;
            }
            $(this).removeClass('is-invalid');
        });

        
        if(addDocRefDropzone.dropzone.files.length === 0){
            $("#addDocRefDropzone").next('span.formErrors')
            .html('Le document est obligatoire');
            return;
        }

        if($(".is-invalid").length ==  0){
            $("#serie_document_ref_action").val($("#document_ref_action").val());
            $("#serie_document_ref_id").val($("#document_ref_id").val());
            $("#serie_document_ref_identifiant_int").val($("#document_ref_identifiant_int").val());
            $("#serie_document_ref_auteur").val($("#document_ref_auteur").val());
            $("#serie_document_ref_desc").val($("#document_ref_desc").val());
            $("#serie_document_ref_date").val($("#document_ref_date").val());
            $("#serie_document_ref_commentaire_date").val($("#document_ref_commentaire_date").val());
            $("#serie_document_ref_type").val($("#document_ref_type").val());
            $("#serie_document_ref_format").val($("#document_ref_format").val());
            $("#serie_document_ref_source").val($("#document_ref_source").val());
            $("#serie_document_ref_site").val($("#document_ref_site").val());
            //$("#serie_document_ref_nom").val($("#document_ref_nom").val());
            $("#serie_document_ref_sous_titre").val($("#document_ref_sous_titre").val());
            $("#serie_document_ref_heure").val($("#document_ref_heure").val());
            
            $("#serie_document_ref_periode").val($("#document_ref_periode").val());
            $("#serie_document_ref_moment").val($("#document_ref_moment").val());
            $("#serie_document_ref_lieu_conservation").val($("#document_ref_lieu_conservation").val());
            $("#serie_document_ref_orientation").val($("#document_ref_orientation").val());
            $("#serie_document_ref_altitude").val($("#document_ref_altitude").val());
            $("#serie_document_ref_coef_maree").val($("#document_ref_coef_maree").val());
            $("#serie_document_ref_cote_doc").val($("#document_ref_cote_doc").val());
            $("#serie_document_ref_langue_id").val($("#document_ref_langue_id").val());
            $("#serie_document_ref_licence_id").val($("#document_ref_licence_id").val());
            $("#serie_document_ref_file_id").val($("#document_ref_file_id").val());
            $("#serie_document_ref_file_titre").val($("#document_ref_file_titre").val());
            $("#serie_document_ref_file_url").val($("#document_ref_file_url").val());
            $("#serie_document_ref_file_poids").val($("#document_ref_file_poids").val());

            
            if($("#serie_document_ref_file_action").val() == "loaded" && addDocRefDropzone.dropzone.files[0].xhr == undefined){
                //le document n'a pas été modifié
                $("#serie_document_ref_file_action").val($("#document_ref_file_action").val());
            }else{
                if(addDocRefDropzone.dropzone.files[0].xhr == undefined){
                    //document nouvellement créer et mise à jour ensuite sans modifier la photo
                    $("#serie_document_ref_file_action").val($("#document_ref_file_action").val());
                }else{
                    //le document a été modifié
                    var fileDropZone = JSON.parse(addDocRefDropzone.dropzone.files[0].xhr.response);
                    if(fileDropZone.status == 'error'){
                        $("#addDocRefDropzone").next('span.formErrors')
                        .html('Une erreur a été rencontrée lors de l\'envoi de la photo');
                        return;
                    };
                    //Le document a été ajouté ou modifié
                    $("#serie_document_ref_file_titre").val(fileDropZone.fileName);
                    $("#serie_document_ref_file_url").val(fileDropZone.fileURI);
                    $("#serie_document_ref_file_poids").val(fileDropZone.fileSize);
                    $("#serie_document_ref_file_action").val('new');
                }
            }
            
            /*var docRefDropzoneUpdate = JSON.parse(addDocRefDropzone.dropzone.files[0].xhr.response);
            $("#serie_document_ref_file_id").val('new');
            $("#serie_document_ref_file_titre").val(docRefDropzoneUpdate.fileName);
            $("#serie_document_ref_file_url").val(docRefDropzoneUpdate.fileURI);
            $("#serie_document_ref_file_poids").val(docRefDropzoneUpdate.fileSize);*/
            
            //on réinitialise avant ajout du lien
            $("#serie_document_ref_download").html("");
            var a = document.createElement('a');
            $(a).attr('href', $("#serie_document_ref_file_url").val())
                .attr('target', "_blank")
                .html($("#serie_document_ref_file_titre").val())
                .appendTo($("#serie_document_ref_download")) ;
            
            $("#editRefDoc, #removeRefDoc").show();
            $("#addRefDoc").hide();
            $("#modalSerieRefDoc").modal('toggle');
        }
    })

    /*FIN DOC REFERENCE*/

    /* SONS DEBUT */
    
    $("#addSonSerie").click(function(){
        chargeModalSon(null);
        $("#modalSerieSon").modal('toggle');
    });

    function chargeModalSon(data){
        if (data == null) {
            $("input.form-son, select.form-son, textarea.form-son").val('');
            //Valeur par défault
            //Format standard
            $("#son_langue_id").val(2);
            addSonDropzone.dropzone.removeAllFiles(true);
        }else{
            $("#son_id").val(data.son_id);
            $("#son_titre").val(data.son_titre);
            $("#son_auteur").val(data.son_auteur);
            $("#son_presentation").val(data.son_presentation);
            $("#son_lien_paysage").val(data.son_lien_paysage);
            $("#son_date").val(data.son_date);
            $("#son_type").val(data.son_type);
            $("#son_format").val(data.son_format);
            $("#son_heure").val(data.son_heure);
            $("#son_type_mat").val(data.son_type_mat);
            $("#son_traitement").val(data.son_traitement);
            $("#son_protocole").val(data.son_protocole);
            $("#son_contexte").val(data.son_contexte);
            $("#son_condition_meteo").val(data.son_condition_meteo);
            $("#son_num_photo").val(data.son_num_photo);
            $("#son_lieu").val(data.son_lieu);
            $("#son_duree").val(data.son_duree);
            $("#son_langue_id").val(data.son_langue_id);
            $("#son_licence_id").val(data.son_licence_id);
            $("#son_struct_resp_id").val(data.son_struct_resp_id);
            $("#son_file_action").val(data.son_file_action);
            $("#son_file_name").val(data.son_file_name);
            $("#son_file_size").val(data.son_file_size);
            $("#son_file_url").val(data.son_file_url);
            addSonDropzone.dropzone.removeAllFiles(true);
            var mockFile = {name : data.son_file_name, size : data.son_file_size, accepted: true, kind: "sound", dataURL : PARAMETRES.url + '/files/' + data.son_file_url};
            addSonDropzone.dropzone.files.push(mockFile);
            addSonDropzone.dropzone.emit("addedfile", mockFile);            
            addSonDropzone.dropzone.emit('complete', mockFile);
        }
    }

    var tableSonsSerie = $("#dataTableSonsSerie").DataTable( {
        dom: "",
        paging:false,
        searching: false,
        language : PARAMETRES.dataTableFrancais,
        columns: [  
            {data: "son_id"}, {data: "son_titre"}, {data: "son_auteur"}, {data: "son_presentation"},  
            {data: "son_lien_paysage"}, {data: "son_date"}, {data: "son_type"}, {data: "son_format"},  
            {data: "son_heure"}, {data: "son_type_mat"}, {data: "son_traitement"}, {data: "son_protocole"},   
            {data: "son_contexte"}, {data: "son_condition_meteo"}, {data: "son_num_photo"}, {data: "son_lieu"}, {data: "son_duree"},   
            {data: "son_langue_id"}, {data: "son_licence_id"}, {data: "son_struct_resp_id"}, {data: "son_file_action"}, 
            {data: "son_file_name"}, {data: "son_file_size"}, {data: "son_file_url"}, {data: "son_action"}, {data: "son_download"}, {data: "action", width: "120px"}
        ],
        columnDefs: [   
            { 
                targets: [ 0, 2, 3, 4 ,5, 6, 7, 8, 9, 10, 11, 12, 13 ,14 ,15 ,16, 17, 18, 19, 20, 21, 22, 23, 24 ], 
                visible: false, 
                searchable: false
            }
        ],
        drawCallback: function ( settings ) {
            }
    } );

    $('#saveSerieSon').on('click', e => {
        var insertionChamp = {};
        //on boucle sur les champs pour détecter les erreurs et remplir le tableau du POST
        $(".form-son").each(function(index){
            if(($(this).val() == "" || $(this).val() == null) && this.required){
                $(this).addClass('is-invalid');
                $(window).scrollTop($(this).position().top);
                return;
            }
            $(this).removeClass('is-invalid');
            var id = $(this).attr('id');
            var value = $(this).val();
            insertionChamp[id] = value;
        });
        
        if(addSonDropzone.dropzone.files.length === 0){
            $("#addSonDropzone").next('span.formErrors')
            .html('Le son est obligatoire');
            return;
        }

        if($("#son_file_action").val() == "loaded" && addSonDropzone.dropzone.files[0].xhr == undefined){
            //On a pas touché au son
            var fileParam = {
                action : 'loaded',
                fileName: $("#son_file_name").val(),
                fileSize: $("#son_file_size").val(),
                fileURI : $("#son_file_url").val(),
            }
        }else{
            if(addSonDropzone.dropzone.files[0].xhr == undefined){
                //son nouvellement créer et mise à jour ensuite sans modifier le son
                var fileParam = {
                    action : $("#son_file_action").val(),
                    fileName: $("#son_file_name").val(),
                    fileSize: $("#son_file_size").val(),
                    fileURI : $("#son_file_url").val(),
                }
            }else{
                //le son a été modifiée
                var sonDropZone = JSON.parse(addSonDropzone.dropzone.files[0].xhr.response);
                if(sonDropZone.status == 'error'){
                    $("#addSonDropzone").next('span.formErrors')
                    .html('Une erreur a été rencontrée lors de l\'envoi du son');
                    return;
                };
                //Le son a été ajoutée ou modifiée
                var fileParam = {
                    action : 'new',
                    fileName: sonDropZone.fileName,
                    fileSize: sonDropZone.fileSize,
                    fileURI: sonDropZone.filePath,
                }
            }
        }
        
        if($(".is-invalid").length ==  0){
            var bTrouve = false; 
            for (var i=0; i<tableSonsSerie.data().length; i++) {
                if (tableSonsSerie.data()[i].son_id == $("#son_id").val()) {
                    tableSonsSerie.data()[i].son_titre = $("#son_titre").val();
                    tableSonsSerie.data()[i].son_auteur = $("#son_auteur").val();
                    tableSonsSerie.data()[i].son_presentation = $("#son_presentation").val();
                    tableSonsSerie.data()[i].son_lien_paysage = $("#son_lien_paysage").val();
                    tableSonsSerie.data()[i].son_date = $("#son_date").val();
                    tableSonsSerie.data()[i].son_type = $("#son_type").val();
                    tableSonsSerie.data()[i].son_format = $("#son_format").val();
                    tableSonsSerie.data()[i].son_heure = $("#son_heure").val();
                    tableSonsSerie.data()[i].son_type_mat = $("#son_type_mat").val();
                    tableSonsSerie.data()[i].son_traitement = $("#son_traitement").val();
                    tableSonsSerie.data()[i].son_protocole = $("#son_protocole").val();
                    tableSonsSerie.data()[i].son_contexte = $("#son_contexte").val();
                    tableSonsSerie.data()[i].son_condition_meteo = $("#son_condition_meteo").val();
                    tableSonsSerie.data()[i].son_num_photo = $("#son_num_photo").val();
                    tableSonsSerie.data()[i].son_lieu = $("#son_lieu").val();
                    tableSonsSerie.data()[i].son_duree = $("#son_duree").val();
                    tableSonsSerie.data()[i].son_langue_id = $("#son_langue_id").val();
                    tableSonsSerie.data()[i].son_licence_id = $("#son_licence_id").val();
                    tableSonsSerie.data()[i].son_struct_resp_id = $("#son_struct_resp_id").val();
                    tableSonsSerie.data()[i].son_file_action = fileParam.action;
                    tableSonsSerie.data()[i].son_file_name = fileParam.fileName;
                    tableSonsSerie.data()[i].son_file_size = fileParam.fileSize;
                    tableSonsSerie.data()[i].son_file_url = fileParam.fileURI;
                    tableSonsSerie.data()[i].son_download = "<a href='" + PARAMETRES.url + "/files/" + fileParam.fileURI +"' download>" + fileParam.fileName + "</a>";
                    //tableSonsSerie.data()[i].son_action = 'updated';
                    if(tableSonsSerie.data()[i].son_action != 'add'){
                        tableSonsSerie.data()[i].son_action = 'updated';
                    }
                    
                    // mise à jour du tableau
                    var ligne = tableSonsSerie.row(i).data();
                    tableSonsSerie.row(i).data(ligne).invalidate();
                    
                    bTrouve = true;
                }
            }
            if (!bTrouve){
                tableSonsSerie.row.add({
                    "son_id": 'new' + newIdSon,
                    "son_titre": $("#son_titre").val(),
                    "son_auteur": $("#son_auteur").val(),
                    "son_presentation": $("#son_presentation").val(),
                    "son_lien_paysage": $("#son_lien_paysage").val(),
                    "son_date": $("#son_date").val(),
                    "son_type": $("#son_type option:selected").val(),
                    "son_format": $("#son_format").val(),
                    "son_heure": $("#son_heure").val(),
                    "son_type_mat": $("#son_type_mat").val(),
                    "son_traitement": $("#son_traitement").val(),
                    "son_protocole": $("#son_protocole").val(),
                    "son_contexte": $("#son_contexte").val(),
                    "son_condition_meteo": $("#son_condition_meteo").val(),
                    "son_num_photo": $("#son_num_photo").val(),
                    "son_lieu": $("#son_lieu").val(),
                    "son_duree": $("#son_duree").val(),
                    "son_langue_id": $("#son_langue_id").val(),
                    "son_licence_id": $("#son_licence_id").val(),
                    "son_struct_resp_id": $("#son_struct_resp_id").val(),
                    "son_file_action": fileParam.action,
                    "son_file_name": fileParam.fileName,
                    "son_file_size": fileParam.fileSize,
                    "son_file_url": fileParam.fileURI,
                    "son_download": "<a href='" + PARAMETRES.url + "/files/" + fileParam.fileURI +"' download>" + fileParam.fileName + "</a>",
                    "son_action": 'add',
                    "action": '<a class="modify"><i class="c-light-blue-500 cur-p ti ti-pencil"></i></a><a class="remove"><i class="c-red-500 cur-p ti ti-trash"></i></a>'}
                ).draw();
                newIdSon++;
            }
            $("#modalSerieSon").modal('toggle');
        }
    });

    $('#dataTableSonsSerie tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        var data = tableSonsSerie.row( $(elem).parent().parent() ).data();
        chargeModalSon(data);
        $("#modalSerieSon").modal('toggle');
    });

    $('#dataTableSonsSerie tbody').on( 'click', 'a.remove', e => {
        var elem = e.currentTarget;
        var ligneIndex = $(elem).parent().parent();
        var data = tableSonsSerie.row( ligneIndex ).data();
        data.son_action = 'delete';
        // mise à jour du tableau
        tableSonsSerie.row(ligneIndex).data(data).invalidate();
        ligneIndex.addClass('rowDelete');
    });

    /* SONS FIN */

    /* DOCUMENTS DEBUT */
    
    $("#addDocumentSerie").click(function(){
        chargeModalDocument(null);
        $("#modalSerieDocument").modal('toggle');
    });

    function chargeModalDocument(data){
        if (data == null) {
            $("input.form-document, select.form-document, textarea.form-document").val('');
            //Valeur par défault
            //Format standard
            addDocumentDropzone.dropzone.removeAllFiles(true);
        }else{
            $("#document_id").val(data.document_id);
            $("#document_titre").val(data.document_titre);
            $("#document_legende").val(data.document_legende);
            $("#document_file_action").val(data.document_file_action);
            $("#document_file_name").val(data.document_file_name);
            $("#document_file_size").val(data.document_file_size);
            $("#document_file_url").val(data.document_file_url);
            addDocumentDropzone.dropzone.removeAllFiles(true);
            var mockFile = {name : data.document_file_name, size : data.document_file_size, accepted: true, kind: "sound", dataURL : PARAMETRES.url + '/files/' + data.document_file_url};
            addDocumentDropzone.dropzone.files.push(mockFile);
            addDocumentDropzone.dropzone.emit("addedfile", mockFile);            
            addDocumentDropzone.dropzone.emit('complete', mockFile);
        }
    }

    var tableDocumentsSerie = $("#dataTableDocumentsSerie").DataTable( {
        dom: "",
        paging:false,
        searching: false,
        language : PARAMETRES.dataTableFrancais,
        columns: [  
            {data: "document_id"}, {data: "document_titre"}, {data: "document_legende"}, {data: "document_file_action"}, 
            {data: "document_file_name"}, {data: "document_file_size"}, {data: "document_file_url"}, 
            {data: "document_action"}, {data: "document_download"}, {data: "action", width: "120px"}
        ],
        columnDefs: [   
            { 
                targets: [ 0, 2, 3, 4 ,5, 6, 7 ], 
                visible: false, 
                searchable: false
            }
        ],
        drawCallback: function ( settings ) {
            }
    } );

    $('#saveSerieDocument').on('click', e => {
        var insertionChamp = {};
        //on boucle sur les champs pour détecter les erreurs et remplir le tableau du POST
        $(".form-document").each(function(index){
            if(($(this).val() == "" || $(this).val() == null) && this.required){
                $(this).addClass('is-invalid');
                $(window).scrollTop($(this).position().top);
                return;
            }
            $(this).removeClass('is-invalid');
            var id = $(this).attr('id');
            var value = $(this).val();
            insertionChamp[id] = value;
        });
        
        if(addDocumentDropzone.dropzone.files.length === 0){
            $("#addDocumentDropzone").next('span.formErrors')
            .html('Le document est obligatoire');
            return;
        }

        if($("#document_file_action").val() == "loaded" && addDocumentDropzone.dropzone.files[0].xhr == undefined){
            //On a pas touché au document
            var fileParam = {
                action : 'loaded',
                fileName: $("#document_file_name").val(),
                fileSize: $("#document_file_size").val(),
                fileURI : $("#document_file_url").val(),
            }
        }else{
            if(addDocumentDropzone.dropzone.files[0].xhr == undefined){
                //document nouvellement créer et mise à jour ensuite sans modifier le document
                var fileParam = {
                    action : $("#document_file_action").val(),
                    fileName: $("#document_file_name").val(),
                    fileSize: $("#document_file_size").val(),
                    fileURI : $("#document_file_url").val(),
                }
            }else{
                //le document a été modifiée
                var documentDropZone = JSON.parse(addDocumentDropzone.dropzone.files[0].xhr.response);
                if(documentDropZone.status == 'error'){
                    $("#addDocumentDropzone").next('span.formErrors')
                    .html('Une erreur a été rencontrée lors de l\'envoi du document');
                    return;
                };
                //Le document a été ajoutée ou modifiée
                var fileParam = {
                    action : 'new',
                    fileName: documentDropZone.fileName,
                    fileSize: documentDropZone.fileSize,
                    fileURI: documentDropZone.filePath,
                }
            }
        }
        
        if($(".is-invalid").length ==  0){
            var bTrouve = false; 
            for (var i=0; i<tableDocumentsSerie.data().length; i++) {
                if (tableDocumentsSerie.data()[i].document_id == $("#document_id").val()) {
                    tableDocumentsSerie.data()[i].document_titre = $("#document_titre").val();
                    tableDocumentsSerie.data()[i].document_legende = $("#document_legende").val();
                    tableDocumentsSerie.data()[i].document_file_action = fileParam.action;
                    tableDocumentsSerie.data()[i].document_file_name = fileParam.fileName;
                    tableDocumentsSerie.data()[i].document_file_size = fileParam.fileSize;
                    tableDocumentsSerie.data()[i].document_file_url = fileParam.fileURI;
                    tableDocumentsSerie.data()[i].document_download = "<a href='" + PARAMETRES.url + "/files/" + fileParam.fileURI +"' download>" + fileParam.fileName + "</a>";
                    
                    //tableDocumentsSerie.data()[i].document_action = 'updated';
                    if(tableDocumentsSerie.data()[i].document_action != 'add'){
                        tableDocumentsSerie.data()[i].document_action = 'updated';
                    }
                    
                    // mise à jour du tableau
                    var ligne = tableDocumentsSerie.row(i).data();
                    tableDocumentsSerie.row(i).data(ligne).invalidate();
                    
                    bTrouve = true;
                }
            }
            if (!bTrouve){
                tableDocumentsSerie.row.add({
                    "document_id": 'new' + newIdDocument,
                    "document_titre": $("#document_titre").val(),
                    "document_legende": $("#document_legende").val(),
                    "document_file_action": fileParam.action,
                    "document_file_name": fileParam.fileName,
                    "document_file_size": fileParam.fileSize,
                    "document_file_url": fileParam.fileURI,
                    "document_download": "<a href='" + PARAMETRES.url + "/files/" + fileParam.fileURI +"' download>" + fileParam.fileName + "</a>",
                    "document_action": 'add',
                    "action": '<a class="modify"><i class="c-light-blue-500 cur-p ti ti-pencil"></i></a><a class="remove"><i class="c-red-500 cur-p ti ti-trash"></i></a>'}
                ).draw();
                newIdDocument++;
            }
            $("#modalSerieDocument").modal('toggle');
        }
    });

    $('#dataTableDocumentsSerie tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        var data = tableDocumentsSerie.row( $(elem).parent().parent() ).data();
        chargeModalDocument(data);
        $("#modalSerieDocument").modal('toggle');
    });

    $('#dataTableDocumentsSerie tbody').on( 'click', 'a.remove', e => {
        var elem = e.currentTarget;
        var ligneIndex = $(elem).parent().parent();
        var data = tableDocumentsSerie.row( ligneIndex ).data();
        data.document_action = 'delete';
        // mise à jour du tableau
        tableDocumentsSerie.row(ligneIndex).data(data).invalidate();
        ligneIndex.addClass('rowDelete');
    });

    /* DOCUMENTS FIN */

    /*LIENS EXTERNE DEBUT*/
    
    $("#addLienExtSerie").click(function(){
        $("#LienExtValue").removeClass('is-invalid');
        $("#LienExtValue").val("");
        $("#modalSerieLienExt").modal('toggle');
    });

    var tableLienExtSerie = $("#dataTableLienExtSerie").DataTable( {
        dom: "",
        paging:false,
        searching: false,
        language : PARAMETRES.dataTableFrancais,
        columns: [  
            {data: "lienext_id"}, {data: "lienext_value"}, {data: "lienext_download"},  {data: "action", width: "120px"}
        ],
        columnDefs: [   
            { 
                targets: [ 0, 1 ], 
                visible: false, 
                searchable: false
            }
        ],
        drawCallback: function ( settings ) {
            }
    } );

    $('#saveSerieLienExt').on('click', e => {
        
        if($("#LienExtValue").val() ==  ""){
            $("#LienExtValue").addClass('is-invalid');
            return;
        }
            var bTrouve = false; 
            for (var i=0; i<tableLienExtSerie.data().length; i++) {
                if (tableLienExtSerie.data()[i].lienext_id == $("#lienext_id").val()) {
                    tableLienExtSerie.data()[i].lienext_value = $("#LienExtValue").val();
                    tableLienExtSerie.data()[i].lienext_download = "<a href='" + $("#LienExtValue").val() +"' target='_blank'>" + $("#LienExtValue").val() + "</a>";
                    
                    // mise à jour du tableau
                    var ligne = tableLienExtSerie.row(i).data();
                    tableLienExtSerie.row(i).data(ligne).invalidate();
                    
                    bTrouve = true;
                }
            }
            if (!bTrouve){
                tableLienExtSerie.row.add({
                    "lienext_id": 'new' + newIdLien,
                    "lienext_value": $("#LienExtValue").val(),
                    "lienext_download": "<a href='" + $("#LienExtValue").val() +"' target='_blank'>" + $("#LienExtValue").val() + "</a>",
                    "action": '<a class="modify"><i class="c-light-blue-500 cur-p ti ti-pencil"></i></a><a class="remove"><i class="c-red-500 cur-p ti ti-trash"></i></a>'}
                ).draw();
                newIdLien++;
            }
            $("#modalSerieLienExt").modal('toggle');
    });

    $('#dataTableLienExtSerie tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        var data = tableLienExtSerie.row( $(elem).parent().parent() ).data();
        $("#LienExtValue").val(data.lienext_value);
        $("#LienExtId").val(data.lienext_id);
        $("#modalSerieLienExt").modal('toggle');
    });

    $('#dataTableLienExtSerie tbody').on( 'click', 'a.remove', e => {
        var elem = e.currentTarget;
        var ligneIndex = $(elem).parent().parent();
        tableLienExtSerie.row(ligneIndex).remove().draw( false );
    });

    /*LIENS EXTERNES FIN*/
    
    /* LIEN entre les select */
    if(PARAMETRES.linkSelectCommune){
        $("#serie_coverage_dep").on( 'change', (elem) => {
            $( "#serie_coverage_dep option:selected" ).each(function() {
                var dep_code = $( this ).data('code');
                var updateSelectedValue = $('#serie_coverage_com option:selected').val();
                if($('#serie_coverage_com option:selected').text().substring(0,2) != dep_code ){
                    updateSelectedValue = null;
                }
                $('#serie_coverage_com option').each(function() {
                    if ($( this ).text().substring(0,2) == dep_code){
                        $(this).show();
                        if (updateSelectedValue == null){
                            updateSelectedValue = $(this).val();
                        }
                    }else{
                        $(this).hide();
                    }
                });
                $('#serie_coverage_com').val(updateSelectedValue);
                $("#serie_coverage_com").trigger("chosen:updated");
            })
        })
        //Fire event after binding
        $("#serie_coverage_dep").trigger('change');
    }

    //A tester
    if(PARAMETRES.linkSelectUnitePaysage){
        $("#serie_coverage_ens_paysage").on( 'change', (elem) => {
            $( "#serie_coverage_ens_paysage option:selected" ).each(function() {
                var idEnsemblePays = $( this ).val();
                var updateSelectedValue = $('#serie_coverage_unite_paysage option:selected').val();
                if($('#serie_coverage_unite_paysage option:selected').data('ensemble') != idEnsemblePays ){
                    updateSelectedValue = null;
                }
                $('#serie_coverage_unite_paysage option').each(function() {
                    if ($( this ).data('ensemble') == idEnsemblePays){
                        $(this).show();
                        if (updateSelectedValue == null){
                            updateSelectedValue = $(this).val();
                        }
                    }else{
                        $(this).hide();
                    }
                });
                $('#serie_coverage_unite_paysage').val(updateSelectedValue);
                $("#serie_coverage_unite_paysage").trigger("chosen:updated");
            })
        })
        //Fire event after binding
        $("#serie_coverage_ens_paysage").trigger('change');
    }

    
    if(PARAMETRES.linkSelectStructureOpp){
        $("#serie_structure_opp").prop('disabled', 'disabled');
        
        $("#serie_objet_opp").on( 'change', (elem) => {
            var idStruct = $( "#serie_objet_opp option:selected" ).data('structure');
            $('#serie_structure_opp option[value="' + idStruct + '"]').prop('selected', true);
        })
    }
    
    if(PARAMETRES.identifiantSerieCalculAuto){

        $("#serie_objet_opp, #serie_id_inter_opp").on( 'change', (elem) => {
            //On verifie que l'OPP est bien renseigné
            if ($("#serie_objet_opp").val() == ""){
                return;
            }
            //en mode création
            if(elem.currentTarget.id == "serie_objet_opp" && $("#serie_id").val() == 'new'){
                //Récupération du tableau des évolutions
                $.ajax({
                    url: PARAMETRES.url + '/get/serie/newId',
                    data: {opp_id : $("#serie_objet_opp").val()},
                    async: false, // Mode synchrone
                    type : 'POST',
                    success: function(reponse, statut){
                        if (statut == "success") {
                            $("#serie_id_inter_opp").val(reponse.newid);
                        } else {
                            alert('Une erreur a été rencontrée lors de la génération de l\'identifiant interne (voir fonction sql get_next_identifiant_interne_serie())');
                        }
                    },
                    error : function(resultat, statut, erreur){
                        alert(erreur);
                    }
                });
            }

            //Si on modifie l'OPP => MAJ du code interne 
            var codeOPP = $("#serie_objet_opp option:selected").text().split(" ").join("").substring(0,8).toUpperCase();
            var identifiant = codeOPP + "-" + $("#serie_id_inter_opp").val();
            $("#serie_id_serie").val(identifiant);
            
        });
    }
    
    if(PARAMETRES.nomPhotoCalculAuto){
        
        $("#addPhotoSerie").click(function(){
            var identifiant_serie = $("#serie_id_serie").val();
            //Récupération du tableau des évolutions
            $.ajax({
                url: PARAMETRES.url + '/get/photo/newId',
                data: {serie_id : $("#serie_objet_opp").val()},
                async: false, // Mode synchrone
                type : 'POST',
                success: function(reponse, statut){
                    if (statut == "success") {
                        $("#photo_titre").val($("#serie_id_serie").val() + "-" + reponse.newid);
                    } else {
                        alert('Une erreur a été rencontrée lors de la génération de l\'identifiant interne (voir fonction sql get_next_identifiant_interne_serie())');
                    }
                },
                error : function(resultat, statut, erreur){
                    alert(erreur);
                }
            });

        });
    }
})

