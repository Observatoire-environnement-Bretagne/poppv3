import { PARAMETRES } from '../custom/parametre';
import { flashMessage } from '../flashMessage/flashMessage';

$(function() {

    var tableFormat = $('#dataTableFormats').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     false,
        searching: false,
        rowId: 0,
        language : PARAMETRES.dataTableFrancais,
        columns: [  
            {data: "format_id"}, {data: "format_nom"}, {data: "format_desc"}, {data: "action", width: "120px"}
        ],
        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            }
        ]
    });

    $('#dataTableFormats tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        var ligneIndex = $(elem).parent().parent();
        var data = tableFormat.row(ligneIndex).data();
        updateModalFormat(data);
        $("#modalFormat").modal('toggle');
    });

    
    $('#dataTableFormats tbody a.remove').jConfirm().on('confirm', function(e){
        //recupère l'élement cliqué
        var elem = e.currentTarget;
        //récupère la ligne
        var ligneIndex = $(elem).parent().parent();
        //column 0 => id de la serie
        var formatId = tableFormat.row(ligneIndex).data().format_id;
        //Appel au serveur                
        $.ajax({
            url: PARAMETRES.url + '/admin/remove/format/' + formatId,
        })
        .done(function( data, message ) {
            //quand l'appel est terminé
            //si OK
            if(data.status == "ok"){
                //supprime la ligne dans le tableau
                tableFormat.row(ligneIndex).remove().draw( false );
                
                flashMessage("success", "Succès", "Le format a été supprimé");
        
                //envoie un message utilisateur pour informer que tout est ok
            } 
        })

        .fail(function(data, message) {
            //si pas OK
            if (data.status){
                 flashMessage("danger", "Erreur", "Le format ne peut pas être supprimé");
            }
               
        })

    });



    $("#addFormat").click(function(){
        updateModalFormat(null);
        $("#modalFormat").modal('toggle');
    })

    function updateModalFormat(data) {
        if(data){
            $('#format_nom').val(data.format_nom);
            $('#format_desc').val(data.format_desc);
            $('#format_id').val(data.format_id);
        }else{
            $('#format_nom').val("");
            $('#format_desc').val("");
            $('#format_id').val("new");
        }
    }

    
    $("#saveFormat").click(function(){
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
        
        //insertionChamp['format_desc'] = CKEDITOR.instances.format_desc.getData();
        insertionChamp['format_desc'] = $('#format_desc').val();
        if($(".is-invalid").length ==  0){
            $.post({
                type: "POST",
                url: PARAMETRES.url + '/admin/update/format/' + insertionChamp['format_id'],
                data: insertionChamp
            }).done(function( response ) {
                if(response.status == 'ok'){
                    if (insertionChamp['format_id'] == "new"){
                        tableFormat.row.add({
                            format_id: response.formatId,
                            format_nom: insertionChamp['format_nom'],
                            format_desc: insertionChamp['format_desc'],
                            action: '<a class="modify"><i class="c-light-blue-500 cur-p ti ti-pencil"></i></a><a class="remove"><i class="c-red-500 cur-p ti ti-trash"></i></a>'}
                        ).draw();
                    }else{
                        for (var i=0; i<tableFormat.data().length; i++) {
                            if (tableFormat.data()[i].format_id == $("#format_id").val()) {
                                tableFormat.data()[i].format_nom = $("#format_nom").val();
                                tableFormat.data()[i].format_desc = $('#format_desc').val();
                                // mise à jour du tableau
                                var ligne = tableFormat.row(i).data();
                                tableFormat.row(i).data(ligne).invalidate();
                            }
                        }
                    }
                }else{
                    alert(data.message);
                }
                $('#modalFormat').modal('toggle');
            }).fail(function() {
                alert( "error" );
            });
        }
    });

})
