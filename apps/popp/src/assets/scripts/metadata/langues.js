import { PARAMETRES } from '../custom/parametre';
import { flashMessage } from '../flashMessage/flashMessage';

$(function() {

    var tableLangue = $('#dataTableLangues').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     false,
        searching: false,
        rowId: 0,
        language : PARAMETRES.dataTableFrancais,
        columns: [  
            {data: "langue_id"}, {data: "langue_nom"}, {data: "langue_desc"}, {data: "action", width: "120px"}
        ],
        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            }
        ]
    });

    $('#dataTableLangues tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        var ligneIndex = $(elem).parent().parent();
        var data = tableLangue.row(ligneIndex).data();
        updateModalLangue(data);
        $("#modalLangue").modal('toggle');
    });




    $('#dataTableLangues tbody a.remove').jConfirm().on('confirm', function(e){
        //recupère l'élement cliqué
        var elem = e.currentTarget;
        //récupère la ligne
        var ligneIndex = $(elem).parent().parent();
        //column 0 => id de la serie
        var langueId = tableLangue.row(ligneIndex).data().langue_id;
        //Appel au serveur                
        $.ajax({
            url: PARAMETRES.url + '/admin/remove/langue/' + langueId,
        })
        .done(function( data, message ) {
            //quand l'appel est terminé
            //si OK
            if(data.status == "ok"){
                //supprime la ligne dans le tableau
                tableLangue.row(ligneIndex).remove().draw( false );
                
                flashMessage("success", "Succès", "La langue a été supprimée");
        
                //envoie un message utilisateur pour informer que tout est ok
            } 
        })

        .fail(function(data, message) {
            //si pas OK
            if (data.status){
                 flashMessage("danger", "Erreur", "La langue ne peut pas être supprimée");
            }
               
        })

    });



    $("#addLangue").click(function(){
        updateModalLangue(null);
        $("#modalLangue").modal('toggle');
    })

    function updateModalLangue(data) {
        if(data){
            $('#langue_nom').val(data.langue_nom);
            $("#langue_desc").val(data.langue_desc);
            //CKEDITOR.instances.langue_desc.setData(data.langue_desc);
            $('#langue_id').val(data.langue_id);
        }else{
            $('#langue_nom').val("");
            $("#langue_desc").val("");
            //CKEDITOR.instances.langue_desc.setData("");
            $('#langue_id').val("new");
        }
    }

    
    $("#saveLangue").click(function(){
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
        
        //insertionChamp['langue_desc'] = CKEDITOR.instances.langue_desc.getData();
        insertionChamp['langue_desc'] = $("#langue_desc").val();;
        if($(".is-invalid").length ==  0){
            $.post({
                type: "POST",
                url: PARAMETRES.url + '/admin/update/langue/' + insertionChamp['langue_id'],
                data: insertionChamp
            }).done(function( response ) {
                if(response.status == 'ok'){
                    if (insertionChamp['langue_id'] == "new"){
                        tableLangue.row.add({
                            langue_id: response.langueId,
                            langue_nom: insertionChamp['langue_nom'],
                            langue_desc: insertionChamp['langue_desc'],
                            action: '<a class="modify"><i class="c-light-blue-500 cur-p ti ti-pencil"></i></a><a class="remove"><i class="c-red-500 cur-p ti ti-trash"></i></a>'}
                        ).draw();
                    }else{
                        for (var i=0; i<tableLangue.data().length; i++) {
                            if (tableLangue.data()[i].langue_id == $("#langue_id").val()) {
                                tableLangue.data()[i].langue_nom = $("#langue_nom").val();
                                tableLangue.data()[i].langue_desc = $("#langue_desc").val();
                                // mise à jour du tableau
                                var ligne = tableLangue.row(i).data();
                                tableLangue.row(i).data(ligne).invalidate();
                            }
                        }
                    }
                }else{
                    alert(data.message);
                }
                $('#modalLangue').modal('toggle');
            }).fail(function() {
                alert( "error" );
            });
        }
    });

})
