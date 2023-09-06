import Dropzone from 'dropzone';
import 'dropzone/dist/dropzone.css';
import loadImage from "blueimp-load-image";

function parseDate(s) {
    var b = s.split(/\D/);
    return new Date(b[0],b[1]-1,b[2],b[3],b[4],b[5]);
  }

Dropzone.prototype.defaultOptions.dictDefaultMessage = "Les fichiers doivent peser moins de 256 Mo.";
Dropzone.prototype.defaultOptions.dictRemoveFile = "Supprimer";
Dropzone.prototype.defaultOptions.addRemoveLinks = true;
Dropzone.prototype.defaultOptions.acceptedFiles = ".jpeg,.jpg,.png,.gif";
Dropzone.prototype.defaultOptions.maxFiles = 1;
Dropzone.prototype.defaultOptions.uploadMultiple = false;
Dropzone.prototype.defaultOptions.autoprocessQueue = false;
Dropzone.prototype.defaultOptions.timeout = 300000;
Dropzone.prototype.defaultOptions.init = function () {
    this.hiddenFileInput.removeAttribute('multiple');
    this.on("maxfilesexceeded", function (file) {
        this.removeAllFiles();
        this.addFile(file);
    });
};


Dropzone.options.logoStructure= {
    paramName: "logoStructure",
    addRemoveLinks: true,
    paramName: "file", // The name that will be used to transfer the file
    dictRemoveFile: "Supprimer",
    dictDefaultMessage: "Déposer le logo ici (poids inférieur à 256Mo), les formats acceptés sont jpeg, jpg, png et gif.",
    maxFiles: 1,
    addRemoveLinks: true,
    maxFilesize: 256, //MB
    acceptedFiles: ".jpeg,.jpg,.png,.gif",
    autoprocessQueue: false,
    /*previewTemplate: "\
        <div class=\"dz-preview dz-file-preview\">\n  \n\
            <div class=\"dz-image\"><img data-dz-thumbnail /></div>\n  \n\
            <div class=\"dz-details\">\n    <!--div class=\"dz-size\"><span data-dz-size></span></div-->\n    \n\
            <div class=\"dz-filename\"><span data-dz-name></span></div>\n  </div>\n  \n\
            <div class=\"dz-progress\"><span class=\"dz-upload\" data-dz-uploadprogress></span></div>\n  \n\
            <div class=\"dz-error-message\"><span data-dz-errormessage></span></div>\n  \n\
            <div class=\"dz-success-mark\"> \n\
            <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:sketch=\"http://www.bohemiancoding.com/sketch/ns\">\n      <title>Check</title>\n      <defs></defs>\n      <g id=\"Page-1\" stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\" sketch:type=\"MSPage\">\n        <path d=\"M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\" id=\"Oval-2\" stroke-opacity=\"0.198794158\" stroke=\"#747474\" fill-opacity=\"0.816519475\" fill=\"#FFFFFF\" sketch:type=\"MSShapeGroup\"></path>\n      </g>\n    </svg>\n  </div> \n\
            <div class=\"dz-error-mark\">\n    <svg width=\"54px\" height=\"54px\" viewBox=\"0 0 54 54\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:sketch=\"http://www.bohemiancoding.com/sketch/ns\">\n      <title>Error</title>\n      <defs></defs>\n      <g id=\"Page-1\" stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\" sketch:type=\"MSPage\">\n        <g id=\"Check-+-Oval-2\" sketch:type=\"MSLayerGroup\" stroke=\"#747474\" stroke-opacity=\"0.198794158\" fill=\"#FFFFFF\" fill-opacity=\"0.816519475\">\n          <path d=\"M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z\" id=\"Oval-2\" sketch:type=\"MSShapeGroup\"></path>\n        </g>\n      </g>\n    </svg>\n  </div> \n\
            <!--input type=\"text\" placeholder=\"Title\" size=\"12\"-->\n\
        </div>",*/
    init: function() {
        this.on("addedfile", function() {
            if (this.files[1]!=null){
                this.removeFile(this.files[0]);
            }
        });
        this.on("success", function(file, response) {
            //si on a une erreur sur le chargement
            if(response.status == "error"){
                alert (response.message);
                return;
            }/*else{
                response.logoName;
                response.logoSize
                response.logoFormat
                response.logoURI
            }*/
        });
    }
};


Dropzone.options.updateStructLogoDropzoneForm = {
    paramName: "updateStructLogoDropzoneForm",
    addRemoveLinks: true,
    paramName: "file", // The name that will be used to transfer the file
    dictRemoveFile: "Supprimer",
    dictDefaultMessage: "Déposer le logo ici (poids inférieur à 256Mo), les formats acceptés sont jpeg, jpg, png et gif.",
    maxFiles: 1,
    addRemoveLinks: true,
    maxFilesize: 256, //MB
    acceptedFiles: ".jpeg,.jpg,.png,.gif",
    autoprocessQueue: false,
    init: function() {
        this.on("addedfile", function() {
            if (this.files[1]!=null){
                this.removeFile(this.files[0]);
            }
        });
        this.on("success", function(file, response) {
            //si on a une erreur sur le chargement
            if(response.status == "error"){
                alert (response.message);
                return;
            }
        });
    }
};

Dropzone.options.updateSerieDropzoneForm = {
    paramName: "updateSerieDropzoneForm",
    addRemoveLinks: true,
    paramName: "file", // The name that will be used to transfer the file
    dictRemoveFile: "Supprimer",
    dictDefaultMessage: "Déposer le fichier ici (poids inférieur à 256Mo), les formats acceptés sont jpeg, jpg, png et gif.",
    maxFiles: 1,
    addRemoveLinks: true,
    maxFilesize: 256, //MB
    acceptedFiles: ".jpeg,.jpg,.png,.gif",
    autoprocessQueue: false,
    init: function() {
        this.on("addedfile", function() {
            if (this.files[1]!=null){
                this.removeFile(this.files[0]);
            }
        });
        this.on("success", function(file, response) {
            //si on a une erreur sur le chargement
            if(response.status == "error"){
                alert (response.message);
                return;
            }
        });
    }
};

Dropzone.options.addPhotoDropzone = {
    paramName: "addPhotoDropzone",
    addRemoveLinks: true,
    paramName: "file", // The name that will be used to transfer the file
    dictRemoveFile: "Supprimer",
    dictDefaultMessage: "Déposer le fichier ici (poids inférieur à 256Mo), les formats acceptés sont jpeg, jpg, png et gif.",
    maxFiles: 1,
    addRemoveLinks: true,
    maxFilesize: 256, //MB
    acceptedFiles: ".jpeg,.jpg,.png,.gif",
    autoprocessQueue: false,
    init: function() {
        this.on("addedfile", function() {
            if (this.files[1]!=null){
                this.removeFile(this.files[0]);
            }
            loadImage.parseMetaData(this.files[0], function (data) {
                if (data.exif){
                    if(data.exif.getAll().Model){
                        $("#photo_type_appareil").val(data.exif.getAll().Model);
                    }else{
                        $("#photo_type_appareil").val("");
                    }
                    if(data.exif.getAll().Exif){
                        if(data.exif.getAll().Exif.DateTimeOriginal){
                            var dateTime = parseDate(data.exif.getAll().Exif.DateTimeOriginal);
                            var hoursField = ("0" + dateTime.getHours()).slice(-2) + "" + ("0" + dateTime.getMinutes()).slice(-2);
                            var dayField = ("0" + dateTime.getDate()).slice(-2) + "/" + ("0" + (dateTime.getMonth() + 1) ).slice(-2) + "/" + dateTime.getFullYear() ;
                            $("#photo_heure").val(hoursField);
                            $("#photo_date_prise").val(dayField);
                        }else{
                            $("#photo_heure").val("");
                            $("#photo_date_prise").val("");
                        }
                        if(data.exif.getAll().Exif.FocalLength){
                            $("#photo_focale").val(data.exif.getAll().Exif.FocalLength);
                        }else{
                            $("#photo_focale").val("");
                        }
                        if(data.exif.getAll().Exif.FNumber){
                            $("#photo_ouverture").val(data.exif.getAll().Exif.FNumber);
                        }else{
                            $("#photo_ouverture").val("");
                        }
                        if(data.exif.getAll().Exif.SceneCaptureType){
                            $("#photo_type_film").val(data.exif.getAll().Exif.SceneCaptureType);
                        }else{
                            $("#photo_type_film").val("");
                        }
                        if(data.exif.getAll().Exif.PhotographicSensitivity){
                            $("#photo_iso").val(data.exif.getAll().Exif.PhotographicSensitivity + " ISO" );
                        }else{
                            $("#photo_iso").val("");
                        }
                    }
                    //if(data.exif.getAll().Orientation){
                    //    $("#photo_orientation").val(data.exif.getAll().Orientation);
                    //}
                    
                    //$("#photo_poids_ori").val(data.exif.getAll().Model);
                    //$("#photo_inclinaison").val(data.exif.getAll().Model);
                    //$("#photo_hauteur").val(data.exif.getAll().Model);
                    //$("#photo_altitude").val(data.exif.getAll().Model);
                }
                //var orientation = data.exif.get('Orientation');
            });
        });
        this.on("success", function(file, response) {
            //si on a une erreur sur le chargement
            if(response.status == "error"){
                alert (response.message);
                return;
            }
        });
    }
};

Dropzone.options.importPhotoDropzoneForm = {
    paramName: "importPhotoDropzoneForm",
    addRemoveLinks: true,
    paramName: "file", // The name that will be used to transfer the file
    dictRemoveFile: "Supprimer",
    dictDefaultMessage: "Déposez votre archive .ZIP contenant les photos de la série ici.",
    maxFiles: 2,
    addRemoveLinks: true,
    maxFilesize: 2000, //MB
    acceptedFiles: ".zip",
    autoprocessQueue: true,
    init: function() {
        this.on("addedfile", function() {
            if (this.files[1]!=null){
                this.removeFile(this.files[0]);
            }
        });
        this.on("success", function(file, response) {
            //si on a une erreur sur le chargement
            if(response.status == "error"){
                alert (response.message);
                return;
            }
        });
    }
};

Dropzone.options.importTableDropzoneForm = {
    paramName: "importTableDropzoneForm",
    addRemoveLinks: true,
    paramName: "file", // The name that will be used to transfer the file
    dictRemoveFile: "Supprimer",
    dictDefaultMessage: "Déposez le tableau d'import au format .csv ici",
    maxFiles: 1,
    addRemoveLinks: true,
    maxFilesize: 2000, //MB
    acceptedFiles: ".csv",
    autoprocessQueue: false,
    init: function() {
        this.on("addedfile", function() {
            if (this.files[1]!=null){
                this.removeFile(this.files[0]);
            }
        });
        this.on("success", function(file, response) {
            //si on a une erreur sur le chargement
            if(response.status == "error"){
                alert (response.message);
                return;
            }
        });
    }
};


Dropzone.options.addSonDropzone = {
    paramName: "addSonDropzone",
    addRemoveLinks: true,
    paramName: "file", // The name that will be used to transfer the file
    dictRemoveFile: "Supprimer",
    dictDefaultMessage: "Déposer le fichier ici (poids inférieur à 256Mo), les formats acceptés sont mp4, wav et mp3.",
    maxFiles: 1,
    addRemoveLinks: true,
    maxFilesize: 256, //MB
    acceptedFiles: ".wav,.mp3,.mp4",
    autoprocessQueue: false,
    init: function() {
        this.on("addedfile", function() {
            if (this.files[1]!=null){
                this.removeFile(this.files[0]);
            }
        });
        this.on("success", function(file, response) {
            //si on a une erreur sur le chargement
            if(response.status == "error"){
                alert (response.message);
                return;
            }
        });
    }
};
Dropzone.options.addDocumentDropzone = {
    paramName: "addDocumentDropzone",
    addRemoveLinks: true,
    paramName: "file", // The name that will be used to transfer the file
    dictRemoveFile: "Supprimer",
    dictDefaultMessage: "Déposer le fichier ici (poids inférieur à 256Mo), les formats acceptés sont pdf, doc, docx et odt.",
    maxFiles: 1,
    addRemoveLinks: true,
    maxFilesize: 256, //MB
    acceptedFiles: ".pdf,.doc,.docx,.odt",
    autoprocessQueue: false,
    init: function() {
        this.on("addedfile", function() {
            if (this.files[1]!=null){
                this.removeFile(this.files[0]);
            }
        });
        this.on("success", function(file, response) {
            //si on a une erreur sur le chargement
            if(response.status == "error"){
                alert (response.message);
                return;
            }
        });
    }
};

Dropzone.options.addFaqDoc = {
    paramName: "addFaqDoc",
    addRemoveLinks: true,
    paramName: "file", // The name that will be used to transfer the file
    dictRemoveFile: "Supprimer",
    dictDefaultMessage: "Déposer le fichier ici (poids inférieur à 256Mo), les formats acceptés sont .pdf, .txt, .csv, .doc, .docx, .pdf, .png, .gif, .jpg, .jpeg, .xls, .xlsx.",
    maxFiles: 1,
    addRemoveLinks: true,
    maxFilesize: 256, //MB
    acceptedFiles: ".pdf, .txt, .csv, .doc, .docx, .pdf, .png, .gif, .jpg, .jpeg, .xls, .xlsx",
    autoprocessQueue: false,
    init: function() {
        this.on("addedfile", function() {
            if (this.files[1]!=null){
                this.removeFile(this.files[0]);
            }
        });
        this.on("success", function(file, response) {
            //si on a une erreur sur le chargement
            if(response.status == "error"){
                alert (response.message);
                return;
            }
        });
    }
};

Dropzone.options.addRessourceDoc = {
    paramName: "addRessourceDoc",
    addRemoveLinks: true,
    paramName: "file", // The name that will be used to transfer the file
    dictRemoveFile: "Supprimer",
    dictDefaultMessage: "Déposer le fichier ici (poids inférieur à 256Mo), les formats acceptés sont .pdf, .txt, .csv, .doc, .docx, .pdf, .png, .gif, .jpg, .jpeg, .xls, .xlsx.",
    maxFiles: 1,
    addRemoveLinks: true,
    maxFilesize: 256, //MB
    acceptedFiles: ".pdf, .txt, .csv, .doc, .docx, .pdf, .png, .gif, .jpg, .jpeg, .xls, .xlsx",
    autoprocessQueue: false,
    init: function() {
        this.on("addedfile", function() {
            if (this.files[1]!=null){
                this.removeFile(this.files[0]);
            }
        });
        this.on("success", function(file, response) {
            //si on a une erreur sur le chargement
            if(response.status == "error"){
                alert (response.message);
                return;
            }
        });
    }
};

Dropzone.options.addRessourceLogo = {
    paramName: "addRessourceLogo",
    addRemoveLinks: true,
    paramName: "file", // The name that will be used to transfer the file
    dictRemoveFile: "Supprimer",
    dictDefaultMessage: "Déposer le fichier ici (poids inférieur à 2Mo), les formats acceptés sont .png, .jpg, .jpeg, .gif, .tif.",
    maxFiles: 1,
    addRemoveLinks: true,
    maxFilesize: 2, //MB
    acceptedFiles: ".png, .jpeg, .jpg, .gif, .tif, .pdf, .png, .gif, .jpg, .jpeg, .xls, .xlsx",
    autoprocessQueue: false,
    init: function() {
        this.on("addedfile", function() {
            if (this.files[1]!=null){
                this.removeFile(this.files[0]);
            }
        });
        this.on("success", function(file, response) {
            //si on a une erreur sur le chargement
            if(response.status == "error"){
                alert (response.message);
                return;
            }
        });
    }
};

Dropzone.options.addDocumentAnnexeDropzone = {
    paramName: "addDocumentAnnexeDropzone",
    addRemoveLinks: true,
    paramName: "file", // The name that will be used to transfer the file
    dictRemoveFile: "Supprimer",
    dictDefaultMessage: "Déposer le fichier ici (poids inférieur à 256Mo), les formats acceptés sont .pdf, .txt, .csv, .doc, .docx, .pdf, .png, .gif, .jpg, .jpeg, .xls, .xlsx.",
    maxFiles: 1,
    addRemoveLinks: true,
    maxFilesize: 256, //MB
    acceptedFiles: ".pdf, .txt, .csv, .doc, .docx, .pdf, .png, .gif, .jpg, .jpeg, .xls, .xlsx",
    autoprocessQueue: false,
    init: function() {
        this.on("addedfile", function() {
            if (this.files[1]!=null){
                this.removeFile(this.files[0]);
            }
        });
        this.on("success", function(file, response) {
            //si on a une erreur sur le chargement
            if(response.status == "error"){
                alert (response.message);
                return;
            }
        });
    }
};


Dropzone.options.importPhotoForm = {
    paramName: "importPhotoForm",
    addRemoveLinks: true,
    paramName: "file", // The name that will be used to transfer the file
    dictRemoveFile: "Supprimer",
    dictDefaultMessage: "Déposer le fichier ici (poids inférieur à 256Mo), les formats acceptés sont .pdf, .txt, .csv, .doc, .docx, .pdf, .png, .gif, .jpg, .jpeg, .xls, .xlsx.",
    maxFiles: 1,
    addRemoveLinks: true,
    maxFilesize: 256, //MB
    acceptedFiles: ".png, .gif, .jpg, .jpeg",
    autoprocessQueue: false,
    init: function() {
        this.on("addedfile", function() {
            if (this.files[1]!=null){
                this.removeFile(this.files[0]);
            }
        });
        this.on("success", function(file, response) {
            //si on a une erreur sur le chargement
            if(response.status == "error"){
                alert (response.message);
                return;
            }
        });
    }
};
