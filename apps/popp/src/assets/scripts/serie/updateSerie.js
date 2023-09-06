import { PARAMETRES } from '../custom/parametre';
import { flashMessage } from '../flashMessage/flashMessage';


$(function() {
    
    var tableSeries = $('#dataTableSeries').DataTable({
        "paging":   true,
        "ordering": true,
        "info":     false,
        searching: true,
        language : PARAMETRES.dataTableFrancais,
        rowId: 0,
        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            }
        ],
        "drawCallback": function ( settings ) {
            $('#dataTableSeries tbody td:not(:last-child)').click(function (idx, ev) {
                //if (idx.currentTarget.cellIndex != 0) {
                    // redirect
                    var serieId = tableSeries.row( $(this).parent() ).data()[0];
                    var url = PARAMETRES.url + "/public/get/serie/" + serieId;

                    if (idx.ctrlKey){
                        window.open(url,'_blank')
                    }else{
                        document.location.href = url;
                    }
                //}
            });

            $('a.remove').jConfirm().on('confirm', function(e){
                //recupère l'élement cliqué
                var elem = e.currentTarget;
                //récupère la ligne
                var ligneIndex = $(elem).parent().parent();
                //column 0 => id de la serie
                var serieId = tableSeries.row(ligneIndex).data()[0]; 
                //Appel au serveur                
                $.ajax({
                    url: PARAMETRES.url + '/gestion/remove/serie/' + serieId,    
                })
                .done(function( data, message ) {
                    //quand l'appel est terminé
                    //si OK
                    if(data.status == "ok"){
                        //supprime la ligne dans le tableau
                        tableSeries.row(ligneIndex).remove().draw( false );
                        
                        flashMessage("success", "Succès", "La série a été supprimée");
                
                        //envoie un message utilisateur pour informer que tout est ok
                    } 
                })

                .fail(function(data, message) {
                    //si pas OK
                    if (data.status){
                         flashMessage("danger", "Erreur", "La série ne peut pas être supprimée");
                    }
                       
                })
        
            });
        },
    });

    $('#dataTableSeries tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        var ligneIndex = $(elem).parent().parent();
        var serieId = tableSeries.row(ligneIndex).data()[0];
        window.location.replace(PARAMETRES.url + "/gestion/update/serie/"+ serieId);
    });
})
