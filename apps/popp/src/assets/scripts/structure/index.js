import { PARAMETRES } from '../custom/parametre';

$(function() {
    var tableStructure = $('#dataTableStructures').DataTable({
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
            $('#dataTableStructures tbody td:not(:last-child)').click(function (idx, ev) {
                //if (idx.currentTarget.cellIndex != 0) {
                    // redirect
                    var structId = tableStructure.row( $(this).parent() ).data()[0];
                    var url = PARAMETRES.url + "/show/structure/" + structId;

                    if (idx.ctrlKey){
                        window.open(url,'_blank')
                    }else{
                        document.location.href = url;
                    }
                //}
            });
            
        },
    });

    $('#dataTableStructures tbody').on( 'click', 'a.remove', e => {
    //$('#dataTableStructures a.remove').click(function(){
        if(confirm("Vous êtes sur le point de supprimer une structure. Souhaitez vous continuer ?")){
            var elem = e.currentTarget;
            var ligneIndex = $(elem).parent().parent();
            var structureId = tableStructure.row(ligneIndex).data()[0];
            //var structureId = tableStructure.row(this.parentElement.parentElement).data()[0];
            $.ajax({
                url: PARAMETRES.url + '/admin/remove/structures/' + structureId,
                success: function (data) {
                    if(data.status == 'ok'){
                        for (var i=0; i<tableStructure.data().length; i++) {
                            if (tableStructure.data()[i][0] == structureId) {
                                tableStructure.row(i).remove().draw( false );
                            }
                        }
                        //tableStructure.row(this.parentElement.parentElement).remove().draw( false );
                    }else{
                        alert(data.message);
                    }
                },
                error : function (){
                    alert("Une erreur a été rencontrée lors de la suppression");
                }
            })
        }
    });

    //$('#dataTableStructures a.modify').click(function(){
    $('#dataTableStructures tbody').on( 'click', 'a.modify', e => {
        //var structureId = tableStructure.row(this.parentElement.parentElement).data()[0];
        var elem = e.currentTarget;
        var ligneIndex = $(elem).parent().parent();
        var structureId = tableStructure.row(ligneIndex).data()[0];
        window.location.replace(PARAMETRES.url + "/admin/update/structures/"+ structureId);
    });
})
