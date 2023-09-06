import { PARAMETRES } from '../custom/parametre';

$(function() {
    /*$('.one-time').slick({
        infinite: true,
        speed: 1300,
        slidesToShow: 1,
        adaptiveHeight: true,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 6000,
        centerMode: true,
        variableWidth: true,
        //fade: true,
      });*/
      $('.one-time').slick({
        centerMode: true,
        centerPadding: 'auto',
        slidesToShow: 1,
        autoplay: true,
        autoplaySpeed: 6000,
      });
    
    $('.carrousel-actu').slick({
        dots: true,
        infinite: true,
        speed: 500,
        fade: true,
        cssEase: 'linear'
    });



      $("#savePhotoCarrousel").on("click", function() {
        //Création du tableau de parametre envoi
        var insertionChamp = {};

        if(importPhotoForm.dropzone.files.length != 0){
            var dropzonePhotoCarrousel = JSON.parse(importPhotoForm.dropzone.files[0].xhr.response);
            var titrePhotoCarousel = $("#titrephotocarousel").val();
            var ordrePhotoCarousel = $("#ordrephotocarousel").val();
            var actionCarrousel = $("#action-carrousel").val();
            
            $.ajax({
                //url : 'insertDb', 
                url : PARAMETRES.url + '/admin/actualite/insertCarouselActualite',
                type : 'POST',
                cache: true,
                data : {
                    dropzonePhotoCarrousel: dropzonePhotoCarrousel,
                    titrePhotoCarousel: titrePhotoCarousel,
                    ordrePhotoCarousel: ordrePhotoCarousel,
                    action:actionCarrousel
                },
                success: function (data) {
                    //Création de la mini photo
                    var col = $("<div>", {
                        class:"col-md-3 text-center p-20 mT-20",
                        id:"block-photo-carrousel-" + data.id,
                        style:"display: none;"
                    })
                    
                    $("<img>", {
                        class:"img-fluid mx-auto",
                        src: PARAMETRES.url + '/files/' + data.url, 
                        style:"width: 100px; height: 50px;"
                    }).appendTo(col);
                    
                    $("<p>", {
                        class:"text-dark font-weight-normal"
                    }).html(data.title)
                    .appendTo(col);
                    
                    $("<p>", {
                        class:"supprimerPhoto",
                        "data-id": data.id
                    }).html('<i class="c-red-500 cur-p ti ti-trash"></i>')
                    .jConfirm().on('confirm', function(e){
                        var carrouselPhotoId = $(this).data("id");
                        supprimerPhotoCarrousel(carrouselPhotoId)
                    })
                    .appendTo(col);

                    $("#content-photo-carrousel").append(col)
                    col.fadeIn("slow")

                    //On vide les champs
                    $("#titrephotocarousel").val("")
                    $("#ordrephotocarousel").val("")
                    importPhotoForm.dropzone.removeAllFiles(true);

                    //location.reload();
                }
            });

        } else {
            //erreur
        }  
    });

    

    $('.supprimerPhoto').jConfirm().on('confirm', function(e){
        var carrouselPhotoId = $(this).data("id");
        supprimerPhotoCarrousel(carrouselPhotoId)

    });

});

function supprimerPhotoCarrousel(carrouselPhotoId){        
    var url = PARAMETRES.url + '/admin/remove/photoActualite/' + carrouselPhotoId;
        
    $.ajax({
        url: url,
        type:"POST",
        data: {
            "carrouselPhotoId":carrouselPhotoId,
        },
        success: function (data) {
            if(data.status == 'ok'){
                $("#block-photo-carrousel-" + carrouselPhotoId).fadeOut("slow", function(){
                    $(this).remove()
                })
            }
        },
    }); 
}

        
    
