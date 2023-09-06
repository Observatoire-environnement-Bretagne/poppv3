import { PARAMETRES } from '../custom/parametre';

import {jstree} from 'jstree';

$(function() {
    $('#treeThesaurusFac').jstree({
        "core" : {
            "animation" : 0,
            "check_callback" : true,
            "themes" : { "stripes" : true },
            'data' : {
                'url' : PARAMETRES.url + '/get/tree/thesaurusFacultatif',
                "dataType" : "json"
            }
        },
        "types" : {
            "#" : {
                "max_children" : 1,
                "max_depth" : 4,
                "valid_children" : ["root"]
            },
            "root" : {
                "icon" : 'ti ti-desktop',
                "valid_children" : ["default"]
            },
            "default" : {
                "icon" : 'ti ti-folder',
                "valid_children" : ["default","file"]
            },
            "file" : {
                "icon" : "ti ti-file",
                "valid_children" : []
            }
        },
        "plugins" : [
            "contextmenu",  "search",
            "state", "types", "wholerow"
        ],
        contextmenu : {
            items : function (node) {
                var items = {
                    createItem: { // The "create" menu item
                        label: "Créer",
                        action: function (data) {
                            var inst = $.jstree.reference(data.reference),
                                obj = inst.get_node(data.reference);
                            inst.create_node(obj, {}, "last", function (new_node) {
                                new_node.data = {file: true};
                                new_node.text= "nouveau thesaurus";
                                if(new_node.parents.length == 4){
                                    new_node.icon = 'ti ti-file';
                                }
                                setTimeout(function () { inst.edit(new_node); },0);
                            });
                        }
                    },
                    renameItem: { // The "create" menu item
                        label: "Renommer",
                        action: function (data) {
                            var inst = $.jstree.reference(data.reference),
                                obj = inst.get_node(data.reference);
                            inst.edit(obj);
                        }
                    },
                    deleteItem: { // The "delete" menu item
                        label: "Supprimer",
                        action: function (data) {
                            var inst = $.jstree.reference(data.reference),
                                obj = inst.get_node(data.reference);
                            if(inst.is_selected(obj)) {
                                inst.delete_node(inst.get_selected());
                            }
                            else {
                                inst.delete_node(obj);
                            }
                        }
                    }
                }

                if(node.parents.length == 4){
                    delete items.createItem;
                }
                if(node.parents.length == 1){
                    delete items.deleteItem;
                    delete items.renameItem;
                }
                return items;
            }
        }
    });

    function checkValideThesaurus(thesaurus){
        var newThesaurus = [];
        var dataThesaurus = {};
        for(var i=0; i<thesaurus.length; i++){
            if(thesaurus[i].parent != "#"){
                //si on est dans les thésaurus de base
                if(thesaurus[i].parent == "0"){
                    dataThesaurus = {'id': thesaurus[i].id, tabThesaurus: []}
                    newThesaurus.push(dataThesaurus);
                }
                //sinon On recherche dans le nouveau tableau thésaurus
                else{
                    for(var j=0; j<newThesaurus.length; j++){
                        if (newThesaurus[j].id == thesaurus[i].parent){
                            dataThesaurus = {'id': thesaurus[i].id, tabThesaurus: []}
                            newThesaurus[j].tabThesaurus.push(dataThesaurus);
                        }
                        for(var k=0; k<newThesaurus[j].tabThesaurus.length; k++){
                            if (newThesaurus[j].tabThesaurus[k].id == thesaurus[i].parent){
                                dataThesaurus = {'id': thesaurus[i].id}
                                newThesaurus[j].tabThesaurus[k].tabThesaurus.push(dataThesaurus);
                            }
                        }
                    }
                }
            }
        }
        var tabDataThesaurusValid = true;
        for(var i=0; i<newThesaurus.length; i++){
            if(newThesaurus[i].tabThesaurus.length == 0){
                tabDataThesaurusValid = false;
            }
            for(var j=0; j<newThesaurus[i].tabThesaurus.length; j++){
                if(newThesaurus[i].tabThesaurus[j].tabThesaurus.length == 0){
                    tabDataThesaurusValid = false;
                }
            }
        }
        return tabDataThesaurusValid;
    }

    $("#saveThesaurusFacultatif").click(function(){
        var dataThesaurus = $('#treeThesaurusFac').jstree(true).get_json('#', {flat:true})
        dataThesaurus = dataThesaurus.filter(function(item) {
            delete item.state;
            delete item.icon;
            delete item.li_attr;
            delete item.a_attr;
            return item ; 
        });
        if (!checkValideThesaurus(dataThesaurus)){
            $("#messageConfirmThesaurus")
                .addClass('alert alert-danger ta-c w-100')
                .html("L'arbre du thésaurus doit contenir 3 niveaux par branche")
                .fadeIn(500);
            return ;
        }
        //var mytext = JSON.stringify(v);
        $.ajax({
            url : PARAMETRES.url + '/gestion/thesaurusFacultatif/save',
            type : 'POST',
            cache: true,
            data : {'tree': dataThesaurus},
            success: function (data) {
                if(data.status == 'ok'){
                    $("#messageConfirmThesaurus")
                        .removeClass('alert-danger')
                        .addClass('alert alert-success ta-c w-100')
                        .html("Le thésaurus a été enregistrée.")
                        .fadeIn(1000)
                        .delay(2000)
                        .fadeOut(1000);
                }else if(data.status == 'erreur'){
                    $("#messageConfirmThesaurus")
                        .addClass('alert alert-danger ta-c w-100')
                        .html(data.message)
                        .fadeIn(500);
                }
            },
            error : function (){
                $("#messageConfirmThesaurus")
                    .addClass('alert alert-danger ta-c w-100')
                    .html("Une erreur a été rencontrée lors de l'enregistrement.")
                    .fadeIn(500);
            }
        });
        //console.log(mytext);
    });
})