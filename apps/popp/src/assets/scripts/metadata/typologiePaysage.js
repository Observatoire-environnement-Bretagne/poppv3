import { PARAMETRES } from '../custom/parametre';
import { flashMessage } from '../flashMessage/flashMessage';

$(function() {

    var tableTypologiePaysage = $('#dataTableTypologiePaysages').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     false,
        searching: false,
        rowId: 0,
        language : PARAMETRES.dataTableFrancais,
        columns: [  
            {data: "typologiePaysage_id"}, {data: "typologiePaysage_nom"}, {data: "typologiePaysage_desc"}, {data: "action", width: "120px"}
        ],
        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            }
        ]
    });

    $('#dataTableTypologiePaysages tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        var ligneIndex = $(elem).parent().parent();
        var data = tableTypologiePaysage.row(ligneIndex).data();
        updateModalTypologiePaysage(data);
        $("#modalTypologiePaysage").modal('toggle');
    });

   

    $('#dataTableTypologiePaysages tbody a.remove').jConfirm().on('confirm', function(e){
        //recupère l'élement cliqué
        var elem = e.currentTarget;
        //récupère la ligne
        var ligneIndex = $(elem).parent().parent();
        //column 0 => id de la serie
        var typologiePaysageId = tableTypologiePaysage.row(ligneIndex).data().typologiePaysage_id;
        //Appel au serveur                
        $.ajax({
            url: PARAMETRES.url + '/admin/remove/typologiePaysage/' + typologiePaysageId,
        })
        .done(function( data, message ) {
            //quand l'appel est terminé
            //si OK
            if(data.status == "ok"){
                //supprime la ligne dans le tableau
                tableTypologiePaysage.row(ligneIndex).remove().draw( false );
                
                flashMessage("success", "Succès", "La typologie de paysage a été supprimée");
        
                //envoie un message utilisateur pour informer que tout est ok
            } 
        })

        .fail(function(data, message) {
            //si pas OK
            if (data.status){
                 flashMessage("danger", "Erreur", "La typologie de paysage ne peut pas être supprimée");
            }
               
        })

    });



    $("#addTypologiePaysage").click(function(){
        updateModalTypologiePaysage(null);
        $("#modalTypologiePaysage").modal('toggle');
    })

    function updateModalTypologiePaysage(data) {
        if(data){
            $('#typologiePaysage_nom').val(data.typologiePaysage_nom);
            $("#typologiePaysage_desc").val(data.typologiePaysage_desc);
            $('#typologiePaysage_id').val(data.typologiePaysage_id);
        }else{
            $('#typologiePaysage_nom').val("");
            $("#typologiePaysage_desc").val("");
            $('#typologiePaysage_id').val("new");
        }
    }

    
    $("#saveTypologiePaysage").click(function(){
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
        
        insertionChamp['typologiePaysage_desc'] = $("#typologiePaysage_desc").val();
        if($(".is-invalid").length ==  0){
            $.post({
                type: "POST",
                url: PARAMETRES.url + '/admin/update/typologiePaysage/' + insertionChamp['typologiePaysage_id'],
                data: insertionChamp
            }).done(function( response ) {
                if(response.status == 'ok'){
                    if (insertionChamp['typologiePaysage_id'] == "new"){
                        tableTypologiePaysage.row.add({
                            typologiePaysage_id: response.typologiePaysageId,
                            typologiePaysage_nom: insertionChamp['typologiePaysage_nom'],
                            typologiePaysage_desc: insertionChamp['typologiePaysage_desc'],
                            action: '<a class="modify"><i class="c-light-blue-500 cur-p ti ti-pencil"></i></a><a class="remove"><i class="c-red-500 cur-p ti ti-trash"></i></a>'}
                        ).draw();
                    }else{
                        for (var i=0; i<tableTypologiePaysage.data().length; i++) {
                            if (tableTypologiePaysage.data()[i].typologiePaysage_id == $("#typologiePaysage_id").val()) {
                                tableTypologiePaysage.data()[i].typologiePaysage_nom = $("#typologiePaysage_nom").val();
                                tableTypologiePaysage.data()[i].typologiePaysage_desc = $("#typologiePaysage_desc").val();
                                // mise à jour du tableau
                                var ligne = tableTypologiePaysage.row(i).data();
                                tableTypologiePaysage.row(i).data(ligne).invalidate();
                            }
                        }
                    }
                }else{
                    alert(data.message);
                }
                $('#modalTypologiePaysage').modal('toggle');
            }).fail(function() {
                alert( "error" );
            });
        }
    });

})
