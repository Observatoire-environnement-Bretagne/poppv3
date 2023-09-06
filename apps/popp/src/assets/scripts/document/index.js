
import { PARAMETRES } from '../custom/parametre';
import { flashMessage } from '../flashMessage/flashMessage';

var newIdDocument = 0;
$(function() {
    $("#addDocumentAnnexe").click(function(){
        chargeModalDocumentAnnexe(null);
        $("#modalDocumentAnnexe").modal('toggle');
    });

    function chargeModalDocumentAnnexe(data){
        $("#document_annexe_opp").chosen({disable_search_threshold: 10});
        $("#document_annexe_opp").val("").trigger('chosen:updated');
        $("#document_annexe_opp_chosen").css('width', '100%');
        if (data == null) {
            $("input.form-document-annexe, select.form-document-annexe, textarea.form-document-annexe").val('');
            //Valeur par défault
            //Format standard
            $("#document_annexe_id").val('new');
            addDocumentAnnexeDropzone.dropzone.removeAllFiles(true);
            $("#document_annexe_all_opp").prop("checked", false);
        }else{
            $("#document_annexe_id").val(data.document_annexe_id);
            $("#document_annexe_titre").val(data.document_annexe_titre);
            $("#document_annexe_desc").val(data.document_annexe_desc);
            //$("#document_annexe_opp_id").val(data.document_annexe_opp);
            $("#document_annexe_file_action").val('loaded');
            $("#document_annexe_file_name").val(data.document_annexe_file_name);
            $("#document_annexe_file_size").val(data.document_annexe_file_size);
            $("#document_annexe_file_url").val(data.document_annexe_file_url);
            if(data.document_annexe_opp_id == 'tous'){
                $("#document_annexe_all_opp").prop("checked", true);
            }else{
                $("#document_annexe_all_opp").prop("checked", false);
                $("#document_annexe_opp").val(data.document_annexe_opp_id.replace(' ', '').split(',')).trigger('chosen:updated');
            }
            
            addDocumentAnnexeDropzone.dropzone.removeAllFiles(true);
            var mockFile = {name : data.document_annexe_file_name, size : data.document_annexe_file_size, accepted: true, kind: "sound", dataURL : PARAMETRES.url + '/files/' + data.document_annexe_file_url};
            addDocumentAnnexeDropzone.dropzone.files.push(mockFile);
            addDocumentAnnexeDropzone.dropzone.emit("addedfile", mockFile);            
            addDocumentAnnexeDropzone.dropzone.emit('complete', mockFile);
        }
    }

    var tableDocuments = $("#dataTableDocuments").DataTable( {
        dom: "ftp",
        paging:false,
        searching: true,
        language : PARAMETRES.dataTableFrancais,
        columns: [  
            {data: "document_annexe_id"}, {data: "document_annexe_titre"}, {data: "document_annexe_desc"}, {data: "document_annexe_opp_id"}, {data: "document_annexe_opp_nom"}, 
            {data: "document_annexe_file_action"}, {data: "document_annexe_file_name"}, {data: "document_annexe_file_size"}, {data: "document_annexe_file_url"}, 
            {data: "document_annexe_download"}, {data: "action", width: "120px"}
        ],
        columnDefs: [   
            { 
                targets: [ 0, 3, 5, 6, 7, 8 ], 
                visible: false, 
                searchable: false
            }
        ],
        drawCallback: function ( settings ) {
            }
    } );

    $('#saveDocumentAnnexe').on('click', e => {
        var insertionChamp = {};
        //on boucle sur les champs pour détecter les erreurs et remplir le tableau du POST
        $(".form-document-annexe").each(function(index){
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
        
        insertionChamp['document_annexe_all_opp'] = $("#document_annexe_all_opp").is(':checked');
        if(insertionChamp['document_annexe_all_opp'] == false && insertionChamp['document_annexe_opp'].length == 0){
            $("#document_annexe_opp").addClass('is-invalid');
            return;
        }

        if(addDocumentAnnexeDropzone.dropzone.files.length === 0){
            $("#addDocumentAnnexeDropzone").next('span.formErrors')
            .html('Le document est obligatoire');
            return;
        }

        if($("#document_annexe_file_action").val() == "loaded" && addDocumentAnnexeDropzone.dropzone.files[0].xhr == undefined){
            //On a pas touché au document
            var fileParam = {
                action : 'loaded',
                fileName: $("#document_annexe_file_name").val(),
                fileSize: $("#document_annexe_file_size").val(),
                fileURI : $("#document_annexe_file_url").val(),
            }
        }else{
            if(addDocumentAnnexeDropzone.dropzone.files[0].xhr == undefined){
                //document nouvellement créer et mise à jour ensuite sans modifier le document
                var fileParam = {
                    action : $("#document_annexe_file_action").val(),
                    fileName: $("#document_annexe_file_name").val(),
                    fileSize: $("#document_annexe_file_size").val(),
                    fileURI : $("#document_annexe_file_url").val(),
                }
            }else{
                //le document a été modifiée
                var documentAnnexeDropZone = JSON.parse(addDocumentAnnexeDropzone.dropzone.files[0].xhr.response);
                if(documentAnnexeDropZone.status == 'error'){
                    $("#addDocumentAnnexeDropzone").next('span.formErrors')
                    .html('Une erreur a été rencontrée lors de l\'envoi du document');
                    return;
                };
                //Le document a été ajoutée ou modifiée
                var fileParam = {
                    action : 'new',
                    fileName: documentAnnexeDropZone.fileName,
                    fileSize: documentAnnexeDropZone.fileSize,
                    fileURI: documentAnnexeDropZone.filePath,
                }
            }
        }
        
        if($(".is-invalid").length ==  0){
            var documentAnnexeId = $('#document_annexe_id').val();
            $.post({
                type: "POST",
                url: PARAMETRES.url + '/gestion/modify/document',
                data: {
                    document_annexe_id : documentAnnexeId,
                    document_annexe_titre : $("#document_annexe_titre").val(),
                    document_annexe_desc : $("#document_annexe_desc").val(),
                    document_annexe_opp_id : $("#document_annexe_opp").val(),
                    document_annexe_all_opp : $("#document_annexe_all_opp").is(':checked'),
                    document_annexe_file_action : fileParam.action,
                    document_annexe_file_name: fileParam.fileName,
                    document_annexe_file_size: fileParam.fileSize,
                    document_annexe_file_url: fileParam.fileURI,
                }
            }).done(function( data ) {
                if (documentAnnexeId == 'new'){
                    if($("#document_annexe_all_opp").is(':checked')){
                        var opp = 'Tous';
                        var oppId = 'tous';
                    }else{
                        var oppId = $("#document_annexe_opp").val().join(',');
                        var opp_selected = $("#document_annexe_opp option:selected");
                        for(var j=0; j<opp_selected.length; j++){
                            if(j == 0){
                                var opp = opp_selected[j].text;
                            }else{
                                opp = opp + ', ' + opp_selected[j].text;
                            }
                        }
                    }
                    tableDocuments.row.add({
                        "document_annexe_id": data.documentAnnexeId,
                        "document_annexe_titre": $("#document_annexe_titre").val(),
                        "document_annexe_desc": $("#document_annexe_desc").val(),
                        "document_annexe_opp_id": oppId,
                        "document_annexe_opp_nom": opp,
                        "document_annexe_file_action": fileParam.action,
                        "document_annexe_file_name": data.documentAnnexeFileName,
                        "document_annexe_file_size": fileParam.fileSize,
                        "document_annexe_file_url": fileParam.fileURI,
                        "document_annexe_download": "<a href='" + data.documentAnnexeFileUrl +"' download>" + data.documentAnnexeFileName + "</a>",
                        "action": '<a class="modify"><i class="c-light-blue-500 cur-p ti ti-pencil"></i></a><a class="remove"><i class="c-red-500 cur-p ti ti-trash"></i></a>'}
                    ).draw();
                }else{
                    for (var i=0; i<tableDocuments.data().length; i++) {
                        if (tableDocuments.data()[i].document_annexe_id == $("#document_annexe_id").val()) {
                            if($("#document_annexe_all_opp").is(':checked')){
                                var opp = 'Tous';
                                var oppId = 'tous';
                            }else{
                                var oppId = $("#document_annexe_opp").val().join(',');
                                var opp_selected = $("#document_annexe_opp option:selected");
                                for(var j=0; j<opp_selected.length; j++){
                                    if(j == 0){
                                        var opp = opp_selected[i].text;
                                    }else{
                                        opp = opp + ', ' + opp_selected[i].text;
                                    }
                                }
                            }
                            tableDocuments.data()[i].document_annexe_titre = $("#document_annexe_titre").val();
                            tableDocuments.data()[i].document_annexe_desc = $("#document_annexe_desc").val();
                            tableDocuments.data()[i].document_annexe_opp_id = oppId;
                            tableDocuments.data()[i].document_annexe_opp_nom = opp;
                            tableDocuments.data()[i].document_annexe_file_action = fileParam.action;
                            tableDocuments.data()[i].document_annexe_file_name = data.documentAnnexeFileName;
                            tableDocuments.data()[i].document_annexe_file_size = fileParam.fileSize;
                            tableDocuments.data()[i].document_annexe_file_url = fileParam.fileURI;
                            tableDocuments.data()[i].document_annexe_download = "<a href='" + data.documentAnnexeFileUrl +"' download>" + data.documentAnnexeFileName + "</a>";
                            
                            // mise à jour du tableau
                            var ligne = tableDocuments.row(i).data();
                            tableDocuments.row(i).data(ligne).invalidate();

                        }
                    }
                }
                $('#modalDocumentAnnexe').modal('toggle');
            }).fail(function() {
                alert( "error" );
            });
        }
    });

    $('#dataTableDocuments tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        var data = tableDocuments.row( $(elem).parent().parent() ).data();
        chargeModalDocumentAnnexe(data);
        $("#modalDocumentAnnexe").modal('toggle');
    });


    $('#dataTableDocuments tbody a.remove').jConfirm().on('confirm', function(e){
                //recupère l'élement cliqué
                var elem = e.currentTarget;
                //récupère la ligne
                var ligneIndex = $(elem).parent().parent();
                //column 0 => id de la serie
                var data = tableDocuments.row( ligneIndex ).data();
                //Appel au serveur                
                $.ajax({
                    url: PARAMETRES.url + '/gestion/delete/document/' + data.document_annexe_id,
                })
                .done(function( data, message ) {
                    //quand l'appel est terminé
                    //si OK
                    if(data.status == "ok"){
                        //supprime la ligne dans le tableau
                        tableDocuments.row(ligneIndex).remove().draw( false );
                        
                        flashMessage("success", "Succès", "Le document a été supprimé");
                
                        //envoie un message utilisateur pour informer que tout est ok
                    } 
                })
        
                .fail(function(data, message) {
                    //si pas OK
                    if (data.status){
                        flashMessage("danger", "Erreur", "Le document ne peut pas être supprimé");
                    }
                    
                })
        
            });


    $("#selectOppDocument").change(function(){
        var oppId = $(this).val();
        
		if(oppId != 'all'){
            $('#dataTableDocuments tbody tr').each(function(index){
                var oppIdTable = tableDocuments.row(this).data().document_annexe_opp_id;
                if(oppIdTable == 'tous' || oppIdTable.split(',').includes(oppId)){
                //if(oppIdTable == oppId){
                    $(this).fadeIn();
                }else{
                    $(this).fadeOut();
                }
            });
		} else {
			$("#dataTableDocuments tbody tr").fadeIn();
		}

        

    })
})