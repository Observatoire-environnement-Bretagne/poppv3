import 'slick-carousel';
import "slick-carousel/slick/slick.css";

//Import d'OL pour trouver les coordonnées Lambert93 EPSG:2154
import {Tile as TileLayer, Vector as VectorLayer} from 'ol/layer';
import {OSM, Vector as VectorSource} from 'ol/source';
import View from 'ol/View';
import 'ol/ol.css';
import Map from 'ol/Map';
import {fromLonLat}  from 'ol/proj';
import GeoJSON from 'ol/format/GeoJSON';
import Circle from 'ol/geom/Circle';
import {Circle as CircleStyle, Fill, Stroke, Style} from 'ol/style';

import proj4 from 'proj4';

import { PARAMETRES } from '../custom/parametre';

export default (function () {
  var styleSlickTrack;
  var isDocRef = false;
  
    $("#carouselPhoto").on('init', function(slick){
      var activeSlide =  $("#carouselPhoto div.slick-current");
      reloadChangeCarousel(activeSlide, 1);
      //styleSlickTrack = $('.slick-track').attr('style');
    });

    var slider = $("#carouselPhoto").slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        fade: true,
        asNavFor: '#carouselPhotoMiniature',
        adaptiveHeight: true
      });
      
    $('#carouselPhotoMiniature').slick({
      slidesToShow: 5,
      slidesToScroll: 1,
      asNavFor: '#carouselPhoto',
      dots: true,
      /*centerMode: true,*/
      focusOnSelect: true,
    });

    $('#carouselPhoto').on('beforeChange', function(event, slick, currentSlide, nextSlide){
      var activeSlide =  $(slick.$slides[nextSlide]);
      reloadChangeCarousel(activeSlide, nextSlide +1);
    });

    
    $('.full-screen').click(function(){
      var src = $(this).find('img').attr('src');
      $('#img-full-screen').attr('src', src);
      $('#div-img-full').toggleClass('fadeIn fadeOut');
      //.attr('src', this.attr('data-lazy'));
      //$('#modal-fullscreen').modal('toggle');
    });

    $(".closeModal").click(function(){
      //$('#modal-fullscreen').modal('toggle');
      $('#div-img-full').toggleClass('fadeIn fadeOut');
      //$('.slick-track').attr('style', styleSlickTrack);
    })

    function reloadChangeCarousel(activeSlide, index){
      $("#photoIndex").html(index);
      var type = activeSlide.find('div.image').data('type');
      if (type == 'docref'){
        isDocRef = true;
        $('#descPhoto').hide();
        $('#descDocRef').show();
        $("#docType").html('Document de référence');
        if(activeSlide.find('div.image').data('desc') != "" ){
          var dateDoc = activeSlide.find('div.image').data('date') + '<br>' + activeSlide.find('div.image').data('desc');
        }else{
          var dateDoc = activeSlide.find('div.image').data('date')
        }
        $("#photoDate").html(dateDoc);
        $("#tableauEvolutionPhoto").html("<p>Document de référence<p>");
      }else{
        $('#descPhoto').show();
        $('#descDocRef').hide();
        $("#docType").html('Photo');
        $("#photoDate").html(activeSlide.find('div.image').data('date'));
        $(".table-primary").removeClass('table-primary');
        var indexTableInfo = index;
        if (isDocRef){
          indexTableInfo = index-1;
        }
        $("#recap-" + (indexTableInfo)).addClass('table-primary');
        
      
        //Récupération du tableau des évolutions
        $.ajax({
          url: PARAMETRES.url + '/get/evolution/photo/' + activeSlide.find('div.image').data('pk'),
          async: false, // Mode synchrone
          success: function(reponse, statut){
            if (statut == "success") {
              $("#tableauEvolutionPhoto").html(reponse);
            } else {
              alert('Une erreur a été rencontrée lors de la récupération des évolutions');
            }
          },
          error : function(resultat, statut, erreur){
            alert(erreur);
          }
        });
      }
      $("#photoAuteur").html(activeSlide.find('div.image').data('auteur'));
      $("#photoLicence").html(activeSlide.find('div.image').data('licence'));
      //$('.slick-track').attr('style', styleSlickTrack);
    };

    $("#download").click(function(){
      //$("#export").addClass('fadeOut', 0);
      var serieId = $("#serieId");
      var serieId = serieId.text();
      
      var textLongitudeWGS84 = $("#longitudeWGS84").text();
      var longitudeWGS84 = textLongitudeWGS84.split(" ");
      var X = longitudeWGS84[2];
      
      var textLatitudeWGS84 = $("#latitudeWGS84").text();
      var latitudeWGS84 = textLatitudeWGS84.split(" ");
      var Y = latitudeWGS84[2];     
      
      var coordinates = [parseFloat(X), parseFloat(Y)];
      
//      proj4.defs("EPSG:2154","+proj=lcc +lat_1=49 +lat_2=44 +lat_0=46.5 +lon_0=3 +x_0=700000 +y_0=6600000 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs");
//      proj4.defs('WGS84', "+title=WGS 84 (long/lat) +proj=longlat +ellps=WGS84 +datum=WGS84 +units=degrees");
//
//      var coordinates2154 = proj4('WGS84', 'EPSG:2154', coordinates);
      var EPSG2154 = "+proj=lcc +lat_1=49 +lat_2=44 +lat_0=46.5 +lon_0=3 +x_0=700000 +y_0=6600000 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs";
      var WGS84 = "+title=WGS 84 (long/lat) +proj=longlat +ellps=WGS84 +datum=WGS84 +units=degrees";

      var coordinates2154 = proj4(WGS84, EPSG2154, [coordinates[0],coordinates[1]]);

      $.ajax({
        url: PARAMETRES.url + '/public/get/serie/download/' + serieId,
        type:"POST",
        data: {"coordinates2154":coordinates2154},
        success: function(reponse, statut){
          //$("#loader").addClass('fadeOut');
          if (statut == "success") {
            window.open(reponse.fileUrl);
            /*var element = document.createElement('a');
            $(element).attr('href', encodeURIComponent(reponse.fileUrl))
            .attr('download', 'fiche_serie')
            .hide()
            .appendTo($("body"))
            .click()
            .remove();*/
          } else {
            alert('Une erreur a été rencontrée lors de la récupération des évolutions');
          }
        },
        error : function(resultat, statut, erreur){
          //$("#loader").addClass('fadeOut');
          alert(erreur);
        }
      });
    });
    
    
    $("#export").click(function(){
      if(!confirm('En cliquant sur OK, vous vous engagez à respecter les licences d\'utilisation inhérentes aux fichiers téléchargés')){
        return;
      }
        //$("#export").addClass('fadeOut', 0);
        var serieId = $("#serieId");
        var serieId = serieId.text();
        
        var changePrec = [];
        $("#tableauEvolutionPhoto table tr").each(function(i) {
            var arrayOfThisRow = [];
            if (i === 0){
                var tableData = $(this).find('th');
            }else{
                var tableData = $(this).find('td');
            }
            if (tableData.length > 0) {
                tableData.each(function() { arrayOfThisRow.push($(this).text()); });
                changePrec.push(arrayOfThisRow);
            }
        });
        
        var changeDuree = [];
        $("table#recap-table-duree tr").each(function(i) {
            var arrayOfThisRow = [];
            if (i === 0){
                var tableData = $(this).find('th');
            }else{
                var tableData = $(this).find('td');
            }
            if (tableData.length > 0) {
                tableData.each(function() { arrayOfThisRow.push($(this).text()); });
                changeDuree.push(arrayOfThisRow);
            }
        });
        
        $.ajax({
            url: PARAMETRES.url + '/public/get/serie/export/' + serieId,
            type:"POST",
            data:{"changePrec":changePrec , "changeDuree":changeDuree},
            success: function(reponse, statut){
                //$("#loader").addClass('fadeOut');
                if (statut == "success") {
                    window.open(reponse.fileUrl, "_blank", null);
                    /*var element = document.createElement('a');
                    $(element).attr('href', encodeURIComponent(reponse.fileUrl))
                    .attr('download', 'fiche_serie')
                    .hide()
                    .appendTo($("body"))
                    .click()
                    .remove();*/
                } else {
                    alert('Une erreur a été rencontrée lors de la récupération des évolutions');
                }
            },
            error : function(resultat, statut, erreur){
                //$("#loader").addClass('fadeOut');
                alert(erreur);
            }
        });
    });
    
    //Export des changements d'une photo
    $("#exporterChangementPhoto").click(function(){
      //$("#export").addClass('fadeOut', 0);
      var serieId = $("#serieId").html();
      
      var changePrec = [];
      $("#tableauEvolutionPhoto table tr").each(function(i) {
          var arrayOfThisRow = [];
          if (i === 0){
              var tableData = $(this).find('th');
          }else{
              var tableData = $(this).find('td');
          }
          if (tableData.length > 0) {
              tableData.each(function() { arrayOfThisRow.push($(this).text()); });
              changePrec.push(arrayOfThisRow);
          }
      });
      
      var changeDuree = [];
      $("table#recap-table-duree tr").each(function(i) {
          var arrayOfThisRow = [];
          if (i === 0){
              var tableData = $(this).find('th');
          }else{
              var tableData = $(this).find('td');
          }
          if (tableData.length > 0) {
              tableData.each(function() { arrayOfThisRow.push($(this).text()); });
              changeDuree.push(arrayOfThisRow);
          }
      });
      
      $.ajax({
          url: PARAMETRES.url + '/public/get/serie/exportChangement/' + serieId + '/' + $("#photoIndex").html(),
          type:"POST",
          data:{"changePrec":changePrec , "changeDuree":changeDuree},
          success: function(reponse, statut){
              //$("#loader").addClass('fadeOut');
              if (statut == "success") {
                  window.open(reponse.fileUrl, "_blank", null);
                  /*var element = document.createElement('a');
                  $(element).attr('href', encodeURIComponent(reponse.fileUrl))
                  .attr('download', 'fiche_serie')
                  .hide()
                  .appendTo($("body"))
                  .click()
                  .remove();*/
              } else {
                  alert('Une erreur a été rencontrée lors de la récupération des évolutions');
              }
          },
          error : function(resultat, statut, erreur){
              //$("#loader").addClass('fadeOut');
              alert(erreur);
          }
      });
    });
    
    $("#createSerieComment").click(function(){
      if($("#serie_comment_text").val() == ''){
        $("#messageConfirmComment")
        .addClass('alert alert-danger ta-c w-100')
        .html("Le commentaire est obligatoire.")
        .fadeIn(500);
      }else{
            
        $.ajax({
          url: PARAMETRES.url + '/public/set/photo/comment/' + $("#serie_comment_photo").val(),
          type:"POST",
          data:{"commentaire":$("#serie_comment_text").val()},
          success: function(reponse, statut){
            $("#messageConfirmComment")
                .removeClass('alert-danger')
                .addClass('alert alert-success ta-c w-100')
                .html("Le commentaire va être soumis à validation.")
                .fadeIn(1000)
                .delay(2000)
                .fadeOut(1000);
            $("#serie_comment_text").val("");
          },
          error : function(resultat, statut, erreur){
              //$("#loader").addClass('fadeOut');
              alert(erreur);
          }
      });
          
        //$("#serie_comment_text").val("");

      }

    })
}());