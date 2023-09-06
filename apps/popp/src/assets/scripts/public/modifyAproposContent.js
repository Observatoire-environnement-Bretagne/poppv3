import { PARAMETRES } from '../custom/parametre';

//export default (function () {
    $("#addApropos").click(function(){
        $("#titleApropos").val('');
        $("#labelBtnApropos").val('');
        $("#urlBtnApropos").val('');
        $("#AProposNumOrdre").val('');
        myEditor.setData("");
        //CKEDITOR.instances.descApropos.setData("");
        $("#modalApropos").modal('show');
        $("#idApropos").val('new');
    });    
    
    $(".updateApropos").click(function(){
        var id = $(this).attr("id");
        var titleContent = $("#aproposTitre"+id).html();
        var descContent = $("#aproposDesc"+id).html();
        var docUrlContent = $("#urlBtnApropos"+id).attr("action");
        var docLabelContent = $("#labelBtnApropos"+id).attr("value");
        var aproposNumOrdre = $("#aproposNumOrdre"+id).val();

        $("#titleApropos").val(titleContent);
        $("#urlBtnApropos").val(docUrlContent);
        $("#labelBtnApropos").val(docLabelContent);
        $("#AProposNumOrdre").val(aproposNumOrdre);
        myEditor.setData(descContent);
        //CKEDITOR.instances.descApropos.setData(descContent);

//        $("#descApropos").val(descContent);
        $("#modalApropos").modal('show');
        $("#idApropos").val(id);
    });
    
    $("#createApropos").click(function(){       
        var aproposId = $("#idApropos");
        var aproposIdValue = aproposId.val();
        
        var aproposTitre = $("#titleApropos");
        var aproposTitreValue = aproposTitre.val();
        
        var aproposDocUrl = $("#urlBtnApropos");
        var aproposDocUrlValue = aproposDocUrl.val();
        
        var aproposDocLabel = $("#labelBtnApropos");
        var aproposDocLabelValue = aproposDocLabel.val();
        
        var aproposNumOrdre = $("#AProposNumOrdre").val();
        
        //var aproposDescValue = CKEDITOR.instances.descApropos.getData();
        var aproposDescValue = myEditor.getData();
        
        if (aproposIdValue === 'new'){
            var url = PARAMETRES.url + '/admin/create/apropos';
        }else{
            var url = PARAMETRES.url + '/admin/update/apropos/' + aproposIdValue;
        }
        
        $.ajax({
            url: url,
            type:"POST",
            data: {
                "aproposId":aproposIdValue,
                "aproposTitre":aproposTitreValue,
                "aproposDesc":aproposDescValue,
                "aproposDocUrlValue":aproposDocUrlValue,
                "aproposDocLabelValue":aproposDocLabelValue,
                "aproposNumOrdre" : aproposNumOrdre
            },
            success: function (data) {
                if(data.status == 'ok'){
                    //Dans le cas ou on a déja enregistré le fichier
                    $("#messageConfirmModifApropos")
                        .removeClass('alert-danger')
                        .addClass('alert alert-success ta-c w-100')
                        .html("La rubrique 'A propos' a été créée.")
                        .fadeIn(1000)
                        .delay(2000)
                        .fadeOut(1000);
                        setTimeout(function () {window.location.href = PARAMETRES.url + '/show/apropos/';},2000);
                    $("#modalApropos").modal('hide');
                }
            },
            error : function (){
                $("#messageConfirmModifApropos")
                    .addClass('alert alert-danger ta-c w-100')
                    .html("Une erreur a été rencontrée lors de l'enregistrement.")
                    .fadeIn(500);
            }
        });  
    });

    $('.deleteApropos').jConfirm().on('confirm', function(e){
        
            var id = $(this).attr("delete");
            var url = PARAMETRES.url + '/admin/remove/apropos/' + id;

            $.ajax({
                url: url,
                type:"POST",
                data: {
                    "aproposId":id,
                },
                success: function (data) {
                    if(data.status == 'ok'){
                        //Dans le cas ou on a déja enregistré le fichier
                        $("#messageConfirmModifApropos")
                            .removeClass('alert-danger')
                            .addClass('alert alert-success ta-c w-100')
                            .html("La rubrique 'A propos' a été supprimée.")
                            .fadeIn(1000)
                            .delay(2000)
                            .fadeOut(1000);
                            setTimeout(function () {window.location.href = PARAMETRES.url + '/show/apropos/';},2000);
                        $("#modalApropos").modal('hide');
                    }
                },
                error : function (){
                    $("#messageConfirmModifApropos")
                        .addClass('alert alert-danger ta-c w-100')
                        .html("Une erreur a été rencontrée lors de la suppression.")
                        .fadeIn(500);
                }
            }); 
        
    });


    
    
    $("#contactAproposSubmit").click(function(){
        var nom = $("#contactAproposNom").val();
        var mail = $("#contactAproposMail").val();
        var tel = $("#contactAproposTel").val();
        var msg = $("#contactAproposMsg").val();

        var url = PARAMETRES.url + '/contact/apropos/';

        $.ajax({
            url: url,
            type:"POST",
            data: {
                "expediteurNom" : nom,
                "expediteurMail" : mail,
                "expediteurTel" : tel,
                "expediteurMsg" : msg,
            },
            success: function (data) {
                if(data.status == 'ok'){
                    //Dans le cas ou on a déja enregistré le fichier
                    $("#messageConfirmMailApropos")
                        .removeClass('alert-danger')
                        .addClass('alert alert-success ta-c w-100')
                        .html("Le message a bien été envoyé.")
                        .fadeIn(1000)
                        .delay(2000)
                        .fadeOut(1000);
                        //setTimeout(function () {window.location.href = PARAMETRES.url + '/show/apropos/';},2000);
                        //$("#modalApropos").modal('hide');
                }
            },
            error : function (){
                $("#messageConfirmMailApropos")
                    .addClass('alert alert-danger ta-c w-100')
                    .html("Une erreur a été rencontrée lors de l'envoi du mail.")
                    .fadeIn(500);
            }
        }); 
    });
    

