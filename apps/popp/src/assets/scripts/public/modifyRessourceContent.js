import { PARAMETRES } from '../custom/parametre';
import { flashMessage } from '../flashMessage/flashMessage';
    
$(function() {
    var ressourceTable = $('#ressourceTable').DataTable({
            "paging":   false,
            "ordering": true,
            "info":     false,
            searching: false,
            language : PARAMETRES.dataTableFrancais,
            rowId: 0,
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "visible": false,
                    "searchable": false
                }
            ]
        });

    var ressourceDocTable = $('#dataTableDocumentsRessource').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     false,
        searching: false,
        language : PARAMETRES.dataTableFrancais,
        columns: [  
            {data: "ressource_doc_id"}, {data: "ressource_doc_titre"}, {data: "ressource_doc_download"},
            {data: "ressource_doc_file_action"}, {data: "ressource_doc_file_name"}, {data: "ressource_doc_file_size"}, {data: "ressource_doc_file_url"}, 
            {data: "action", width: "120px"}
        ],
        "columnDefs": [
            {
                "targets": [ 0, 3, 4, 5, 6 ],
                "visible": false,
                "searchable": false
            }
        ]
    });
});

//export default (function () {
    $("#modifRessourceContent").click(function(){
        $("#consultBlock").toggle();
        $("#modifBlock").show();
        $("#consultRessourceContent").show();
        $("#modifRessourceContent").toggle();
    });    
    
    $("#consultRessourceContent").click(function(){
        $("#consultBlock").show();
        $("#modifBlock").toggle();
        $("#consultRessourceContent").toggle();
        $("#modifRessourceContent").show();
    });
    
    $("#validationRessourceContent").click(function(){
        var ressourceId = $("#ressourceId");
        var ressourceIdValue = ressourceId.val();
        
        var ressourceTitre = $("#ressourceTitre");
        var ressourceTitreValue = ressourceTitre.val();
        
        var ressourceQuestion = $("#ressourceQuestion");
        var ressourceQuestionValue = ressourceQuestion.val();
        
        var ressourceDesc = $("#ressourceDesc");
        var ressourceDescValue = ressourceDesc.val();
        
        var ressourceNumOrdreValue = $("#ressourceNumOrdre").val();
        
        /*var ressourceDoc = {documents :[]};
        $('#filesBody > tr').each(function(row) {
            var document = {
                "ressourceDocId": $(this).find('.docId').html(),
                "ressourceDocOldId": $(this).find('.docOldId').html(),
                "ressourceDocName": $(this).find('.docNom').html(),
                "ressourceDocSize": $(this).find('.docPoids').html(),
                "ressourceDocDate": Date.now(),
                "ressourceDocUrl": $(this).find('.docUrl').html(),
            };
            ressourceDoc.documents.push(document);
        });*/
        
        var ressourceLogo = {documents :[]};;
        if($("#ressourceLogoId").val()){
            var logo = {
                "ressourceLogoId": $("#ressourceLogoId").val(),
                "ressourceLogoName": $("#ressourceLogoTitre").val(),
                "ressourceLogoURI": $("#ressourceLogoURL").val(),
                "ressourceLogoSize": $("#ressourceLogoPoids").val(),
                "ressourceLogoDate": Date.now(),
            };
            ressourceLogo.documents.push(logo);
        };
        
        
        $(".ressourceRequired").each(function(index){
            if($(this).val() === "" && this.required){
                $(this).addClass('is-invalid');
                $(window).scrollTop($(this).position().top);
                //$('body').animate({ scrollTop: $(this).position().top }, 500);
                return;
            }
            $(this).removeClass('is-invalid');
        });
        
        if($(".is-invalid").length ===  0){
            $.ajax({
                url: PARAMETRES.url + '/admin/update/ressource/' + ressourceIdValue,
                type:"POST",
                data:{
                    "ressourceId":ressourceIdValue,
                    "ressourceTitre":ressourceTitreValue,
                    "ressourceQuestion":ressourceQuestionValue,
                    "ressourceDesc":ressourceDescValue,
                    "ressourceNumOrdre" : ressourceNumOrdreValue,
                    //"ressourceDocs":JSON.stringify(ressourceDoc),
                    "ressourceLogo":JSON.stringify(ressourceLogo),
                },
                success: function (data) {
                    if(data.status == 'ok'){
                        //Dans le cas ou on a déja enregistré le fichier
                        $("#messageConfirmModifRessource")
                            .removeClass('alert-danger')
                            .addClass('alert alert-success ta-c w-100')
                            .html("La ressource a été modifiée.")
                            .fadeIn(1000)
                            .delay(2000)
                            .fadeOut(1000);
                        setTimeout(function () {window.location.href = PARAMETRES.url + '/show/ressource/' + ressourceIdValue;},2000);

                    }
                },
                error : function (){
                    $("#messageConfirmModifRessource")
                        .addClass('alert alert-danger ta-c w-100')
                        .html("Une erreur a été rencontrée lors de l'enregistrement.")
                        .fadeIn(500);
                }
            });
        }
    });

    $("#createRessource").click(function(){
        var ressourceIdValue =  "new";
        
        var ressourceTitre = $("#addTitleRessource");
        var ressourceTitreValue = ressourceTitre.val();
        
        var ressourceQuestion = $("#addQuestionRessource");
        var ressourceQuestionValue = ressourceQuestion.val();
        
        var ressourceDesc = $("#addDescRessource");
        var ressourceDescValue = ressourceDesc.val();
        
        var ressourceNumOrdreValue = $("#addNumOrdreRessource").val();
        
        var ressourceDocData = {documents :[]};;
        if(addRessourceDoc.dropzone.files.length != 0){
            var fileDocRessource = JSON.parse(addRessourceDoc.dropzone.files[0].xhr.response);
            var document = {
                "ressourceDocId": 'new',
                "ressourceDocName": fileDocRessource.fileName,
                "ressourceDocURI": fileDocRessource.fileURI,
                "ressourceDocSize": fileDocRessource.fileSize ,
                "ressourceDocDate": fileDocRessource.fileDate,
            };
            ressourceDocData.documents.push(document);
        };
        
        var ressourceLogo = {documents :[]};;
        if(addRessourceLogo.dropzone.files.length != 0){
            var fileLogoRessource = JSON.parse(addRessourceLogo.dropzone.files[0].xhr.response);
            var logo = {
                "ressourceLogoId": 'new',
                "ressourceLogoName": fileLogoRessource.fileName,
                "ressourceLogoURI": fileLogoRessource.fileURI,
                "ressourceLogoSize": fileLogoRessource.fileSize,
                "ressourceLogoDate": Date.now(),
            };
            ressourceLogo.documents.push(logo);
        };
        
        $(".ressourceRequired").each(function(index){
            if($(this).val() === "" && this.required){
                $(this).addClass('is-invalid');
                $(window).scrollTop($(this).position().top);
                //$('body').animate({ scrollTop: $(this).position().top }, 500);
                return;
            }
            $(this).removeClass('is-invalid');
        });
        
        if($(".is-invalid").length ===  0){
            $.ajax({
                url: PARAMETRES.url + '/admin/create/ressource',
                type:"POST",
                data: {
                    "ressourceId":ressourceIdValue,
                    "ressourceTitre":ressourceTitreValue,
                    "ressourceQuestion":ressourceQuestionValue,
                    "ressourceDesc":ressourceDescValue,
                    "ressourceNumOrdre":ressourceNumOrdreValue,
                    "ressourceTitreDoc":$("#addTitreDoc").val(),
                    "ressourceDocs":JSON.stringify(ressourceDocData),
                    "ressourceLogo":JSON.stringify(ressourceLogo),
                },
                success: function (data) {
                    if(data.status == 'ok'){
                        //Dans le cas ou on a déja enregistré le fichier
                        $("#messageConfirmCreateRessource")
                            .removeClass('alert-danger')
                            .addClass('alert alert-success ta-c w-100')
                            .html("La ressource a été créée.")
                            .fadeIn(1000)
                            .delay(2000)
                            .fadeOut(1000);
                            setTimeout(function () {window.location.href = PARAMETRES.url + '/show/ressources/';},2000);
                        $("#modalRessource").modal('hide');
                    }
                },
                error : function (){
                    $("#messageConfirmModifRessource")
                        .addClass('alert alert-danger ta-c w-100')
                        .html("Une erreur a été rencontrée lors de l'enregistrement.")
                        .fadeIn(500);
                }
            });
        }
    });
    
    $('#ressourceTable a.remove').click(function(){
        if(confirm("Vous êtes sur le point de supprimer une ressource. Souhaitez vous continuer ?")){
          var ressourceId = ressourceTable.row(this.parentElement.parentElement).data()[0];
          $.ajax({
            url: PARAMETRES.url + '/admin/remove/ressource/' + ressourceId,
            success: function (data) {
                if(data.status == 'ok'){
                    //Dans le cas ou on a déja enregistré le fichier
                    $("#messageConfirmCreateRessource")
                        .removeClass('alert-danger')
                        .addClass('alert alert-success ta-c w-100')
                        .html("La ressource a été supprimée.")
                        .fadeIn(1000)
                        .delay(2000)
                        .fadeOut(1000);
                        //setTimeout(function () {window.location.href = PARAMETRES.url + '/show/ressources/';},2000);
                }
            },
            error : function (){
                $("#messageConfirmModifRessource")
                    .addClass('alert alert-danger ta-c w-100')
                    .html("Une erreur a été rencontrée lors de la suppression de la ressource.")
                    .fadeIn(500);
            }
          });
          ressourceTable.row(this.parentElement.parentElement).remove().draw( false );
        }
    });
    
    //Gestion des documents des ressources :
    /*$(".editDocRessource").click(function(){
        if (addRessourceDoc.dropzone.files[0]!=null){
            addRessourceDoc.dropzone.removeFile(addRessourceDoc.dropzone.files[0]);
        }
        var fileId = $(this).attr('docId');
        $("#ressourceDocFileId").attr("value", fileId);
        $("#ressourceDocFileTitre").val($("#ressourceDocTitre" + fileId ).html());
        var mockFile = {name : $("#ressourceDocTitre" + fileId +" a").html(), size : $("#ressourceDocPoids" + fileId).html(), accepted: true, kind: "file", dataURL : $("#ressourceDocTitre" + fileId +" a").attr('href')};
        addRessourceDoc.dropzone.files.push(mockFile);
        addRessourceDoc.dropzone.emit("addedfile", mockFile);
        addRessourceDoc.dropzone.emit('complete', mockFile);

        $("#modalRessourceFile").modal('toggle');
    });*/

    $('#dataTableDocumentsRessource tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        var data = ressourceDocTable.row( $(elem).parent().parent() ).data();
        addRessourceDoc.dropzone.removeAllFiles(true);

        var fileId =  data.ressource_doc_id;
        $("#ressourceDocFileId").val(fileId);

        $("#ressourceDocFileTitre").val(data.ressource_doc_titre);
        $("#document_ressource_file_action").val(data.ressource_file_action);
        $("#document_ressource_file_name").val(data.ressource_file_name);
        $("#document_ressource_file_size").val(data.ressource_file_size);
        $("#document_ressource_file_url").val(data.ressource_file_url);
        var mockFile = {name : data.ressource_doc_file_name, size : data.ressource_doc_file_size, accepted: true, kind: "file", dataURL : data.ressource_doc_file_url};
        addRessourceDoc.dropzone.files.push(mockFile);
        addRessourceDoc.dropzone.emit("addedfile", mockFile);
        addRessourceDoc.dropzone.emit('complete', mockFile);

        $("#modalRessourceFile").modal('toggle');
        /*chargeModalDocumentAnnexe(data);
        $("#modalDocumentAnnexe").modal('toggle');*/
    });

    $("#addDocRessource").click(function(){
        addRessourceDoc.dropzone.removeAllFiles(true);
        $("#ressourceDocFileId").val('new');
        $("#ressourceDocFileTitre").val("");
        $("input.form-document-ressource").val('');

        $("#messageErreurModifRessource")
            .removeClass("alert alert-danger ta-c w-100")
            .html("");
        $("#modalRessourceFile").modal('toggle');
    });
    
    $("#saveRessourceDoc").click(function(){
        
        if(addRessourceDoc.dropzone.files.length == 0){
            $("#messageErreurModifRessource")
                .addClass('alert alert-danger ta-c w-100')
                .html("Aucun fichier déposé");
            return;
        }
        
        if($("#ressourceDocFileTitre").val() == ""){
            $("#messageErreurModifRessource")
                .addClass('alert alert-danger ta-c w-100')
                .html("Le titre est obligatoire");
            return;
        }

        if($("#document_ressource_file_action").val() == "loaded" && addRessourceDoc.dropzone.files[0].xhr == undefined){
            //On a pas touché au document
            var fileParam = {
                action : 'loaded',
                fileName: $("#document_ressource_file_name").val(),
                fileSize: $("#document_ressource_file_size").val(),
                fileURI : $("#document_ressource_file_url").val(),
            }
        }else{
            if(addRessourceDoc.dropzone.files[0].xhr == undefined){
                //document nouvellement créer et mise à jour ensuite sans modifier le document
                var fileParam = {
                    action : $("#document_ressource_file_action").val(),
                    fileName: $("#document_ressource_file_name").val(),
                    fileSize: $("#document_ressource_file_size").val(),
                    fileURI : $("#document_ressource_file_url").val(),
                }
            }else{
                //le document a été modifiée
                var documentRessourceDropZone = JSON.parse(addRessourceDoc.dropzone.files[0].xhr.response);
                if(documentRessourceDropZone.status == 'error'){
                    $("#messageErreurModifRessource")
                    .addClass('alert alert-danger ta-c w-100')
                    .html('Une erreur a été rencontrée lors de l\'envoi du document');
                    return;
                };
                //Le document a été ajoutée ou modifiée
                var fileParam = {
                    action : 'new',
                    fileName: documentRessourceDropZone.fileName,
                    fileSize: documentRessourceDropZone.fileSize,
                    fileURI: documentRessourceDropZone.filePath,
                }
            }
        }
        //Si on est la, pas d'erreur
        $("#messageErreurModifRessource").removeClass("alert alert-danger ta-c w-100");

        var ressourceDocId = $('#ressourceDocFileId').val();
        var ressourceId = $('#ressourceId').val();
        $.post({
            type: "POST",
            url: PARAMETRES.url + '/admin/modify/ressource/document',
            data: {
                ressource_id : ressourceId,
                document_ressource_id : ressourceDocId,
                document_ressource_titre : $("#ressourceDocFileTitre").val(),
                document_ressource_file_action : fileParam.action,
                document_ressource_file_name: fileParam.fileName,
                document_ressource_file_size: fileParam.fileSize,
                document_ressource_file_url: fileParam.fileURI,
            }
        }).done(function( data ) {
            if (ressourceDocId == 'new'){
                ressourceDocTable.row.add({
                    "ressource_doc_id": data.documentRessourceId,
                    "ressource_doc_titre": $("#ressourceDocFileTitre").val(),
                    "ressource_doc_file_action": fileParam.action,
                    "ressource_doc_file_name": fileParam.fileName,
                    "ressource_doc_file_size": fileParam.fileSize,
                    "ressource_doc_file_url": fileParam.fileURI,
                    "ressource_doc_download": "<a href='" + data.documentRessourceFileUrl +"' download>" + $("#ressourceDocFileTitre").val() + "</a>",
                    "action": '<a class="modify"><i class="c-light-blue-500 cur-p ti ti-pencil"></i></a><a class="remove"><i class="c-red-500 cur-p ti ti-trash"></i></a>'}
                ).draw();

                $("#messageConfirmCreateRessource")
                    .removeClass('alert-danger')
                    .addClass('alert alert-success ta-c w-100')
                    .html("Un document a été ajouté.")
                    .fadeIn(1000)
                    .delay(2000)
                    .fadeOut(1000);
            }else{
                for (var i=0; i<ressourceDocTable.data().length; i++) {
                    if (ressourceDocTable.data()[i].ressource_doc_id == $("#ressourceDocFileId").val()) {
                        ressourceDocTable.data()[i].ressource_doc_titre = $("#ressourceDocFileTitre").val();
                        ressourceDocTable.data()[i].ressource_doc_file_action = fileParam.action;
                        ressourceDocTable.data()[i].ressource_doc_file_name = fileParam.fileName;
                        ressourceDocTable.data()[i].ressource_doc_file_size = fileParam.fileSize;
                        ressourceDocTable.data()[i].ressource_doc_file_url = fileParam.fileURI;
                        ressourceDocTable.data()[i].ressource_doc_download = "<a href='" + data.documentRessourceFileUrl +"' download>" + $("#ressourceDocFileTitre").val() + "</a>";
                        
                        // mise à jour du tableau
                        var ligne = ressourceDocTable.row(i).data();
                        ressourceDocTable.row(i).data(ligne).invalidate();

                        $("#messageConfirmCreateRessource")
                            .removeClass('alert-danger')
                            .addClass('alert alert-success ta-c w-100')
                            .html("Un document a été modifié.")
                            .fadeIn(1000)
                            .delay(2000)
                            .fadeOut(1000);
                    }
                }
            }
            $("#modalRessourceFile").modal('toggle');
        }).fail(function() {
            alert( "error" );
        });
/*
        //if(addRessourceDoc.dropzone.files.length !== 0){
            var updateFileId = $("#ressourceDocFileId").attr("value");
            if(addRessourceDoc.dropzone.files[0].xhr == undefined){
                //Le document n'a pas été modifié
                var elem = $("#ressourceDocTitre" + updateFileId);
                $("#ressourceDocTitre" + updateFileId).html($("#ressourceDocFileTitre").val());
            }else{
                var addDocDropzone = JSON.parse(addRessourceDoc.dropzone.files[0].xhr.response);
                
                if (updateFileId === 'new'){
                    $("#filesBody").append(
                        "<tr class='ressourceDocumentation'><td class='docId'>new</td><td class='docOldId'>new</td><td class='docNom'>" + $("#ressourceDocFileTitre").val() + "</td><td class='docOldId'>new</td><td class='docNom'>" + $("#ressourceDocFileTitre").val() + "</td><td class='docLink'><a href='" + PARAMETRES.url + "/files/" + addDocDropzone.fileURI +"'>" + $("#ressourceDocFileTitre").val() + "</a></td><td class='docPoids' >" + addDocDropzone.fileSize + "</td><td class='docDate' >" + Date.now() + "</td><td class='docUrl' style='display:none'>" + addDocDropzone.fileURI + "</td></td></tr>"
                    );
                }else{
                    $("#ressourceDocId" + updateFileId).html('new');
                    $("#ressourceDocTitre" + updateFileId).html($("#ressourceDocFileTitre").val());
                    $("#ressourceDocURL" + updateFileId).html(addDocDropzone.fileURI);
                    $("#ressourceDocLink" + updateFileId).html($("#ressourceDocFileTitre").val());
                    $("#ressourceDocPoids" + updateFileId).html(addDocDropzone.fileSize);
                }
            }
//            //on réinitialise avant ajout du lien
//            $("#ressourceDocDownload").html("");
//            var a = document.createElement('a');
//            $(a).attr('href', addDocDropzone.fileURI)
//                .attr('target', "_blank")
//                .html(addDocDropzone.fileName)
//                .appendTo($("#ressourceDocDownload")) ;
//            
            $("#editDocRessource, #removeDocRessource").show();
        //}else{
            
        //}
        $("#modalRessourceFile").modal('toggle');*/
    });
    
    $('#dataTableDocumentsRessource tbody').on( 'click', 'a.remove', e => {
        if(confirm("Vous êtes sur le point de supprimer un document de ressource. Souhaitez vous continuer ?")){
            var elem = e.currentTarget;
            var ligneIndex = $(elem).parent().parent();
            var data = ressourceDocTable.row( ligneIndex ).data();
            var ressourceId = $('#ressourceId').val();
            //TODO gérer si new
            $.ajax({
                url: PARAMETRES.url + '/admin/remove/ressource/document/' + data.ressource_doc_id,
                success: function (data) {
                    if(data.status == 'ok'){
                        //Dans le cas ou on a déja enregistré le fichier
                        $("#messageConfirmModifRessource")
                            .removeClass('alert-danger')
                            .addClass('alert alert-success ta-c w-100')
                            .html("Le document de la ressource a été supprimée.")
                            .fadeIn(1000)
                            .delay(2000)
                            .fadeOut(1000);
                            setTimeout(function () {
                                //window.location.href = PARAMETRES.url + '/show/ressource/' + ressourceId;
                            },2000);
                    }
                },
                error : function (){
                    $("#messageConfirmModifRessource")
                        .addClass('alert alert-danger ta-c w-100')
                        .html("Une erreur a été rencontrée lors de la suppression du document.")
                        .fadeIn(500);
                }
              });
              ressourceDocTable.row(ligneIndex).remove().draw( false );
        }
    });

    /*$(".removeDocRessource").click(function(){
      if(confirm("Vous êtes sur le point de supprimer un document de ressource. Souhaitez vous continuer ?")){
        var ressourceDocId = $(this).attr('docId');
        var ressourceId = $('#ressourceId').val();
        if (ressourceDocId === 'new'){
              this.parentElement.parentElement.remove();
              return;
        };
        $.ajax({
            url: PARAMETRES.url + '/admin/remove/ressource/document/' + ressourceDocId,
            success: function (data) {
                if(data.status == 'ok'){
                    //Dans le cas ou on a déja enregistré le fichier
                    $("#messageConfirmModifRessource")
                        .removeClass('alert-danger')
                        .addClass('alert alert-success ta-c w-100')
                        .html("Le document de la ressource a été supprimée.")
                        .fadeIn(1000)
                        .delay(2000)
                        .fadeOut(1000);
                        setTimeout(function () {window.location.href = PARAMETRES.url + '/show/ressource/' + ressourceId;},2000);
                }
            },
            error : function (){
                $("#messageConfirmModifRessource")
                    .addClass('alert alert-danger ta-c w-100')
                    .html("Une erreur a été rencontrée lors de la suppression du document.")
                    .fadeIn(500);
            }
          });
      }
    });*/
    
    //Gestion des logos des ressources :
    $("#editLogoRessource").click(function(){
        if (addRessourceLogo.dropzone.files[0]!=null){
            addRessourceLogo.dropzone.removeFile(addRessourceLogo.dropzone.files[0]);
        }
        var fileId = $(this).attr('docId');
        $("#ressourceLogoId").attr("value", fileId);
        $("#modalRessourceLogo").modal('toggle');
    });
    $("#addLogoRessource ").click(function(){
        if (addRessourceDoc.dropzone.files[0]!=null){
            addRessourceDoc.dropzone.removeFile(addRessourceDoc.dropzone.files[0]);
        }
        $("#ressourceLogoId").attr("value", 'new');
        $("#modalRessourceLogo").modal('toggle');
    });
    function removeRessourceLogo(){
        $("#ressourceLogoId").val('delete');
        $("#ressourceLogoTitre").val("");
        $("#ressourceLogoURL").val("");
        $("#ressourceLogoPoids").val("");
        $("#ressourceLogoDownload").html("");
        $("#editLogoRessource, #removeLogoRessource").hide();
        $("#addLogoRessource").show();
    }
    
    //Validation de la popup
    $("#saveRessourceLogo").click(function(){
        if(addRessourceLogo.dropzone.files.length !== 0){
            var addRessourceLogoDropzone = JSON.parse(addRessourceLogo.dropzone.files[0].xhr.response);
            $("#ressourceLogoId").val('new');
            $("#ressourceLogoTitre").val(addRessourceLogoDropzone.fileName);
            $("#ressourceLogoURL").val(addRessourceLogoDropzone.fileURI);
            $("#ressourceLogoPoids").val(addRessourceLogoDropzone.fileSize);
            
            //on réinitialise avant ajout du lien
            $("#ressourceLogoDownload").html("");
            var a = document.createElement('a');
            $(a).attr('href', addRessourceLogoDropzone.fileURI)
                .attr('target', "_blank")
                .html(addRessourceLogoDropzone.fileName)
                .appendTo($("#ressourceLogoDownload")) ;
            
            $("#editLogoRessource, #removeLogoRessource").show();
            $("#addLogoRessource").hide();
        }else{
            removeRessourceLogo();
        }
        $("#modalRessourceLogo").modal('toggle');
    });
    

    
$('#deleteRessource').jConfirm().on('confirm', function(e){
    //recupère l'élement cliqué
    var ressourceId = $('#deleteRessource').attr('ressource');
   
    //Appel au serveur                
    $.ajax({
        url: PARAMETRES.url + '/admin/remove/ressource/' + ressourceId,
    })
    .done(function( data, message ) {
        //quand l'appel est terminé
        //si OK
        if(data.status == "ok"){
            flashMessage("success", "Succès", "La ressource a été supprimé");

            window.location.href = PARAMETRES.url + '/show/ressources/';

            ressourceTable.row(this.parentElement.parentElement).remove().draw( false );

        } 
    })

    .fail(function(data, message) {
        //si pas OK
        if (data.status){
             flashMessage("danger", "Erreur", "La ressource ne peut pas être supprimé");
        }
           
    })

});
