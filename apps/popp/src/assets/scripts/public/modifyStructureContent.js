import { PARAMETRES } from '../custom/parametre';


$(function() {

    $("#modifStructureContent").click(function(){
        window.location.href = PARAMETRES.url + '/admin/update/structures/' + $("#structureId").val();
        //$("#modalStructureContent").modal('show');
    });    
    
    $("#saveModifStructure").click(function(){
        if(confirm("Vous êtes sur le point de modifier une structure. Souhaitez vous valider cette modification ?")){
            var structureId = $("#structureId");
            var structureIdValue = structureId.val();

            var structureNom = $("#structureNom");
            var structureNomValue = structureNom.val();      

            var structureDescCourte = $("#structureDescCourte");
            var structureDescCourteValue = structureDescCourte.val();     

            var structureAdresse = $("#structureAdresse");
            var structureAdresseValue = structureAdresse.val();    

            var structureContactRef = $("#structureContactRef");
            var structureContactRefValue = structureContactRef.val();   

            var structureEmail = $("#structureEmail");
            var structureEmailValue = structureEmail.val();   

            var structureTelephone = $("#structureTelephone");
            var structureTelephoneValue = structureTelephone.val();    

            var structureSiteWeb = $("#structureSiteWeb");
            var structureSiteWebValue = structureSiteWeb.val();

            $.ajax({
                url: PARAMETRES.url + '/update/structure/' + structureIdValue,
                type:"POST",
                data: {
                    "structureId":structureIdValue,
                    "structureNom":structureNomValue,
                    "structureDescCourte":structureDescCourteValue,
                    "structureAdresse":structureAdresseValue,
                    "structureContactRef":structureContactRefValue,
                    "structureEmail":structureEmailValue,
                    "structureTelephone":structureTelephoneValue,
                    "structureSiteWeb":structureSiteWebValue,
                },
                success: function (data) {
                    if(data.status == 'ok'){
                        //Dans le cas ou on a déja enregistré le fichier
                        $("#modalStructureContent").modal('hide');
                        $("#messageConfirmModifStructure")
                            .removeClass('alert-danger')
                            .addClass('alert alert-success ta-c w-100')
                            .html("La structure a été modifiée.")
                            .fadeIn(1000)
                            .delay(2000)
                            .fadeOut(1000);
                    }
                },
                error : function (){
                    $("#messageConfirmModifStructure")
                        .addClass('alert alert-danger ta-c w-100')
                        .html("Une erreur a été rencontrée lors de l'enregistrement.")
                        .fadeIn(500);
                }
            });
        };       
    });
});

