import { PARAMETRES } from '../custom/parametre';
    
$(function() {

    var tableLogo = $('#dataTableShowStructures').DataTable({
        "paging":   true,
        "ordering": true,
        "info":     false,
        searching: false,
        language : PARAMETRES.dataTableFrancais,
        rowId: 0,
        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            }
        ]
    });
})