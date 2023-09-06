import { PARAMETRES } from '../custom/parametre';
import { flashMessage } from '../flashMessage/flashMessage';

$(function() {
    var tableUser = $('#dataTableUsers').DataTable({
        "paging":   true,
        "ordering": true,
        "info":     false,
        searching: true,
        language : PARAMETRES.dataTableFrancais,
        columns: [  
            {data: "user_id"}, {data: "user_nom"}, {data: "user_prenom"}, {data: "user_email"},  
            {data: "user_sexe"}, {data: "user_date_dercnx"}, {data: "user_roles"}, {data: "action", width: "120px"},
            {data: "user_adresse"}, {data: "user_codepostal"}, {data: "user_ville"}, {data: "user_telephone"}
        ],
        "columnDefs": [
            {
                "targets": [ 0, 8, 9, 10, 11],
                "visible": false,
                "searchable": false
            }
        ],
        "drawCallback": function ( settings ) {
            $('#dataTableUsers tbody td:not(:last-child)').unbind().click(function (idx, ev) {
                var userId = tableUser.row( $(this).parent() ).data().user_id;
                var url = PARAMETRES.url + "/gestion/show/user/" + userId;

                if (idx.ctrlKey){
                    window.open(url,'_blank')
                }else{
                    document.location.href = url;
                }
            });
            
            $('a.remove').jConfirm().on('confirm', function(e){
                //recupère l'élement cliqué
                var elem = e.currentTarget;
                //récupère la ligne
                var ligneIndex = $(elem).parent().parent();
                //column 0 => id de la serie
                var idUser = tableUser.row(ligneIndex).data().user_id;
                //Appel au serveur                
                $.ajax({
                    url: PARAMETRES.url + '/gestion/delete/user/' + idUser,
                })
                .done(function( data, message ) {
                    //quand l'appel est terminé
                    //si OK
                    if(data.status == "ok"){
                        //supprime la ligne dans le tableau
                        tableUser.row(ligneIndex).remove().draw( false );
                        
                        flashMessage("success", "Succès", "L'utilisateur a été supprimé");
                        //envoie un message utilisateur pour informer que tout est ok
        
                    } 
                })
        
                .fail(function(data, message) {
                    //si pas OK
                    if (data.status){
                        flashMessage("danger", "Erreur", "l'utilisateur ne peut pas être supprimé");
                        //envoie un message utilisateur pour informer que tout n'est pas ok
                    }
                       
                })
        
            });
        
        },
    
    });

    
    $('#dataTableUsers tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        var data = tableUser.row( $(elem).parent().parent() ).data();
        updateModaluser(data);
        $("#modalUser").modal('toggle');
    });

    


    $("#saveUser").click(function(){
        var error = false;
        var errorMessage = "";
        var idUser = $('#iduser').val();
        var userName = $('#usernom').val();
        var userPrenom = $('#userprenom').val();
        var userSexe = $('#usersexe').val();
        var email = $('#email').val();
        var userRole = $('#userrole').val();
        var userAdresse = $('#useradresse').val();
        var userCodepostal = $('#usercodepostal').val();
        var userVille = $('#userville').val();
        var userTelephone = $('#usertelephone').val();
        var plainPassword_first = $('#plainPassword_first').val();
        var plainPassword_second = $('#plainPassword_second').val(); 
        if(plainPassword_first != plainPassword_second){
            error = true;
            $('#plainPassword_first').addClass('input-error');
            $('#plainPassword_second').addClass('input-error');
            errorMessage += "Les mots de passe sont différents<br>";
        }else if(plainPassword_first.length > 0 && plainPassword_first.length < 6){
            error = true;
            $('#plainPassword_first').addClass('input-error');
            $('#plainPassword_second').addClass('input-error');
            errorMessage += "Taille minimum du mot de passe : 6 caractères<br>";
        }else if(idUser == "new" && plainPassword_first.length < 6){
            error = true;
            $('#plainPassword_first').addClass('input-error');
            $('#plainPassword_second').addClass('input-error');
            errorMessage += "Taille minimum du mot de passe : 6 caractères<br>";
        }

        if(userName == ""){
            error = true;
            $('#usernom').addClass('input-error');
            errorMessage += "Le nom est obligatoire<br>";
        }

        if(email == ""){
            error = true;
            $('#email').addClass('input-error');
            errorMessage += "L'email est obligatoire<br>";
        }
        if(error){
            $("#infoUser").addClass("alert alert-danger");
            $("#infoUser").html(errorMessage);
            return;
        }
        
        $.post({
            type: "POST",
            url: PARAMETRES.url + '/gestion/modify/user/' + idUser,
            data: {
                idUser: idUser,
                userName: userName,
                userPrenom: userPrenom,
                userSexe: userSexe,
                email: email,
                password : plainPassword_first,
                userRole: userRole,
                userAdresse: userAdresse,
                userCodepostal: userCodepostal,
                userVille: userVille,
                userTelephone: userTelephone
            }
        }).done(function( response ) {
            if(response.status == "error"){
                alert(response.message);
            }else{
                if (idUser == 'new'){
                    tableUser.row.add({
                        user_id: response.userId,
                        user_nom: userName,
                        user_prenom: userPrenom,
                        user_email: email,
                        user_sexe: userSexe,
                        user_date_dercnx: '',
                        user_roles: userRole,
                        action: '<a class="modify"><i class="c-light-blue-500 cur-p ti ti-pencil"></i></a><a class="remove"><i class="c-red-500 cur-p ti ti-trash"></i></a>',
                        user_adresse: userAdresse,
                        user_codepostal: userCodepostal,
                        user_ville: userVille,
                        user_telephone: userTelephone}
                    ).draw();
                }else{
                    for (var i=0; i<tableUser.data().length; i++) {
                        if (tableUser.data()[i].user_id == $("#iduser").val()) {
                            tableUser.data()[i].user_nom = userName;
                            tableUser.data()[i].user_prenom = userPrenom;
                            tableUser.data()[i].user_email = email;
                            tableUser.data()[i].user_sexe = userSexe;
                            tableUser.data()[i].user_roles = userRole;
                            tableUser.data()[i].user_adresse = userAdresse;
                            tableUser.data()[i].user_codepostal = userCodepostal;
                            tableUser.data()[i].user_ville = userVille;
                            tableUser.data()[i].user_telephone = userTelephone;
                            // mise à jour du tableau
                            var ligne = tableUser.row(i).data();
                            tableUser.row(i).data(ligne).invalidate();
                        }
                    }
                }
                $('#modalUser').modal('toggle');
            }
        }).fail(function() {
            alert( "error" );
        });
    });

    function updateModaluser(data) {
        if(data == null){
            $('#usernom').val("");
            $('#userprenom').val("");
            $('#email').val("");
            $('#usersexe').val("");
            $('#iduser').val('new');
            $('#userrole').val("");
            $('#useradresse').val("");
            $('#usercodepostal').val("");
            $('#userville').val("");
            $('#usertelephone').val("");
        }else{
            $('#usernom').val(data.user_nom);
            $('#userprenom').val(data.user_prenom);
            $('#email').val(data.user_email);
            $('#usersexe').val(data.user_sexe);
            $('#iduser').val(data.user_id);
            $('#useradresse').val(data.user_adresse);
            $('#usercodepostal').val(data.user_codepostal);
            $('#userville').val(data.user_ville);
            $('#usertelephone').val(data.user_telephone);
            var role = data.user_roles.replace(",", "")
                .replace("ROLE_USER", "")
                .trim();
            $('#userrole').val(role);
        }
        $('#plainPassword_first').val("");
        $('#plainPassword_second').val("");
        $('.input-error').removeClass('input-error');
        $("#infoUser").removeClass("alert alert-danger");
        $("#infoUser").html("");
    }
 
    $("#addUser").click(function(){
        updateModaluser(null);
    });

    /* FOURNISSEURS */
    
    
    var tableFournisseur = $('#dataTableFournisseurs').DataTable({
        "paging":   true,
        "ordering": true,
        "info":     false,
        searching: true,
        language : PARAMETRES.dataTableFrancais,
        columns: [  
            {data: "user_id"}, 
            {data: "user_username"},  
            {data: "user_email"},  
            {data: "user_nb_serie"},  
            {data: "user_nb_freq"},  
            {data: "user_nom_serie"},  
            {data: "user_date_derpub"}, 
            {data: "user_axe"},  
            {data: "user_opp"}, 
            {data: "user_sexe"}, 
            {data: "user_nom"}, 
            {data: "user_prenom"}, 
            {data: "action", width: "120px"}
        ],
        "columnDefs": [
            {
                "targets": [ 0, 8, 9, 10, 11 ],
                "visible": false,
                "searchable": false
            }
        ],
        "drawCallback": function ( settings ) {
            $('#dataTableFournisseurs tbody td:not(:last-child)').click(function (idx, ev) {
                var userId = tableFournisseur.row( $(this).parent() ).data().user_id;
                var url = PARAMETRES.url + "/gestion/show/user/" + userId;

                if (idx.ctrlKey){
                    window.open(url,'_blank')
                }else{
                    document.location.href = url;
                }
            });
            
            $('#dataTableFournisseurs tbody a.remove').jConfirm().on('confirm', function(e){
                //recupère l'élement cliqué
                var elem = e.currentTarget;
                //récupère la ligne
                var ligneIndex = $(elem).parent().parent();
                //column 0 => id de la serie
                var idUser = tableFournisseur.row(ligneIndex).data().user_id;
                //Appel au serveur                
                $.ajax({
                    url: PARAMETRES.url + '/gestion/delete/user/' + idUser,
                })
                .done(function( data, message ) {
                    //quand l'appel est terminé
                    //si OK
                    if(data.status == "ok"){
                        //supprime la ligne dans le tableau
                        tableFournisseur.row(ligneIndex).remove().draw( false );
                        
                        flashMessage("success", "Succès", "L'utilisateur a été supprimé");
                        //envoie un message utilisateur pour informer que tout est ok
        
                    } 
                })
        
                .fail(function(data, message) {
                    //si pas OK
                    if (data.status){
                        flashMessage("danger", "Erreur", "l'utilisateur ne peut pas être supprimé");
                        //envoie un message utilisateur pour informer que tout n'est pas ok
                    }
                       
                })
        
            });

        },
    });

    
    $('#dataTableFournisseurs tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        var data = tableFournisseur.row( $(elem).parent().parent() ).data();
        updateModalFournisseur(data);
        $("#modalFournisseur").modal('toggle');
        $("#useropp_chosen").css('width', '100%');
    });
    
    


    $("#saveFournisseur").click(function(){
        var error = false;
        var errorMessage = "";
        var idUser = $('#iduser').val();
        var userName = $('#usernom').val();
        var userPrenom = $('#userprenom').val();
        var userSexe = $('#usersexe').val();
        var email = $('#email').val();
        var userOpp = $('#useropp').val();
        var plainPassword_first = $('#plainPassword_first').val();
        var plainPassword_second = $('#plainPassword_second').val(); 
        if(plainPassword_first != plainPassword_second){
            error = true;
            $('#plainPassword_first').addClass('input-error');
            $('#plainPassword_second').addClass('input-error');
            errorMessage += "Les mots de passe sont différents<br>";
        }else if(plainPassword_first.length > 0 && plainPassword_first.length < 6){
            error = true;
            $('#plainPassword_first').addClass('input-error');
            $('#plainPassword_second').addClass('input-error');
            errorMessage += "Taille minimum du mot de passe : 6 caractères<br>";
        }else if(idUser == "new" && plainPassword_first.length < 6){
            error = true;
            $('#plainPassword_first').addClass('input-error');
            $('#plainPassword_second').addClass('input-error');
            errorMessage += "Taille minimum du mot de passe : 6 caractères<br>";
        }

        if(userName == ""){
            error = true;
            $('#usernom').addClass('input-error');
            errorMessage += "Le nom est obligatoire<br>";
        }

        if(email == ""){
            error = true;
            $('#email').addClass('input-error');
            errorMessage += "L'email est obligatoire<br>";
        }
        if(error){
            $("#infoUser").addClass("alert alert-danger");
            $("#infoUser").html(errorMessage);
            return;
        }

        
        $.post({
            type: "POST",
            url: PARAMETRES.url + '/gestion/modify/user/' + idUser,
            data: {
                idUser: idUser,
                userName: userName,
                userPrenom: userPrenom,
                userSexe: userSexe,
                email: email,
                password : plainPassword_first,
                userOpp: userOpp,
                isFournisseur : true,
            }
        }).done(function( response ) {
            if(response.status == "error"){
                alert(response.message);
            }else{
                if (idUser == 'new'){
                    tableFournisseur.row.add({
                        user_id: response.userId,
                        user_username: userPrenom + " " + userName,
                        user_nom: userName,
                        user_prenom: userPrenom,
                        user_email: email,
                        user_sexe: userSexe,
                        user_date_derpub: '',
                        user_nom_serie: '',
                        user_nb_serie: '',
                        user_nb_freq: '',
                        user_axe: '',
                        user_opp: userOpp.join(','),
                        action: '<a class="modify"><i class="c-light-blue-500 cur-p ti ti-pencil"></i></a><a class="remove"><i class="c-red-500 cur-p ti ti-trash"></i></a>'}
                    ).draw();
                }else{
                    for (var i=0; i<tableFournisseur.data().length; i++) {
                        if (tableFournisseur.data()[i].user_id == $("#iduser").val()) {
                            tableFournisseur.data()[i].user_username = userPrenom + " " + userName;
                            tableFournisseur.data()[i].user_nom = userName;
                            tableFournisseur.data()[i].user_prenom = userPrenom;
                            tableFournisseur.data()[i].user_email = email;
                            tableFournisseur.data()[i].user_sexe = userSexe;
                            tableFournisseur.data()[i].user_opp = userOpp.join(',');
                            // mise à jour du tableau
                            var ligne = tableFournisseur.row(i).data();
                            tableFournisseur.row(i).data(ligne).invalidate();
                        }
                    }
                }
                $('#modalFournisseur').modal('toggle');
            }
        }).fail(function() {
            alert( "error" );
        });
    });

    function updateModalFournisseur(data) {
        $("#useropp").chosen({disable_search_threshold: 10});
        if(data == null){
            $('#usernom').val("");
            $('#userprenom').val("");
            $('#email').val("");
            $('#usersexe').val("");
            $('#iduser').val('new');
            $("#useropp").val("").trigger('chosen:updated');
        }else{
            $('#usernom').val(data.user_nom);
            $('#userprenom').val(data.user_prenom);
            $('#email').val(data.user_email);
            $('#usersexe').val(data.user_sexe);
            $('#iduser').val(data.user_id);
            $("#useropp").val(data.user_opp.replace(' ', '').split(',')).trigger('chosen:updated');
            //$('#useropp').val(opp);
        }

        $('#plainPassword_first').val("");
        $('#plainPassword_second').val("");
        $('.input-error').removeClass('input-error');
        $("#infoUser").removeClass("alert alert-danger");
        $("#infoUser").html("");
    }
 
    $("#addFournisseur").click(function(){
        updateModalFournisseur(null);
        $("#useropp_chosen").css('width', '100%');
    });
});
//}())
