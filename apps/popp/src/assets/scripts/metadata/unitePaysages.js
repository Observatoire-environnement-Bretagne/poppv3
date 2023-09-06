import { PARAMETRES } from '../custom/parametre';
import { flashMessage } from '../flashMessage/flashMessage';

$(function() {

    /* UNITE PAYSAGE DEBUT */
    
    var tableUnitePaysage = $('#dataTableUnitePaysages').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     false,
        searching: false,
        rowId: 0,
        language : PARAMETRES.dataTableFrancais,
        columns: [  
            {data: "unitePaysage_id"}, {data: "unitePaysage_nom"}, {data: "unitePaysage_desc"},
            {data: "ensemblePaysage_nom"},{data: "ensemblePaysage_id"}, {data: "action", width: "120px"}
        ],
        "columnDefs": [
            {
                "targets": [ 0, 4 ],
                "visible": false,
                "searchable": false
            }
        ]
    });

    $('#dataTableUnitePaysages tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        var ligneIndex = $(elem).parent().parent();
        var data = tableUnitePaysage.row(ligneIndex).data();
        updateModalUnitePaysage(data);
        $("#modalUnitePaysage").modal('toggle');
    });



    $('#dataTableUnitePaysages tbody a.remove').jConfirm().on('confirm', function(e){
        //recupère l'élement cliqué
        var elem = e.currentTarget;
        //récupère la ligne
        var ligneIndex = $(elem).parent().parent();
        //column 0 => id de la serie
        var unitePaysageId = tableUnitePaysage.row(ligneIndex).data().unitePaysage_id;
        //Appel au serveur                
        $.ajax({
            url: PARAMETRES.url + '/admin/remove/unitePaysage/' + unitePaysageId,
        })
        .done(function( data, message ) {
            //quand l'appel est terminé
            //si OK
            if(data.status == "ok"){
                //supprime la ligne dans le tableau
                tableUnitePaysage.row(ligneIndex).remove().draw( false );
                
                flashMessage("success", "Succès", "L'unité de paysage a été supprimée");
        
                //envoie un message utilisateur pour informer que tout est ok
            } 
        })

        .fail(function(data, message) {
            //si pas OK
            if (data.status){
                 flashMessage("danger", "Erreur", "L'unité de paysage ne peut pas être supprimée");
            }
               
        })

    });



    $("#addUnitePaysage").click(function(){
        updateModalUnitePaysage(null);
        $("#modalUnitePaysage").modal('toggle');
    })

    function updateModalUnitePaysage(data) {
        if(data){
            $('#unitePaysage_nom').val(data.unitePaysage_nom);
            $('#unitePaysage_desc').val(data.unitePaysage_desc);
            $('#ensemblePaysage').val(data.ensemblePaysage_id);
            $('#unitePaysage_id').val(data.unitePaysage_id);
        }else{
            $('#unitePaysage_nom').val("");
            $('#unitePaysage_desc').val("");
            $('#ensemblePaysage').val("");
            $('#unitePaysage_id').val("new");
        }
    }

    
    $("#saveUnitePaysage").click(function(){
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
        
        //insertionChamp['unitePaysage_desc'] = CKEDITOR.instances.unitePaysage_desc.getData();
        insertionChamp['unitePaysage_desc'] = $('#unitePaysage_desc').val();
        if($(".is-invalid").length ==  0){
            $.post({
                type: "POST",
                url: PARAMETRES.url + '/admin/update/unitePaysage/' + insertionChamp['unitePaysage_id'],
                data: insertionChamp
            }).done(function( response ) {
                if(response.status == 'ok'){
                    if (insertionChamp['unitePaysage_id'] == "new"){
                        tableUnitePaysage.row.add({
                            unitePaysage_id: response.unitePaysageId,
                            unitePaysage_nom: insertionChamp['unitePaysage_nom'],
                            unitePaysage_desc: insertionChamp['unitePaysage_desc'],
                            ensemblePaysage_id: insertionChamp['ensemblePaysage'],
                            ensemblePaysage_nom: $("#ensemblePaysage option:selected").text(),
                            action: '<a class="modify"><i class="c-light-blue-500 cur-p ti ti-pencil"></i></a><a class="remove"><i class="c-red-500 cur-p ti ti-trash"></i></a>'}
                        ).draw();
                    }else{
                        for (var i=0; i<tableUnitePaysage.data().length; i++) {
                            if (tableUnitePaysage.data()[i].unitePaysage_id == $("#unitePaysage_id").val()) {
                                tableUnitePaysage.data()[i].unitePaysage_nom = $("#unitePaysage_nom").val();
                                //tableUnitePaysage.data()[i].unitePaysage_desc = CKEDITOR.instances.unitePaysage_desc.getData();
                                tableUnitePaysage.data()[i].unitePaysage_desc = $('#unitePaysage_desc').val();
                                tableUnitePaysage.data()[i].ensemblePaysage_id = $('#ensemblePaysage option:selected').val();
                                tableUnitePaysage.data()[i].ensemblePaysage_nom = $('#ensemblePaysage option:selected').text();
                                // mise à jour du tableau
                                var ligne = tableUnitePaysage.row(i).data();
                                tableUnitePaysage.row(i).data(ligne).invalidate();
                            }
                        }
                    }
                }else{
                    alert(data.message);
                }
                $('#modalUnitePaysage').modal('toggle');
            }).fail(function() {
                alert( "error" );
            });
        }
    });
    /* AXE THEMATIQUE FIN */
})
