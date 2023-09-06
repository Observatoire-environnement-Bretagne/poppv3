import { PARAMETRES } from '../custom/parametre';
    
$(function() {
    var faqTable = $('#faqTable').DataTable({
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
});

//export default (function () {
    $("#modifFaqContent").click(function(){
        $("#consultBlock").toggle();
        $("#modifBlock").show();
        $("#consultFaqContent").show();
        $("#modifFaqContent").toggle();
    });    
    
    $("#consultFaqContent").click(function(){
        $("#consultBlock").show();
        $("#modifBlock").toggle();
        $("#consultFaqContent").toggle();
        $("#modifFaqContent").show();
    });
    
    $("#supprimerFaqContent").click(function(){
        if(confirm("Vous êtes sur le point de supprimer une question de la FAQ.")){
            var faqId = $("#faqId").val();
            $.ajax({
                url: PARAMETRES.url + '/admin/remove/faq/' + faqId,
                success: function (data) {
                    if(data.status == 'ok'){
                        window.location.href = PARAMETRES.url + '/show/faqs/';
                    }
                },
                error : function (){
                    alert("Une erreur a été rencontrée");
                }
            }); 
        }
    });
    
    $("#editDocFaq, #addDocFaq ").click(function(){
        if (addFaqDoc.dropzone.files[0]!=null){
            addFaqDoc.dropzone.removeFile(addFaqDoc.dropzone.files[0]);
        }
        $("#faqDocFileTitre").val("");
        $("#modalFaqFile").modal('toggle');
    });
    
    function removeFileDocFaq(){
        $("#faqDocId").val('delete');
        $("#faqDocTitre").val("");
        $("#faqDocURL").val("");
        $("#faqDocPoids").val("");
        $("#faqDocDownload").html("");
        $("#editDocFaq, #removeDocFaq").hide();
        $("#addDocFaq").show();
    }
    
    $("#saveFaqDoc").click(function(){
        if(addFaqDoc.dropzone.files.length !== 0){
            var addFaqDocDropzone = JSON.parse(addFaqDoc.dropzone.files[0].xhr.response);
            $("#faqDocId").val('new');
            $("#faqDocTitre").val(addFaqDocDropzone.fileName);
            $("#faqDocURL").val(addFaqDocDropzone.fileURI);
            $("#faqDocPoids").val(addFaqDocDropzone.fileSize);
            
            $("#faqDocFileName").val($('#faqDocFileTitre').val());
            
            //on réinitialise avant ajout du lien
            var fileName = addFaqDocDropzone.fileName;
            if($("#faqDocFileTitre").val() != ""){
                fileName = $("#faqDocFileTitre").val();
            }
            $("#faqDocDownload").html("");
            var a = document.createElement('a');
            $(a).attr('href', addFaqDocDropzone.fileURI)
                .attr('target', "_blank")
                .html(fileName)
                .appendTo($("#faqDocDownload")) ;
            
            $("#editDocFaq, #removeDocFaq").show();
            $("#addDocFaq").hide();
        }else{
            removeFileDocFaq();
        }
        $("#modalFaqFile").modal('toggle');
    });
    
    $("#validationFaqContent").click(function(){
        var faqId = $("#faqId");
        var faqIdValue = faqId.val();
        
        var faqTitre = $("#faqTitre");
        var faqTitreValue = faqTitre.val();
        
        var faqHeader = $("#faqHeader");
        var faqHeaderValue = faqHeader.val();
        
        var faqQuestion = $("#faqQuestion");
        var faqQuestionValue = faqQuestion.val();
        
        var faqReponse = $("#faqReponse");
        var faqReponseValue = faqReponse.val();
                
        var faqDate = $("#faqDate");
        var faqDateValue = faqDate.val();
        
        var faqNumOrdreValue = $("#faqNumOrdre").val();
        //var faqFileTitreValue = $("#faqDocFileTitre").val();
        
        
        var faqDocData = {documents :[]};;
        if($("#faqDocId").val()){
            var document = {
                "faqDocId": $("#faqDocId").val(),
                "faqDocName": $("#faqDocTitre").val(),
                "faqDocURI": $("#faqDocURL").val(),
                "faqDocSize": $("#faqDocPoids").val(),
                "faqDocFileName": $("#faqDocFileName").val(),
                "faqDocDate": Date.now(),
            };
            faqDocData.documents.push(document);
        };
        
        $(".faqRequired").each(function(index){
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
                url: PARAMETRES.url + '/admin/update/faq/' + faqIdValue,
                type:"POST",
                data: {
                    "faqId":faqIdValue,
                    "faqTitre":faqTitreValue,
                    "faqHeader":faqHeaderValue,
                    "faqQuestion":faqQuestionValue,
                    "faqReponse":faqReponseValue,
                    "faqDate":faqDateValue,
                    "faqNumOrdre":faqNumOrdreValue,
                    "faqDoc": JSON.stringify(faqDocData),
                },
                success: function (data) {
                    if(data.status == 'ok'){
                        //Dans le cas ou on a déja enregistré le fichier
                        $("#messageConfirmModifFaq")
                            .removeClass('alert-danger')
                            .addClass('alert alert-success ta-c w-100')
                            .html("La question a été modifiée.")
                            .fadeIn(1000)
                            .delay(2000)
                            .fadeOut(1000);
                        setTimeout(function () {window.location.href = PARAMETRES.url + '/show/faq/' + faqIdValue;},2000);

                    }
                },
                error : function (){
                    $("#messageConfirmModifFaq")
                        .addClass('alert alert-danger ta-c w-100')
                        .html("Une erreur a été rencontrée lors de l'enregistrement.")
                        .fadeIn(500);
                }
            });
        }
    });
    
    $("#createFaq").click(function(){
        var faqIdValue =  "new";
        
        var faqTitre = $("#addTitleFaq");
        var faqTitreValue = faqTitre.val();
        
        var faqQuestion = $("#addQuestionFaq");
        var faqQuestionValue = faqQuestion.val();
        
        var faqDesc = $("#addDescFaq");
        var faqDescValue = faqDesc.val();        
        
        var faqReponse = $("#addReponseFaq");
        var faqReponseValue = faqReponse.val();
        
        var faqDate = $("#addDateFaq");
        var faqDateValue = faqDate.val();
        
        var faqNumOrdreValue = $("#addNumOrdreFaq").val();
        var faqTitreDocValue = $("#addTitreDoc").val();
        
        var faqDocData = {documents :[]};;
        if(addFaqDoc.dropzone.files.length != 0){
            var fileDocFaq = JSON.parse(addFaqDoc.dropzone.files[0].xhr.response);
            var document = {
                "faqDocId": 'new',
                "faqDocName": fileDocFaq.fileName,
                "faqDocURI": fileDocFaq.fileURI,
                "faqDocSize": fileDocFaq.fileSize ,
                "faqDocDate": fileDocFaq.fileDate,
            };
            faqDocData.documents.push(document);
        };
        
        $(".faqRequired").each(function(index){
            if($(this).val() == "" && this.required){
                $(this).addClass('is-invalid');
                $(window).scrollTop($(this).position().top);
                //$('body').animate({ scrollTop: $(this).position().top }, 500);
                return;
            }
            $(this).removeClass('is-invalid');
        });
        
        if($(".is-invalid").length ===  0){
            $.ajax({
                url: PARAMETRES.url + '/admin/create/faq',
                type:"POST",
                data: {
                    "faqId":faqIdValue,
                    "faqTitre":faqTitreValue,
                    "faqQuestion":faqQuestionValue,
                    "faqHeader":faqDescValue,
                    "faqReponse":faqReponseValue,
                    "faqDate":faqDateValue,
                    "faqDesc":faqDescValue,
                    "faqNumOrdre":faqNumOrdreValue,
                    "faqTitreDoc": faqTitreDocValue,
                    "faqDoc": JSON.stringify(faqDocData),
                },
                success: function (data) {
                    if(data.status == 'ok'){
                        //Dans le cas ou on a déja enregistré le fichier
                        $("#messageConfirmCreateFaq")
                            .removeClass('alert-danger')
                            .addClass('alert alert-success ta-c w-100')
                            .html("La question a été créée.")
                            .fadeIn(1000)
                            .delay(2000)
                            .fadeOut(1000);
                            //setTimeout(function () {window.location.href = PARAMETRES.url + '/show/faqs/';},2000);
                        $("#modalFaq").modal('hide');
                    }
                },
                error : function (){
                    $("#infoFaq")
                        .addClass('alert alert-danger ta-c w-100')
                        .html("Une erreur a été rencontrée lors de l'enregistrement.")
                        .fadeIn(500);
                }
            });  
        }
    });
    
    /*$('#faqTable a.remove').click(function(){
        if(confirm("Vous êtes sur le point de supprimer une question. Souhaitez vous continuer ?")){
          var faqId = faqTable.row(this.parentElement.parentElement).data()[0];

          $.ajax({
            url: PARAMETRES.url + '/admin/remove/faq/' + faqId,
            success: function (data) {
                if(data.status == 'ok'){
                    //Dans le cas ou on a déja enregistré le fichier
                    $("#messageConfirmCreateFaq")
                        .removeClass('alert-danger')
                        .addClass('alert alert-success ta-c w-100')
                        .html("La question a été supprimée.")
                        .fadeIn(1000)
                        .delay(2000)
                        .fadeOut(1000);
                        //setTimeout(function () {window.location.href = PARAMETRES.url + '/show/faqs/';},2000);

                }
            },
            error : function (){
                $("#messageConfirmCreateFaq")
                    .addClass('alert alert-danger ta-c w-100')
                    .html("Une erreur a été rencontrée lors de la suppression de la question.")
                    .fadeIn(500);
            }
          });
          faqTable.row(this.parentElement.parentElement).remove().draw( false );
        }
    });*/

    $("#removeDocFaq").click(function(){
      if(confirm("Vous êtes sur le point de supprimer une question. Souhaitez vous continuer ?")){
        var doc = $("#faqDocId");
        var faqDocId = doc.val();


        $("#faqDocId").val('');
        $("#faqDocTitre").val('');
        $("#faqDocURL").val('');
        $("#faqDocPoids").val('');
        
        $("#faqDocFileName").val('');
        $("#faqDocDownload").html("");
        
        $("#editDocFaq, #removeDocFaq").hide();
        $("#addDocFaq").show();

        if (faqDocId != 'new'){
            $.ajax({
                url: PARAMETRES.url + '/admin/remove/faq/document/' + faqDocId,
                success: function (data) {
                    if(data.status == 'ok'){
                        //Dans le cas ou on a déja enregistré le fichier
                        $("#messageConfirmModifFaq")
                            .removeClass('alert-danger')
                            .addClass('alert alert-success ta-c w-100')
                            .html("La question a été supprimée.")
                            .fadeIn(1000)
                            .delay(2000)
                            .fadeOut(1000);
                            setTimeout(function () {window.location.href = PARAMETRES.url + '/show/faqs/';},2000);
                    }
                },
                error : function (){
                    $("#messageConfirmModifFaq")
                        .addClass('alert alert-danger ta-c w-100')
                        .html("Une erreur a été rencontrée lors de la suppression du document.")
                        .fadeIn(500);
                }
            });
        }
      }
    });