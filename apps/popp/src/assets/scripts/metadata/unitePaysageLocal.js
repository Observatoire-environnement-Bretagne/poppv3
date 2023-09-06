import { PARAMETRES } from '../custom/parametre';
import { flashMessage } from '../flashMessage/flashMessage';

$(function() {

    var tableUnitePaysageLocale = $('#dataTableUnitePaysageLocales').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     false,
        searching: false,
        rowId: 0,
        language : PARAMETRES.dataTableFrancais,
        columns: [  
            {data: "unitePaysageLocale_id"}, {data: "unitePaysageLocale_nom"}, {data: "unitePaysageLocale_desc"}, {data: "action", width: "120px"}
        ],
        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            }
        ]
    });

    $('#dataTableUnitePaysageLocales tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        var ligneIndex = $(elem).parent().parent();
        var data = tableUnitePaysageLocale.row(ligneIndex).data();
        updateModalUnitePaysageLocale(data);
        $("#modalUnitePaysageLocale").modal('toggle');
    });



    $('#dataTableUnitePaysageLocales tbody a.remove').jConfirm().on('confirm', function(e){
        //recupère l'élement cliqué
        var elem = e.currentTarget;
        //récupère la ligne
        var ligneIndex = $(elem).parent().parent();
        //column 0 => id de la serie
        var unitePaysageLocaleId = tableUnitePaysageLocale.row(ligneIndex).data().unitePaysageLocale_id;
        //Appel au serveur                
        $.ajax({
            url: PARAMETRES.url + '/admin/remove/unitePaysageLocale/' + unitePaysageLocaleId,
        })
        .done(function( data, message ) {
            //quand l'appel est terminé
            //si OK
            if(data.status == "ok"){
                //supprime la ligne dans le tableau
                tableUnitePaysageLocale.row(ligneIndex).remove().draw( false );
                
                flashMessage("success", "Succès", "L'unité de paysage locale a été supprimée");
        
                //envoie un message utilisateur pour informer que tout est ok
            } 
        })

        .fail(function(data, message) {
            //si pas OK
            if (data.status){
                 flashMessage("danger", "Erreur", "L'unité de paysage locale ne peut pas être supprimée");
            }
               
        })

    });



    $("#addUnitePaysageLocale").click(function(){
        updateModalUnitePaysageLocale(null);
        $("#modalUnitePaysageLocale").modal('toggle');
    })

    function updateModalUnitePaysageLocale(data) {
        if(data){
            $('#unitePaysageLocale_nom').val(data.unitePaysageLocale_nom);
            $('#unitePaysageLocale_desc').val(data.unitePaysageLocale_desc);
            $('#unitePaysageLocale_id').val(data.unitePaysageLocale_id);
        }else{
            $('#unitePaysageLocale_nom').val("");
            $('#unitePaysageLocale_desc').val("");
            $('#unitePaysageLocale_id').val("new");
        }
    }

    
    $("#saveUnitePaysageLocale").click(function(){
        var insertionChamp = {};
        //on boucle sur les champs pour détecter les erreurs et remplir le tableau du POST
        $(".form-control").each(function(index){
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
        
        insertionChamp['unitePaysageLocale_desc'] = $('#unitePaysageLocale_desc').val();
        if($(".is-invalid").length ==  0){
            $.post({
                type: "POST",
                url: PARAMETRES.url + '/admin/update/unitePaysageLocale/' + insertionChamp['unitePaysageLocale_id'],
                data: insertionChamp
            }).done(function( response ) {
                if(response.status == 'ok'){
                    if (insertionChamp['unitePaysageLocale_id'] == "new"){
                        tableUnitePaysageLocale.row.add({
                            unitePaysageLocale_id: response.unitePaysageLocaleId,
                            unitePaysageLocale_nom: insertionChamp['unitePaysageLocale_nom'],
                            unitePaysageLocale_desc: insertionChamp['unitePaysageLocale_desc'],
                            action: '<a class="modify"><i class="c-light-blue-500 cur-p ti ti-pencil"></i></a><a class="remove"><i class="c-red-500 cur-p ti ti-trash"></i></a>'}
                        ).draw();
                    }else{
                        for (var i=0; i<tableUnitePaysageLocale.data().length; i++) {
                            if (tableUnitePaysageLocale.data()[i].unitePaysageLocale_id == $("#unitePaysageLocale_id").val()) {
                                tableUnitePaysageLocale.data()[i].unitePaysageLocale_nom = $("#unitePaysageLocale_nom").val();
                                tableUnitePaysageLocale.data()[i].unitePaysageLocale_desc = $('#unitePaysageLocale_desc').val();
                                // mise à jour du tableau
                                var ligne = tableUnitePaysageLocale.row(i).data();
                                tableUnitePaysageLocale.row(i).data(ligne).invalidate();
                            }
                        }
                    }
                }else{
                    alert(data.message);
                }
                $('#modalUnitePaysageLocale').modal('toggle');
            }).fail(function() {
                alert( "error" );
            });
        }
    });

})
