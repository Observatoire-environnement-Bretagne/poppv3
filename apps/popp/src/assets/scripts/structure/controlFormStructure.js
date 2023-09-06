import { PARAMETRES } from '../custom/parametre';
import { flashMessage } from '../flashMessage/flashMessage';

$(document).ready(function(){
    $("#editLogoStructure, #addLogoStructure ").click(function(){
        if (updateStructLogoDropzoneForm.dropzone.files[0]!=null){
            updateStructLogoDropzoneForm.dropzone.removeFile(updateStructLogoDropzoneForm.dropzone.files[0]);
        }
        $("#modalStructureFile").modal('toggle');
    });
    
    $("#removeLogoStructure").click(function(){
        removeFileLogo();
    });

    $("#saveLogo").click(function(){
        if(updateStructLogoDropzoneForm.dropzone.files.length !== 0){
            var logoDropzoneUpdate = JSON.parse(updateStructLogoDropzoneForm.dropzone.files[0].xhr.response);
            $("#structureLogoId").val('new');
            $("#structureLogoTitre").val(logoDropzoneUpdate.logoName);
            $("#structureLogoURL").val(logoDropzoneUpdate.logoURI);
            $("#structureLogoPoids").val(logoDropzoneUpdate.logoSize);
            
            //on réinitialise avant ajout du lien
            $("#structureLogoDownload").html("");
            var a = document.createElement('a');
            $(a).attr('href', logoDropzoneUpdate.logoURI)
                .attr('target', "_blank")
                .html(logoDropzoneUpdate.logoName)
                .appendTo($("#structureLogoDownload")) ;
            
            $("#editLogoStructure, #removeLogoStructure").show();
            $("#addLogoStructure").hide();
        }else{
            removeFileLogo()
        }
        $("#modalStructureFile").modal('toggle');
    });

    function removeFileLogo(){
        $("#structureLogoId").val('delete');
        $("#structureLogoTitre").val("");
        $("#structureLogoURL").val("");
        $("#structureLogoPoids").val("");
        $("#structureLogoDownload").html("");
        $("#editLogoStructure, #removeLogoStructure").hide();
        $("#addLogoStructure").show();
    }

    $("#saveStructure").click(function() {
        //Création du tableau de parametre envoi 
        var insertionChamp = {};

        $(".structure").each(function(index){
            if($(this).val() == "" && this.required){
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

        insertionChamp['add_struct_financeur'] = $("#add_struct_financeur").is(':checked');

        if($(".is-invalid").length ==  0){
            $.ajax({
                //url : 'insertDb', 
                url : PARAMETRES.url + '/admin/structure/insertStructure',
                type : 'POST',
                cache: true,
                data : insertionChamp,
                success: function (data) {
                    if(data.status == 'ok'){
                        //Dans le cas ou on à déja enregistré le fichier
                        if($("#structureLogoId").val() == 'new'){
                            $("#structureLogoId").val('updated')
                        }
                        $("#messageConfirmStructure")
                            .removeClass('alert-danger')
                            .addClass('alert alert-success ta-c w-100')
                            .html("La structure à été enregistrée.")
                            .fadeIn(1000)
                            .delay(2000)
                            .fadeOut(1000);

                        window.location.href = PARAMETRES.url + '/show/structure/' + data.structureId;
                    }else if(data.status == 'erreur'){
                        $("#messageConfirmStructure")
                            .addClass('alert alert-danger ta-c w-100')
                            .html(data.message)
                            .fadeIn(500);
                    }

                },
                error : function (){
                    $("#messageConfirmStructure")
                        .addClass('alert alert-danger ta-c w-100')
                        .html("Une erreur a été rencontrée lors de l'enregistrement.")
                        .fadeIn(500);
                }
            });
        }

        /*if(add_struct_logo_dropzone_form.dropzone.files.length !== 0){
            insertionChamp["structureLogoName"] = JSON.parse(add_struct_logo_dropzone_form.dropzone.files[0].xhr.response).logoName;
            insertionChamp["structureLogoURI"] = JSON.parse(add_struct_logo_dropzone_form.dropzone.files[0].xhr.response).logoURI;
            insertionChamp["structureLogoFormat"] = JSON.parse(add_struct_logo_dropzone_form.dropzone.files[0].xhr.response).logoFormat;
            insertionChamp["structureLogoStatut"] = JSON.parse(add_struct_logo_dropzone_form.dropzone.files[0].xhr.response).logoStatut;
            insertionChamp["structureLogoSize"] = JSON.parse(add_struct_logo_dropzone_form.dropzone.files[0].xhr.response).logoSize;
            insertionChamp["structureLogoDate"] = add_struct_logo_dropzone_form.dropzone.files[0].lastModified;
        }*/
        
        /*
        var longueur = $(".structure").length;
        for (let i = 0 ; i < longueur; i++){
            //Si le champ est null 
            if ($(".structure")[i].value === ""){
                var elt = $(".structure")[i];

                //Si le champ est optionnel 
                if ($(".structure")[i].required === false){
                    var id = $(".structure")[i].id;
                    var value = $(".structure")[i].value;
                    insertionChamp[id] = value;
                }
                //Si le champ est obligatoire 
                else{
                    document.getElementsByClassName("structure")[i].classList.add("is-invalid");
                    document.getElementsByClassName("structure")[i].focus();
                    return;
                }
            }
            //Si le champ a une valeur 
            else{
                document.getElementsByClassName("structure")[i].classList.remove("is-invalid");
                var id = $(".structure")[i].id;
                var value = $(".structure")[i].value;
                insertionChamp[id] = value;
            }  
        }*/
        /*if(add_struct_logo_dropzone_form.dropzone.files.length !== 0){
            insertionChamp["structureLogoName"] = JSON.parse(add_struct_logo_dropzone_form.dropzone.files[0].xhr.response).logoName;
            insertionChamp["structureLogoURI"] = JSON.parse(add_struct_logo_dropzone_form.dropzone.files[0].xhr.response).logoURI;
            insertionChamp["structureLogoFormat"] = JSON.parse(add_struct_logo_dropzone_form.dropzone.files[0].xhr.response).logoFormat;
            insertionChamp["structureLogoStatut"] = JSON.parse(add_struct_logo_dropzone_form.dropzone.files[0].xhr.response).logoStatut;
            insertionChamp["structureLogoSize"] = JSON.parse(add_struct_logo_dropzone_form.dropzone.files[0].xhr.response).logoSize;
            insertionChamp["structureLogoDate"] = add_struct_logo_dropzone_form.dropzone.files[0].lastModified;
        }*/

    //    if (insertionChamp['add_struct_id'] !== 'new'){
            /*Dropzone.options.myAwesomeDropzone = {
                init: function() {
                    let myDropzone = this;

                    let mockFile = { name: "Filename", size: 12345 };
                    myDropzone.displayExistingFile(mockFile, fileUri);
                }
            }
        
        //var longueur = $(".structure").length;
        
        $.ajax({
            //url : 'insertDb', 
            url : PARAMETRES.url + '/admin/structure/insertStructure',
            type : 'POST',
            cache: true,
            data : insertionChamp
        });*/
    });

    var tableLogo = $('#dataTableStructuresUpdateFile').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     false,
        searching: false,
        rowId: 0,
        language : PARAMETRES.dataTableFrancais,
        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            }
        ]
    });

    function redrawLineInTableLogo(line){
        //tableLogo.clear();
        /*if(line[0] == 'delete'){
            line.push("<a class='modify' data-toggle='modal' data-target='#modalStructureFile' rel='modalStructureFile:open'><i class='c-green-500 cur-p ti ti-plus' style='font-weight: bold;'></i></a>");
        }else{
            line.push('<a class="modify" data-toggle="modal" data-target="#modalStructureFile" rel="modalStructureFile:open"><i class="c-light-blue-500 cur-p ti ti-pencil"></i></a> <a class="remove"><i class="c-red-500 cur-p ti ti-trash"></i></a>');
        }*/
        tableLogo.row.row("0").data(line).draw();
    }

    //suppression du logo => suppression dans le tablea
    $('#dataTableStructuresUpdateFile a.remove').click(function(){
        var line = Array('delete', 'Logo', '', '', '')
        redrawLineInTableLogo(line);
        //Récupération 
        /*var temp = tableLogo.row(this.parentElement.parentElement).data();
        //id
        temp[0] = 'delete';
        //Titre
        temp[2] = "";
        //URL
        temp[3] = "";
        //Poids
        temp[4] = "";
        //Actions
        temp[5] = "<a class='modify' data-toggle='modal' data-target='#modalStructureFile' rel='modalStructureFile:open'><i class='c-green-500 cur-p ti ti-plus' style='font-weight: bold;'></i></a>";
        tableLogo.row(this.parentElement.parentElement).data(temp).draw();
*/
        //tableLogo.row(this.parentElement.parentElement).remove().draw( false );
        /*if(confirm("Vous êtes sur le point de supprimer une structure. Souhaitez vous continuer ?")){
            var logoId = $("#structureLogoId").html();
            $.ajax({
                url: PARAMETRES.url + '/admin/remove/structure/logo/' + logoId,
            })
        }*/
    });

$('#deletetructure').jConfirm().on('confirm', function(e){
    //recupère l'élement cliqué
    var structureId = $("#add_struct_id").val();

    //Appel au serveur                
    $.ajax({
        url: PARAMETRES.url + '/admin/remove/structure/' + structureId,
    })
    .done(function( data, message ) {
        //quand l'appel est terminé
        //si OK
        if(data.status == "ok"){
            window.location.href = PARAMETRES.url + '/admin/get/structures';

            flashMessage("success", "Succès", "La structure a été supprimée");
        } 
    })

    .fail(function(data, message) {
        //si pas OK
        if (data.status){
             flashMessage("danger", "Erreur", "La structure ne peut pas être supprimée");
        }
           
    })

})
});