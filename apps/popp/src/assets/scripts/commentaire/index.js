import { PARAMETRES } from '../custom/parametre';
import { flashMessage } from '../flashMessage/flashMessage';

$(function() {

    var tableCommentaires = $("#dataTableCommentaires").DataTable( {
        paging:false,
        searching: true,
        language : PARAMETRES.dataTableFrancais,
        columns: [  
            {data: "commentaire_id"}, {data: "commentaire_auteur"}, {data: "commentaire_text"}, {data: "commentaire_photo"}, {data: "commentaire_serie"}, 
            {data: "commentaire_date"}, {data: "action", width: "120px"}
        ],
        columnDefs: [   
            { 
                targets: [ 0 ], 
                visible: false, 
                searchable: false
            }
        ],
        
        "drawCallback": function ( settings ) {
            
            $('#dataTableCommentaires tbody a.remove').jConfirm().on('confirm', function(e){
                //recupère l'élement cliqué
                var elem = e.currentTarget;
                //récupère la ligne
                var ligneIndex = $(elem).parent().parent().parent();
                //column 0 => id de la serie
                var commentaireId = tableCommentaires.row(ligneIndex).data().commentaire_id;
                //Appel au serveur                
                $.ajax({
                    url: PARAMETRES.url + '/gestion/remove/commentaire/' + commentaireId, 
                })
                .done(function( data, message ) {
                    //quand l'appel est terminé
                    //si OK
                    if(data.status == "ok"){
                        //supprime la ligne dans le tableau
                        tableCommentaires.row(ligneIndex).remove().draw( false );
                        
                        flashMessage("success", "Succès", "Le commentaire a été supprimé");
                
                        //envoie un message utilisateur pour informer que tout est ok
                    } 
                })
        
                .fail(function(data, message) {
                    //si pas OK
                    if (response.status){
                         flashMessage("danger", "Erreur", "Le commentaire ne peut pas être supprimé");
                    }
                       
                })
            });
        }
    } );

    $('#dataTableCommentaires tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        $(elem)
        .children()
        .toggleClass('ti-na ti-check-box')
        .toggleClass('c-red-500 c-light-green-500');

        var data = tableCommentaires.row( $(elem).parent().parent().parent() ).data();
        $.post({
            url: PARAMETRES.url + '/gestion/commentaire/publication/' + data.commentaire_id
        });
        var etat = $(elem).parent().parent().parent().attr('data-etat');
        if (etat == '0'){
            $(elem).parent().parent().parent().attr('data-etat', '1')
        }else{
            $(elem).parent().parent().parent().attr('data-etat', '0')
        }
    });
    
    $("#commentairePublie").change(function(){
        var checkbox = $(this);
        
		if(checkbox.is(':checked')){
            $('#dataTableCommentaires tbody tr').each(function(index){
                if($(this).attr('data-etat') == 0){
                    $(this).fadeIn();
                }else{
                    $(this).fadeOut();
                }
            });
		} else {
			$("#dataTableCommentaires tbody tr").fadeIn();
		}

        /*$('a.remove').jConfirm().on('confirm', function(e){
            //recupère l'élement cliqué
            var elem = e.currentTarget;
            //récupère la ligne
            var ligneIndex = $(elem).parent().parent().parent();
            //column 0 => id de la serie
            var commentaireId = tableCommentaires.row(ligneIndex).data().commentaire_id;
            //Appel au serveur                
            $.ajax({
                url: PARAMETRES.url + '/gestion/remove/commentaire/' + commentaireId, 
            })
            .done(function( data, message ) {
                //quand l'appel est terminé
                //si OK
                if(response.status == "ok"){
                    //supprime la ligne dans le tableau
                    tableCommentaires.row(ligneIndex).remove().draw( false );
                    
                    flashMessage("success", "Succès", "Le commentaire a été supprimé");
            
                    //envoie un message utilisateur pour informer que tout est ok
                } 
            })
    
            .fail(function(data, message) {
                //si pas OK
                if (response.status){
                     flashMessage("danger", "Erreur", "Le commentaire ne peut pas être supprimé");
                }
                   
            })
    
        });*/

    })

    
})