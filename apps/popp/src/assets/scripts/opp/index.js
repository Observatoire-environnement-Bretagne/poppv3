import { PARAMETRES } from '../custom/parametre';
import { flashMessage } from '../flashMessage/flashMessage';

$(function() {

    var newIdOpp = 0;

    var tableOpp = $('#dataTableOpps').DataTable({
        "paging":   true,
        "ordering": true,
        "info":     false,
        searching: true,
        rowId: 0,
        language : PARAMETRES.dataTableFrancais,
        columns: [  
            {data: "opp_id"}, {data: "opp_name"}, {data: "opp_desc"}, {data: "opp_technicite"},  
            {data: "opp_annee_creation"}, {data: "opp_niv_territ"}, {data: "opp_valo"}, {data: "opp_structure_opp_id"}, 
            {data: "opp_structure_opp_nom"}, {data: "opp_participative", className: "center-align",
                render: function ( data, type, row ) {
                    if (data == true){
                        return '<i class="c-green-500 ti ti-check-box"></i>';
                    }
                    return "";}
            }, {data: "opp_gestionnaires_id"}, {data: "opp_fournisseurs_id"}, {data: "action", width: "120px"}
        ],
        "columnDefs": [
            {
                "targets": [ 0, 2, 3, 5, 6, 7, 10, 11 ],
                "visible": false,
                "searchable": false
            }
        ],
        "drawCallback": function ( settings ) {
            $('#dataTableOpps tbody td:not(:last-child)').click(function (idx, ev) {
                var oppId = tableOpp.row( $(this).parent() ).data().opp_id;
                var url = PARAMETRES.url + "/show/opp/" + oppId;

                if (idx.ctrlKey){
                    window.open(url,'_blank')
                }else{
                    document.location.href = url;
                }
            });
            
            $('a.remove').jConfirm().on('confirm', function(e){
                var elem = e.currentTarget;
                var ligneIndex = $(elem).parent().parent();
            
                var oppId = tableOpp.row(ligneIndex).data().opp_id;
        
                $.ajax({
                    url: PARAMETRES.url + '/gestion/remove/opp/' + oppId,
                })
                .done(function( data, message ) {
                        if(data.status == 'ok'){
                            /*
                            for (var i=0; i<tableOpp.data().length; i++) {
                                if (tableOpp.data()[i][0] == oppId) {
                                    tableOpp.row(i).remove().draw( false );
                                }
                            }*/
                            tableOpp.row(ligneIndex).remove().draw( false );
                            flashMessage("success", "Succès", "Le OPP a été supprimé");
                        } else {
                            flashMessage("danger", "Erreur", data.message);
                        }
                    })
                    .fail(function(data, message) {
                        //si pas OK
                        if (data.status){
                             flashMessage("danger", "Erreur", "Le OPP ne peut pas être supprimé");
                        }
                           
                    }) 
            });

        },
    });

    $('#dataTableOpps tbody').on( 'click', 'a.modify', e => {
        var elem = e.currentTarget;
        var ligneIndex = $(elem).parent().parent();
        var data = tableOpp.row(ligneIndex).data();
        updateModalOpp(data);
        $("#modalOpp").modal('toggle');
    });

    
        
        
        
    

    $("#addOpp").click(function(){
        updateModalOpp(null);
        $("#modalOpp").modal('toggle');
        $("#oppGestionnaires, #oppFournisseurs").chosen({disable_search_threshold: 10});
        $("#oppGestionnaires").val("").trigger('chosen:updated');
        $("#oppFournisseurs").val("").trigger('chosen:updated');
    })

    function updateModalOpp(data){
        if (data == null) {
            $("input.form-control, select.form-control, textarea.form-control").val('');
            $("#oppId").val("new");
            myEditor.setData("");
            //CKEDITOR.instances.oppDesc.setData("");
            $("#oppParticipative").prop("checked", false);
        }else{
            
            $("#oppName").val(data.opp_name);
            myEditor.setData(data.opp_desc);
            //CKEDITOR.instances.oppDesc.setData(data.opp_desc);
            //$("#oppDesc").text(data.opp_desc);
            $("#oppTechnicite").text(data.opp_technicite);
            $("#oppAnneeCreation").val(data.opp_annee_creation);
            $("#oppNivTerrit").val(data.opp_niv_territ);
            $("#oppValo").val(data.opp_valo);
            $("#oppId").val(data.opp_id);
            $("#oppStructureOpp").val(data.opp_structure_opp_id);
            $("#oppParticipative").prop("checked", data.opp_participative);
            $("#oppGestionnaires").val(data.opp_gestionnaires_id.split(',')).trigger('chosen:updated');
            $("#oppFournisseurs").val(data.opp_fournisseurs_id.split(',')).trigger('chosen:updated');
            /*if(data.opp_participative){

            }*/
        }
    }
    
    $("#saveOpp").click(function(){
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
        
        insertionChamp['oppParticipative'] = $("#oppParticipative").is(':checked');
        //insertionChamp['oppDesc'] = CKEDITOR.instances.oppDesc.getData();
        insertionChamp['oppDesc'] = myEditor.getData();
        if($(".is-invalid").length ==  0){
            $.post({
                type: "POST",
                url: PARAMETRES.url + '/gestion/update/opp/' + insertionChamp['oppId'],
                data: insertionChamp
            }).done(function( response ) {
                if (insertionChamp['oppId'] == "new"){
                    tableOpp.row.add({
                        opp_id: response.oppId,
                        opp_name: insertionChamp['oppName'],
                        opp_desc: insertionChamp['oppDesc'],
                        opp_technicite: insertionChamp['oppTechnicite'],
                        opp_annee_creation: insertionChamp['oppAnneeCreation'],
                        opp_niv_territ: insertionChamp['oppNivTerrit'],
                        opp_valo: insertionChamp['oppValo'],
                        opp_structure_opp_id: insertionChamp['oppStructureOpp'],
                        opp_structure_opp_nom: $("#oppStructureOpp option:selected").text(),
                        opp_participative: $("#oppParticipative").is(':checked'),
                        opp_gestionnaires_id: insertionChamp['oppGestionnaires'],
                        opp_fournisseurs_id: insertionChamp['oppFournisseurs'],
                        action: '<a class="modify"><i class="c-light-blue-500 cur-p ti ti-pencil"></i></a><a class="remove"><i class="c-red-500 cur-p ti ti-trash"></i></a>'}
                    ).draw();
                }else{
                    for (var i=0; i<tableOpp.data().length; i++) {
                        if (tableOpp.data()[i].opp_id == $("#oppId").val()) {
                            tableOpp.data()[i].opp_name = $("#oppName").val();
                            tableOpp.data()[i].opp_desc = myEditor.getData();
                            //tableOpp.data()[i].opp_desc = CKEDITOR.instances.oppDesc.getData();
                            tableOpp.data()[i].opp_technicite = $("#oppTechnicite").val();
                            tableOpp.data()[i].opp_annee_creation = $("#oppAnneeCreation").val();
                            tableOpp.data()[i].opp_niv_territ = $("#oppNivTerrit").val();
                            tableOpp.data()[i].opp_valo = $("#oppValo").val();
                            tableOpp.data()[i].opp_structure_opp_id = $("#oppStructureOpp option:selected").val();
                            tableOpp.data()[i].opp_structure_opp_nom = $("#oppStructureOpp option:selected").text();
                            tableOpp.data()[i].opp_participative = $("#oppParticipative").is(':checked');
                            tableOpp.data()[i].opp_gestionnaires_id = insertionChamp['oppGestionnaires'];
                            tableOpp.data()[i].opp_fournisseurs_id = insertionChamp['oppFournisseurs'];
                            // mise à jour du tableau
                            var ligne = tableOpp.row(i).data();
                            tableOpp.row(i).data(ligne).invalidate();
                        }
                    }
                }
                $('#modalOpp').modal('toggle');
            }).fail(function() {
                alert( "error" );
            });
        }
    });
    
    $("#oppGestionnaires, #oppFournisseurs").chosen({disable_search_threshold: 10});
    $("#oppGestionnaires_chosen, #oppFournisseurs_chosen").css('width', '100%');
})
