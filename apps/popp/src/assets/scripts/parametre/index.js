import { PARAMETRES } from '../custom/parametre';

$(function() {

    var tableParametre = $('#dataTableParametres').DataTable({
        "paging":   false,
        "ordering": true,
        "info":     false,
        searching: false,
        rowId: 0,
        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            }
        ]
    });

    $('#dataTableParametres a.modify').click(function(){
        var idParametre = tableParametre.row(this.parentElement.parentElement).data()[0];
        var codeParametre = tableParametre.row(this.parentElement.parentElement).data()[1];
        var valeurParametre = tableParametre.row(this.parentElement.parentElement).data()[2];
        updateModalParametre(idParametre, valeurParametre, codeParametre);
    });

    function updateModalParametre(idParametre, valeurParametre, codeParametre) {
        $('#parametreVal').val(valeurParametre);
        $('#idParametre').val(idParametre);
        $('#codeParametre').html(codeParametre);
    }

    $("#saveParametre").click(function(){
        var idParametre = $('#idParametre').val();
        var parametreVal = $('#parametreVal').val();

        $.post({
            type: "POST",
            url: PARAMETRES.url + '/admin/update/parametre/' + idParametre,
            data: {
                'parametreVal': parametreVal
            }
        }).done(function( ) {
            var temp = tableParametre.row("#"+idParametre).data();
            temp[2] = parametreVal;
            tableParametre.row("#"+idParametre).data(temp).draw();
            $('#modalParametre').modal('toggle');
        }).fail(function() {
            alert( "error" );
        });
    });
})
