import { PARAMETRES } from '../custom/parametre';

$(function() {

    var tablePhotos = $('#dataTablePhotos').DataTable({
        "paging":   true,
        "ordering": true,
        "info":     false,
        searching: true,
        rowId: 0,
        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            }
        ]
    });

    $('#dataTablePhotos a.remove').click(function(){
        if(confirm("Vous êtes sur le point de supprimer une structure. Souhaitez vous continuer ?")){
            var photoId = tablePhotos.row(this.parentElement.parentElement).data()[0];
            $.ajax({
                url: PARAMETRES.url + '/administrateur/remove/photo/' + photoId,
            })
            tablePhotos.row(this.parentElement.parentElement).remove().draw( false );
        }
    });

    $('#dataTablePhotos a.modify').click(function(){
        var photoId = tablePhotos.row(this.parentElement.parentElement).data()[0];

        window.location.replace(PARAMETRES.url + "/administrateur/update/photo/"+ photoId);
        
        $('#dataTableSeriePhoto a.modify')
        
    });

})
//}())
