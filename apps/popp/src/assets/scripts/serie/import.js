import { PARAMETRES } from '../custom/parametre';


$('#importSerie').click(function(){
    var insertionChamp = {};
    //Récupération des données des fichiers uploadés 
        //Récupération des données de l'archive ZIP de photos de la nouvelle série
        var PhotoDropzoneContent = importPhotoDropzoneForm.dropzone.files;
        if (PhotoDropzoneContent.length !== 0){
            var ZipPhotoDropZone = JSON.parse(PhotoDropzoneContent[0].xhr.response);
            var ZipId = "new";
            var ZipName = ZipPhotoDropZone.archiveName;
            var ZipURL = ZipPhotoDropZone.archiveURI;
            var ZipPath = ZipPhotoDropZone.archivepath;
            var ZipSize = ZipPhotoDropZone.archiveSize;
            //Insertion des données dans le tableau d'envoi AJAX
            insertionChamp["zip"] = {
                "zipId" : ZipId,
                "zipName" : ZipName,
                "zipURL" : ZipURL,
                "zipPath" : ZipPath,
                "zipSize" : ZipSize,
            };
            $("#messageImportZipPhotos").hide();
        }else{
            $("#messageImportZipPhotos")
                .addClass('alert alert-danger ta-c w-100 mT-20')
                .html("L'insertion d'un ZIP est nécessaire")
                .fadeIn(500);
                return;
        }
                
        //Récupération des données de la table CSV de la nouvelle série
        var CSVDropZoneContent = importTableDropzoneForm.dropzone.files;
        if (CSVDropZoneContent.length !== 0){
            var CSVDropZone = JSON.parse(CSVDropZoneContent[0].xhr.response);
            var CSVId = "new";
            var CSVName = CSVDropZone.csvName;
            var CSVURL = CSVDropZone.csvURI;
            var CSVPath = CSVDropZone.csvPath;
            var CSVSize = CSVDropZone.csvSize;
            //Insertion des données dans le tableau d'envoi AJAX
            insertionChamp["csv"] = {
                "csvId" : CSVId,
                "csvName" : CSVName,
                "csvURL" : CSVURL,
                "csvPath" : CSVPath,
                "csvSize" : CSVSize
            };
            $("#messageImportTableSerie").hide();
        }else{
            $("#messageImportTableSerie")
                .addClass('alert alert-danger ta-c w-100 mT-20')
                .html("L'insertion d'un CSV est nécessaire.")
                .fadeIn(500);
                return;
        }
        
    //Appel AJAX en envoyant le taleau insertionChamp
    if(insertionChamp.length !== 0){
        $.ajax({
            url : PARAMETRES.url + '/gestion/import/serie',
            type : 'POST',
            cache: true,
            data : insertionChamp,
            success: function (data) {
                if(data.status == 'ok'){
                    //Dans le cas ou on à déja enregistré le fichier
                    $("#dataTableSeriesUpdateFile tr").each(function(index){
                        var ligne = $(this);
                        if($(ligne).find(".fileId").val() == 'new'){
                            $(ligne).find(".fileId").val('updated');
                        }
                    });
                    $("#messageConfirmImport")
                        .removeClass('alert-danger')
                        .addClass('alert alert-success ta-c w-100')
                        .html("L'élement a été enregistrée.")
                        .fadeIn(1000)
                        .delay(2000)
                        .fadeOut(1000);
                    importTableDropzoneForm.dropzone.removeAllFiles(true);
                    importPhotoDropzoneForm.dropzone.removeAllFiles(true);
                }else if(data.status == 'erreur'){
                    $("#messageConfirmImport")
                        .addClass('alert alert-danger ta-c w-100')
                        .html(data.message)
                        .fadeIn(500);
                }

            },
            error : function (){
                $("#messageConfirmImport")
                    .addClass('alert alert-danger ta-c w-100')
                    .html("Une erreur a été rencontrée lors de l'import.")
                    .fadeIn(500);
            }
        });
    };
});