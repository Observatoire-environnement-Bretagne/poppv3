import { PARAMETRES } from '../custom/parametre';

export default (function () {

    var tableLogo = $('#dataTableStructuresUpdateFile').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     false,
        searching: false,
        rowId: 0,
        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            }
        ]
    });

    $('#dataTableStructuresUpdateFile a.remove').click(function(){
        tableStructure.row(this.parentElement.parentElement).remove().draw( false );
    });

    $("#saveLogo").click(function(){
        //Création du tableau de parametre envoi 
        var insertionChamp = {};
        var structureId = $("#add_struct_id").val();
        var structurecomponent = $("#add_struct_id");
        var structureLogoId = $("#structureLogoId")[0].textContent;

        if(updateStructLogoDropzoneForm.dropzone.files.length !== 0){
            var logoDropzoneUpdate = JSON.parse(updateStructLogoDropzoneForm.dropzone.files[0].xhr.response);
            insertionChamp["structureLogoName"] = logoDropzoneUpdate.logoName;
            insertionChamp["structureLogoURI"] = logoDropzoneUpdate.logoURI;
            insertionChamp["structureLogoFormat"] = logoDropzoneUpdate.logoFormat;
            insertionChamp["structureLogoStatut"] = logoDropzoneUpdate.logoStatut;
            insertionChamp["structureLogoSize"] = logoDropzoneUpdate.logoSize;
            insertionChamp["structureLogoDate"] = updateStructLogoDropzoneForm.dropzone.files[0].lastModified;
        }else{

        }

        $.ajax({
            // url : 'insertDb', 
            url : PARAMETRES.url + "/admin/remove/structure/logo/" + structureLogoId,
            type : 'POST',
            cache: true,
            data : insertionChamp,
            success: function (data) {
                $("#messageConfirmStructure")
                    .addClass('alert alert-success ta-c w-100')
                    .html("La structure à été enregistrée.")
                    .delay(1000)
                    .fadeOut(1000);

            }
        });
    });
});