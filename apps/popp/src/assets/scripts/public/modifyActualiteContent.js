import { ajax } from 'jquery';
import { PARAMETRES } from '../custom/parametre';
import { flashMessage } from '../flashMessage/flashMessage';


$(function() {

    // bouton pour valider une actualite 
    $(".js-save-actualite").on("click", function(){
        //var actualiteEditor = CKEDITOR.instances.editor.getData();
        var actualiteEditor = myEditor.getData();
        //var actualiteEditor = window.editor.getData();
        
        var actualiteId = $("#actualiteId").val();
        var ordreActualite = $("#ordreactualite").val();
        var url = PARAMETRES.url + '/admin/save/actualite';
        
        //On va également enregistrer les photos qui sont en cours de création pour le carrousel
        $.ajax({
            url: url,
            type:"POST",
            data: {
                "actualiteEditor": actualiteEditor,
                "actualiteId": actualiteId,
                "ordreActualite": ordreActualite
                },
        })
        .done(function( data, message ) {
            //quand l'appel est terminé
            //si OK
            if(data.status == "ok"){
                window.location.href = PARAMETRES.url + '/show/actualite/';
            } 
        })
        .fail(function(data, message) {
            //si pas OK
            if (data.status){
               /* $("#messageConfirmModifActualite")
                .addClass('alert alert-danger ta-c w-100')
                .html("Une erreur a été rencontrée lors de l'enregistrement.")
                .fadeIn(500);*/
            }
               
        })
    })

    /*Bouton delete de chaque actualité*/
    $('.deleteActualite').jConfirm().on('confirm', function(e){
        var actualiteId = $(this).data("id");
        var url = PARAMETRES.url + '/admin/remove/actualite/' + actualiteId;
        $.ajax({
            url: url,
            type:"POST",
            data: {
                "actualiteId":actualiteId,
            },
            success: function (data) {
                if(data.status == 'ok'){
                    location.reload();
                    
                }
            }
        }); 
    
    });

    //Initialisation de ck-viewer - Media
    $('.ck-viewable figure.media oembed').each(function() {
        var url = $(this).attr("url")
        var divMedia = ""
        //Vidéo youtube
        if(url.includes('youtube')){
            var id = url.split("?v=")[1]; //sGbxmsDFVnE
            var embedlink = "http://www.youtube.com/embed/" + id;
    
            divMedia = $("<div>", {
                class:"youtube_player",
                videoID: id,
                /*width:"width",
                height:"height",
                theme:"theme dark",
                rel:"rel 1", 
                controls:"controls 1",
                showinfo:"showinfo 1",
                autoplay:"autoplay 0",
                mute:"mute 0",
                srcdoc:"srcdoc",
                loop:"loop 0",
                loading:"loading 1"*/
            })
        }else{
            //son ou vidéo
            if(url.includes('mp3') || url.includes('wav')){
                var divSon = `<audio controls ` +
                    `src="${ url }">` +
                    `Votre navigateur ne supporte pas l'élément` +
                '<code>audio</code></audio>'
                divMedia = $("<div>").append(divSon)
            }
            else{
                var divVideo = `<video width="620" height="405" controls="controls" >` +
                    `<source src="${ url }" type="video/mp4" />` +
                    `Votre navigateur ne supporte pas l'élément vidéo ou le format de la vidéo. ` +
                '</video>'
                divMedia = $("<div>").append(divVideo)
            }
        }

        /*var iframe = $("<iframe>", {
            width:"420",
            height:"315",
            src:"//www.youtube-nocookie.com/embed/xm-Ppiu8DeA?theme=theme dark&rel=rel 1&controls=controls&showinfo=showinfo 1&autoplay=autoplay&mute=mute 0&loop=loop"//embedlink
        })*/

        $(this).parent().append(divMedia)
    })

})









