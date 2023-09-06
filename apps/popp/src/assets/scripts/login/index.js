import { PARAMETRES } from '../custom/parametre';

$(function() {
    $("#btnCreateCompte").click(function() {
        
        if (controleSaisieCreationCompte()) {
            $("#modalInfoUser").modal('toggle');
        }
        return;
    });

    $("#saveInfoUser").click(function(){
        if(controleSaisieInfoUser()){
            var typeCompteRadiosValue = $("input[name='typeCompteRadios']:checked").val();
            var url = PARAMETRES.url + "/create/user";
            $.ajax({
                url: url,
                type: "POST",
                async: false,
                data: {
                       nom: $("#c_nom").val(),
                       prenom: $("#c_prenom").val(),
                       sexe:$("#c_sexe").val(),
                       email: $("#c_email").val(),
                       mdp: $("#plainPassword_first").val(),
                       adresse: $("#user_adresse").val(),
                       code_postal: $("#user_code_postal").val(),
                       ville: $("#user_ville").val(),
                       telephone: $("#user_telephone").val(),
                       opp_id: $("#user_opp").val(),
                       typeCompte : typeCompteRadiosValue
                      },
                success: function (data) {
                    $("#modalInfoUser").modal('toggle');
                    $("#msgCreateCompte").removeClass('alert-success');
                    $("#msgCreateCompte").removeClass('alert-warning');
                    $("#msgCreateCompte").removeClass('alert-danger');
                    if (data.erreur) {
                        $("#msgCreateCompte").addClass('alert');
                        $("#msgCreateCompte").addClass('alert-danger');
                        $("#msgCreateCompte").html(data.erreur);
                    }else if(data.warning){
                        $("#msgCreateCompte").addClass('alert');
                        $("#msgCreateCompte").addClass('alert-warning');
                        $("#msgCreateCompte").html(data.warning);
                    }else if(data.status){
                        $("#msgCreateCompte").addClass('alert');
                        $("#msgCreateCompte").addClass('alert-success');
                        if(typeCompteRadiosValue == "isUser"){
                            $("#msgCreateCompte").html("Votre compte a été créé, vous pouvez maintenant vous connecter.<br \><a href='#signin'>Se connecter</a>");
                        }else{
                            $("#msgCreateCompte").html("Votre compte a été créé, un mail est envoyé au responsable pour valider votre compte.<br \>En attendant <a href='" + PARAMETRES.url + "/public/popp'>accéder librement au site</a>");
                        }
                        
                    }
                },
                error: function () {

                }
            });
        }
    });
    // Bouton Création du compte <--
    
    // Contrôle Création du compte -->
    function controleSaisieCreationCompte() {
        var retour = true;
        var champErreur = "";

        // Contrôle Email oblibatoire
        if($("#c_email").val() == ""){
            $("#c_email").addClass("formErrors");
            $("#c_email").nextAll("span.formErrors:first").html("L'adresse email est obligatoire");
            if (champErreur == "") champErreur = "#c_email";
        }else if (! isValidateEmail($("#c_email").val())) {
            $("#c_email").addClass("formErrors");
            $("#c_email").nextAll("span.formErrors:first").html("Email incorrect"); 
            if (champErreur == "") champErreur = "#c_email";
        }else if (checkMailBdd('', $("#c_email").val())) {
            $("#c_email").addClass("formErrors");
            $("#c_email").nextAll("span.formErrors:first").html("Un compte est déjà associé à cette adresse mail dans notre système. <a href=\"#reinitPwd\">Réinitialiser votre mot de passe.</a>"); 
            if (champErreur == "") champErreur = "#c_email";
        }else {
            $("#c_email").removeClass("formErrors");
            $("#c_email").nextAll("span.formErrors:first").html("");
        }
        // Contrôle Nom oblibatoire
        if($("#c_nom").val() == ""){
            $("#c_nom").addClass("formErrors");
            $("#c_nom").nextAll("span.formErrors:first").html("Le nom est obligatoire");
            if (champErreur == "") champErreur = "#c_nom";
        } else {
            $("#c_nom").removeClass("formErrors");
            $("#c_nom").nextAll("span.formErrors:first").html("");
        }
        // Contrôle Prénom oblibatoire
        if($("#c_prenom").val() == ""){
            $("#c_prenom").addClass("formErrors");
            $("#c_prenom").nextAll("span.formErrors:first").html("Le prénom est obligatoire");
            if (champErreur == "") champErreur = "#c_prenom";
        }else {
            $("#c_prenom").removeClass("formErrors");
            $("#c_prenom").nextAll("span.formErrors:first").html("");
        }
        // Contrôle Mot de passe oblibatoire
        if($('#plainPassword_first').val() != $('#plainPassword_second').val()){
            $("#plainPassword_first").addClass("formErrors");
            $("#plainPassword_second").addClass("formErrors");
            $("#plainPassword_first").nextAll("span.formErrors:first").html("Les mots de passe sont différents");
            if (champErreur == "") champErreur = "#plainPassword_first";
        }else if($('#plainPassword_first').val().length < 6){
            $('#plainPassword_first').addClass('formErrors');
            $('#plainPassword_second').addClass('formErrors');
            $("#plainPassword_first").nextAll("span.formErrors:first").html("Taille minimum du mot de passe : 6 caractères");
            if (champErreur == "") champErreur = "#plainPassword_first";
        }else {
            $("#plainPassword_first").removeClass("formErrors");
            $("#plainPassword_second").removeClass("formErrors");
            $("#plainPassword_first").nextAll("span.formErrors:first").html("");
        }

        var typeCompteRadiosValue = $("input[name='typeCompteRadios']:checked").val();
        if(typeCompteRadiosValue == "isFournisseur"){
            $("#divUserOpp").show();
        }else{
            $("#divUserOpp").hide();
        }

        if (champErreur != "") {
            retour = false;
        }

        return retour;
    }

    function controleSaisieInfoUser() {

        // Contrôle droits de diffusion
        if(!$("#useraccept").is(':checked')){
            $("#messageInfoUser").addClass("formErrors");
            $("#messageInfoUser").html("Vous devez accepter les droits de diffusion pour valider votre compte");
            return false;
        } else {
            $("#messageInfoUser").removeClass("formErrors");
            $("#messageInfoUser").html("");
        }
        var typeCompteRadiosValue = $("input[name='typeCompteRadios']:checked").val();
        // Contrôle OPP obligatoire
        if($("#user_opp").val() == "" && typeCompteRadiosValue == "isFournisseur"){
            $("#messageInfoUser").addClass("formErrors");
            $("#messageInfoUser").html("Sélection d'un OPP obligatoire");
            return false;
        } else {
            $("#messageInfoUser").removeClass("formErrors");
            $("#messageInfoUser").html("");
        }

        return true;
    }

    // Vérification du mail en Bdd -->
    function checkMailBdd(id, mail){
        var trouve = false;

        $.ajax({url: PARAMETRES.url + "/check/mail",
                type : 'POST', // Le type de la requête HTTP.
                dataType: "json", 
                async: false, // Mode synchrone
                data: ({
                    id:    id,
                    mail: mail
                }),
                success: function(reponse, statut){
                    //alert(reponse.data.length);
                    if (statut == "success") {
                        if(reponse.data.length>0) {
                            if(reponse.data[0] !== '') {
                                if(reponse.data.length>1) {
                                    trouve = true;
                                }else {
                                    if (reponse.data[0].userId.toString() !== id) {
                                        trouve = true;
                                    }
                                }
                            }
                        }
                        //alert("Data: " + data.data.length + "\nStatus: " + status);
                    } else {
                        alert('Une erreur a été rencontrée à la vérification du mail');
                    }
                },
                error : function(resultat, statut, erreur){
                    alert(erreur);
                }
            });

        return trouve;
    }
    // Vérification du mail en Bdd <--

    
        // Validation de l'adresse email -->
        $("#c_email").on('blur', function(event) {
            var emailValide = false;
            if ($(this).val() !== "") {
                if (! isValidateEmail($(this).val())) {
                    $(this).addClass("formErrors");
                    $(this).nextAll("span.formErrors:first").html("Email incorrect"); 
                }else {
                    $(this).removeClass("formErrors");
                    $(this).nextAll("span.formErrors:first").html("");
                    emailValide = true;
                }
            }else {
                $(this).removeClass("formErrors");
                $(this).nextAll("span.formErrors:first").html("");
            }

            if (emailValide) {
                // Si le mail est valide, alors on vérifie qu'il est unique en bdd
                var mailExiste = true;
                mailExiste = checkMailBdd('', $(this).val());
                if (mailExiste) {
                    $(this).addClass("formErrors");
                    $(this).nextAll("span.formErrors:first").html("Un compte est déjà associé à cette adresse mail dans notre système. Réinitialiser votre mot de passe."); 
                }else {
                    $(this).removeClass("formErrors");
                    $(this).nextAll("span.formErrors:first").html("");
                }
            }
        });
        // Validation de l'adresse email <--

        
        function isValidateEmail(sEmail) {
            var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;

            if (filter.test(sEmail)) {
                return true;
            }
            else {
                return false;
            }
            
        }


        $("#btnReinitPwd").click(function() {
            var dataSend = { email: $("#r_email").val()};

            // Sauvegarde les données
            var jqxhr = $.post(PARAMETRES.url + '/user/reinitPassword'
                    ,dataSend
                    ,function(data, status){
                    //alert("Status: " + status);
                        $("#msgReinit").removeClass('alert-success');
                        $("#msgReinit").removeClass('alert-danger');
                        if (status == "success") {
                            if(data.etat == 'succes') {
                                $("#msgReinit").addClass('alert');
                                $("#msgReinit").addClass('alert-success');
                                $("#msgReinit").html('Votre mot de passe a été réinitialisé et un nouveau mot de passe vous a été envoyé par mail.');
                            } else {
                                $("#msgReinit").addClass('alert');
                                $("#msgReinit").addClass('alert-danger');
                                $("#msgReinit").html('Une erreur a été rencontrée à la réinitialisation du mot de passe.<br/>< ' + data.erreur.erreurCode + " - " + data.erreur.erreurMessage + " >");
                            }
                        } else {
                            $("#msgReinit").addClass('alert');
                            $("#msgReinit").addClass('alert-danger');
                            $("#msgReinit").html('Une erreur a été rencontrée à la réinitialisation du mot de passe.');
                        }

                });
        });

    //Réinitialisation du mot de passe
    
    // When the user clicks on the password field, show the message box
    $("#plainPassword_first").on('focus', function(event){
        $("#message").css("display", 'block');
    });

    // When the user clicks outside of the password field, hide the message box
    $("#plainPassword_first").on('blur', function(event){
        $("#message").css("display", 'none');
    });

    // Action Valider
    $("#btnSaveNewPassword").on('click', function(event){
        var champErreur = '';
        // Contrôle Mot de passe oblibatoire
        if($('#plainPassword_first').val() != $('#plainPassword_second').val()){
            $("#plainPassword_first").addClass("formErrors");
            $("#plainPassword_second").addClass("formErrors");
            $("#plainPassword_first").nextAll("span.formErrors:first").html("Les mots de passe sont différents");
            if (champErreur == "") champErreur = "#plainPassword_first";
        }else if($('#plainPassword_first').val().length < 6){
            $('#plainPassword_first').addClass('formErrors');
            $('#plainPassword_second').addClass('formErrors');
            $("#plainPassword_first").nextAll("span.formErrors:first").html("Taille minimum du mot de passe : 6 caractères");
            if (champErreur == "") champErreur = "#plainPassword_first";
        }else {
            $("#plainPassword_first").removeClass("formErrors");
            $("#plainPassword_second").removeClass("formErrors");
            $("#plainPassword_first").nextAll("span.formErrors:first").html("");
        }
        
        if (champErreur != "") {
            return false;
        }

        var dataSend = { userId: $("#userId").val(), 
                            userPwd: $("#plainPassword_first").val()
                        };

        // Sauvegarde les données contact
        var jqxhr = $.post(PARAMETRES.url + '/user/updatePassword'
            ,dataSend
            ,function(data, status){
            //alert("Data: " + data + "\nStatus: " + status);
                if (status == "success") {
                    var url = PARAMETRES.url + "/public/popp";
                    document.location.href = url;
                } else {
                    alert('Un erreur a été rencontrée à l\'enregistrement');
                }

        });
    });
})