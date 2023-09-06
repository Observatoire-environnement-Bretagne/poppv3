import { PARAMETRES } from '../custom/parametre';
import { flashMessage } from '../flashMessage/flashMessage';

$(function() {

    $('a.deleteSelectionPanier').jConfirm().on('confirm', function(e){
        var panierSelectionId = $(this).data("id");
        var url = PARAMETRES.url + '/public/remove/panierSelection/' + panierSelectionId;
        $.ajax({
            url: url,
        })
        .done(function( data, message ) {
            if(data.status == "ok"){
                flashMessage("success", "Succès", data.message);
                setTimeout(function(){
                    location.reload();
                }, 200)
            } 
        })
        .fail(function(data, message) {
            //si pas OK
            if (data.status){
                 flashMessage("danger", "Erreur", "Suppression impossible");
                 location.reload();
            }
        })      

    });

    $('a.viderPanierSelection').jConfirm().on('confirm', function(e){
        var url = PARAMETRES.url + '/public/remove/viderPanier';
        $.ajax({
            url: url,
        })
        .done(function( data, message ) {
            if(data.status == "ok"){
                location.reload();
                flashMessage("success", "Succès", "Le panier a été vidé");
            } 
        })

        .fail(function(data, message) {
            //si pas OK
            if (data.status){
                 flashMessage("danger", "Erreur", "Le panier ne peut pas être vidé");
            }
               
        })      

    });

    $("#btn-generate-fichier-panier").on("click", function(){
        var urlGetSessionPanier = $(this).data("url-get-session-panier");
        var url = $(this).data("url");
        var erreurMessage = $(this).data("erreur-message");
        $.ajax({
          type: "POST", 
          url: urlGetSessionPanier
        })
        .done(function(data){
            if(Object.keys(data.serieSelection).length > 100 ){
                flashMessage("danger", "Erreur", erreurMessage);
            }else{
                if(confirm("Le traitement peut être long, souhaitez-vous continuer?")){
                    $("#loader").removeClass('fadeOut');
                    
                    var a = document.createElement("a");
                    document.body.appendChild(a);
                    a.style = "display: none";
                    //var url = window.URL.createObjectURL(blob);
                    a.href = url;
                    a.target = "_blank";
                    var dateTime = Date.now();
                    //a.download = "export_series_" + dateTime + ".xlsx";
                    a.click();
                
                    document.body.removeChild(a);
                    $("#loader").addClass('fadeOut');
                }
            }
            //$("#loader").toggleClass('fadeIn fadeOut');
        })
    })

    $("#btn-generate-fiches-terrains-panier").on("click", function(){
        var urlGetSessionPanier = $(this).data("url-get-session-panier");
        var url = $(this).data("url");
        $.ajax({
          type: "POST", 
          url: urlGetSessionPanier
        })
        .done(function(data){
            if(Object.keys(data.serieSelection).length > 100 ){
                flashMessage("danger", "Erreur", "L'export est limité à 100 élement");
            }else{
                if(confirm("Le traitement peut être long, souhaitez vous continuer?")){
                    $("#loader").removeClass('fadeOut');
                    
                    var a = document.createElement("a");
                    document.body.appendChild(a);
                    a.style = "display: none";
                    //var url = window.URL.createObjectURL(blob);
                    a.href = url;
                    var dateTime = Date.now();
                    a.download = "export_fiches_terrains_" + dateTime + ".zip";
                    a.click();
                
                    document.body.removeChild(a);
                    $("#loader").addClass('fadeOut');
                }
            }
            //$("#loader").toggleClass('fadeIn fadeOut');
        })
    })

    //export excel
/*
    var a = document.createElement("a");

        document.body.appendChild(a);

        a.style = "display: none";

        //var url = window.URL.createObjectURL(blob);

        a.href = urlGenerateXlsx;

        var dateTime = Date.now();

        a.download = "export_panier_" + dateTime + ".xlsx";

        a.click();

        document.body.removeChild(a);*/


});
