import { PARAMETRES } from '../custom/parametre';
import {photoSource} from './map';
import Masonry from 'masonry-layout';
import { flashMessage } from '../flashMessage/flashMessage';
import 'jstree';
import 'chosen-js';
import 'chosen-js/chosen.min.css';
import { isEmptyObject } from 'jquery';

export default (function () {
    
    if($("#nbSerie").length == 1){
        $.post(PARAMETRES.url + '/get/init/param'
            ,[]
            ,function(data, status){
                $("#nbSerie").html(data.series.length);
                $("#selectTypo").val(data.typoId);
                $("#selectEnsPay").val(data.ensPayId); 
                $("#selectUniPay").val(data.uniPayId); 
                //filtre par défaut
                $("#selectPay").val(data.payId);
                $("#selectReg").val(data.regId);
            
                $("#selectDep").val(data.depId);
                $("#selectCom").val(data.comId).trigger('chosen:updated');;
                $("#selectAxeThe").val(data.axeTheId); 
                $("#SelectIdSer").val(data.serId);
                $("#SelectOpp").val(data.oppId);
                $("#SelectAnn").val(data.ann);
                $("#SelectStructureOpp").val(data.porteurOppId);
                
                $("#dataSearchThesaurus").val(data.dataSearchThesaurus);
                $("#listeSeries").html("");

                //On ajoute les photos liés au critères
                // Sauvegarde les données contact
                
                var dataSend = { 
                    typoId: $("#selectTypo").val(), 
                    ensPayId: $("#selectEnsPay").val(), 
                    uniPayId: $("#selectUniPay").val(), 
                    payId: $("#selectPay").val(), 
                    regId: $("#selectReg").val(), 
                    depId: $("#selectDep").val(), 
                    comId: $("#selectCom").val(), 
                    axeTheId: $("#selectAxeThe").val(), 
                    serId: $("#SelectIdSer").val(), 
                    oppId: $("#SelectOpp").val(), 
                    ann: $("#SelectAnn").val(),
                    thesaurus: $("#dataSearchThesaurus").val(),
                    porteurOppId: $("#SelectStructureOpp").val(),
                    action: 'save'
                };
                $.post(PARAMETRES.url + '/set/critereSearch/series'
                    ,dataSend
                    ,function(data, status){
                    //alert("Data: " + data + "\nStatus: " + status);
                        if (status == "success") {
                            if(PARAMETRES.autoLoadSeries){
                                drawListeSeries(data.tabSeries);
                            }
                            $("#loader").removeClass('fadeIn');
                        } else {
                            alert('Un erreur a été rencontrée');
                            $("#loader").removeClass('fadeIn');
                        }
                });
        });
    }

    $("#messageAccueil")
    .delay(5000)
    .fadeOut(1000);

    $("#selectCom").chosen({
        disable_search_threshold: 10,
        no_results_text: "Aucun résultat trouvé"
    });
    $("#selectCom_chosen").css("width",'auto');

  $('#search').on('click', e => {
    search('save');
  });

  $("#removeSearch").on('click', e => {
    //réinitialisation de la recherche carto
    $("#listClickMapSeriePhoto").html("");

    $("#selectTypo").val('all');
    $("#selectEnsPay").val('all'); 
    $("#selectUniPay").val('all'); 
    //filtre par défaut
    $("#selectPay").val('all');
    $("#selectReg").val('all');

    $("#selectDep").val('all');
    $("#selectCom").val('all').trigger('chosen:updated');
    $("#selectAxeThe").val('all'); 
    $("#SelectIdSer").val('all');
    $("#SelectOpp").val('all');
    $("#SelectAnn").val('all');
    $("#SelectStructureOpp").val('all');
    
    $("#dataSearchThesaurus").val('');
    $("#listeSeries").html("");
    search('remove');

    $("#treeThesaurus").jstree();
    $("#treeThesaurus").jstree().destroy();
  });

    function search(action){
      $("#loader")
      .css('opacity', '0.5')
      .toggleClass('fadeIn fadeOut');
    var dataSend = { 
        typoId: $("#selectTypo").val(), 
        ensPayId: $("#selectEnsPay").val(), 
        uniPayId: $("#selectUniPay").val(), 
        payId: $("#selectPay").val(), 
        regId: $("#selectReg").val(), 
        depId: $("#selectDep").val(), 
        comId: $("#selectCom").val(), 
        axeTheId: $("#selectAxeThe").val(), 
        serId: $("#SelectIdSer").val(), 
        oppId: $("#SelectOpp").val(), 
        ann: $("#SelectAnn").val(),
        thesaurus: $("#dataSearchThesaurus").val(),
        porteurOppId: $("#SelectStructureOpp").val(),
        action: action
    };
    // Sauvegarde les données contact
    $.post(PARAMETRES.url + '/set/critereSearch/series'
        ,dataSend
        ,function(data, status){
        //alert("Data: " + data + "\nStatus: " + status);
            if (status == "success") {
                $("#nbSerie").html(data.nbseries);
                photoSource.refresh();
                //if(action != 'remove'){
                    drawListeSeries(data.tabSeries);
                //}
                $("#loader").toggleClass('fadeIn fadeOut');
            } else {
                alert('Un erreur a été rencontrée lors de la recherche');
                $("#loader").toggleClass('fadeIn fadeOut');
            }
    });
  }
  

    function drawListeSeries(tabSeries){
        //réinitialisation de la recherche carto
        $("#listClickMapSeriePhoto").html("");
        $("#listeSeries").html("");
        var div1_1 = $("<div>", {
            class:'row'
        })
        var div2 = $("<div>", {
            class:'masonry-item col-md-12'
        }).appendTo(div1_1)
        var div3 = $("<div>", {
            class:'bgc-white p-10 bd'
        }).appendTo(div2)
        var div4 = $("<div>", {
            class:'row'
        }).appendTo(div3)
        var div5_1 = $("<div>", {
            class:'col-md-6'
        }).append("<h2>Résultat de la recherche</h2>")
        .appendTo(div4)
        var div5_2 = $("<div>", {
            class:'col-md-6 m-auto text-center'
        }).appendTo(div4)
        var buttonImporter = $("<button>", {
            class:'js-action-addallbasket btn btn-primary cur-p',
            name:'importerSerie',
            value:'importerSerie'
        }).html($("#listeSeries").data("label-export-all-series"))
        .appendTo(div5_2)
        var div1_2 = $("<div>", {
            id:'js-message'
        })

        $("#listeSeries")
        .append(div1_1)
        .append(div1_2)

        /*$("#listeSeries").html("<div class='row'><div class='masonry-item col-md-12'><div class='bgc-white p-10 bd'>
        <div class='row'><div class='col-md-6'><h2>Résultat de la recherche :</h2></div>
        <div class='col-md-6 m-auto text-center'><button name='importerSerie' class='js-action-addallbasket btn btn-primary cur-p' value='importerSerie'>Ajouter toute les séries</button></div></div></div></div></div><div id='js-message'></div>");
        */
        tabSeries.forEach(function(serie, index){
            var classOpp;
            if (serie.opp == 'local'){
                classOpp = "bgc-white";
            }else{
                classOpp = "bgc-light-grey-100";
            }
            var divGrandPere = $('<div />',{
                class: 'row',
                style: 'width: 100%'
            })

            var divPere = $('<div />',{
                class: 'masonry-item col-md-11 cur-p btn-outline-success',
                style: 'width: 90%'
            }).on("click", function () {
                location.href = PARAMETRES.url + '/public/get/serie/' + serie.id;
                //$(this).remove();
            })
                
            var divMere = $('<div />', { 
                class: 'p-10 bd ' + classOpp,
                
            }).appendTo(divPere);

            divMere.append('<div style="display:flex"><h6 class="c-grey-900">' + serie.titre + '</h6><p class="mL-10"> #OPP ' + serie.opp +'</p></div>');
            var divFille = $('<div />', { 
                class: 'mT-10'
            }).appendTo(divMere);
            var divPhotos = $('<div />', { 
                class: 'peers fxw-nw@lg+ ta-c gap-10',
                height: '110px'
            }).appendTo(divFille);
            for (let i = 0; i < serie.photos.length; i++){
                var img = $('<img />', { 
                    id: serie.photos[i].id,
                    src: serie.photos[i].url,
                    alt: serie.photos[i].titre
                });
                var divPhoto = $('<div />', { 
                    class: 'peer'
                }).append(img);

                divPhoto.append('<div class="c-grey-900">' + serie.photos[i].date + '</div>');
                    
                $(divPhoto).appendTo(divPhotos);
            }
            
            var divButton = $('<div />', {
                class: 'masonry-item col-md-1 mt-auto mb-auto',
            });
            $("<button>", {
                name:'importerSelection',
                'data-id':serie.id,
                class:"js-action-addbasket cur-p",
                'data-toggle':"tooltip", 
                'data-placement':"top", 
                title:"Ajouter au panier",
                'data-trigger':'hover'
            }).tooltip()
            .html("<i class='ti-plus'></i>")
            .appendTo(divButton)
            
            //divButton.append('<button name="importerSelection" data-id="'+serie.id+'" class="js-action-addbasket cur-p" value="importerSelection"><i class="ti-plus"></i></button>');

            var d = document.createElement('div');
            
            $(d).addClass('row')
                .append(divPere)
                .append(divButton)
                .appendTo($("#listeSeries")) //main div
                
                /*.hide()
                .slideToggle(300)
                .delay(50);*/
        })
    
        new Masonry('.masonry', {
            itemSelector: '.masonry-item',
            columnWidth: '.masonry-sizer',
            percentPosition: true,
        });
  
        $(".js-action-addbasket").on("click", function(){
            var id = $(this).data("id");

            var tabId = [];
            tabId.push(id);
            var url = PARAMETRES.url + '/public/add/panier';

            $.ajax({
                url: url,
                type:"POST",
                data: {
                    tabId : tabId,
                }  
            })
            .done(function( data, message ) {
                if(data.status == "ok"){
                    flashMessage("success", "Succès", $("#listeSeries").data("label-success-add-serie"), 1000);
                }else{
                    flashMessage("success", "Succès", $("#listeSeries").data("label-already-added-serie"), 1000);
                }
            })
    
            .fail(function(data, message) {
                //si pas OK
                if (data.status){
                     flashMessage("danger", "Error", "Ajout au panier échoué", 1000);
                }
                   
            })  
        });

        $(".js-action-addallbasket").on("click", function(){

            var tabId = [];

            tabSeries.map(serie => {
                var id = serie.id;
                tabId.push(id);
            })

            
            var url = PARAMETRES.url + '/public/add/panier';


            $.ajax({
                url: url,
                type:"POST",
                data: {
                    
                    tabId : tabId,
                }
            })
            .done(function( data, message ) {
            
                if(data.status == "ok"){
                    flashMessage("success", "Succès", $("#listeSeries").data("label-success-add-series"));
                } 
            })
    
            .fail(function(data, message) {
                //si pas OK
                if (data.status){
                     flashMessage("danger", "Erreur", "Erreur lors de l'ajout dans le panier");
                }
                   
            })  
        });

      //$("#listeSeries").append();
    };
//<a class="jstree-anchor" href="#"><i class="jstree-icon jstree-themeicon-hidden"></i>Loading ...</a>
    $("#btnThesaurus").click(function(){
        $("#thesaurusModal").modal('toggle');
        
        $("#treeThesaurus")
            .bind('loaded.jstree', function(e, d) {
                var tabThesaurus = $("#dataSearchThesaurus").val().split(',');
                for(var i = 0; i < tabThesaurus.length; i++){
                    $("#treeThesaurus").jstree(true).select_node(tabThesaurus[i]);
                }

                var $tree = $(this);
                $($tree.jstree().get_json('#', {
                    flat: true
                  }))
                  .each(function(index, value) {
                    var node = $("#treeThesaurus").jstree().get_node(this.id);
                    var lvl = node.parents.length;
                    if(lvl < 3){
                        $("#treeThesaurus").jstree(true).disable_node(node);
                    }
                  });

                /*$("#dataSearchThesaurus").jstree().get_json($("#dataSearchThesaurus").jstree(), {
                    flat: true
                  })
                  .each(function(index, value) {
                    var node = $("#dataSearchThesaurus").jstree().get_node(this.id);
                    var lvl = node.parents.length;
                    if(lvl < 3){
                        $("#treeThesaurus").jstree(true).disable_node(data.node);
                    }
                  });*/
            })
            /*.bind("select_node.jstree", function(event, data){
                if(data.node.parents.length < 3 ){
                    $("#treeThesaurus").jstree(true).disable_node(data.node);
                    $('#treeThesaurus').jstree(true).deselect_node(data.node);                    
                    $('#treeThesaurus').jstree(true).toggle_node(data.node);  
                    return false;
                    var jstree = $("#treeThesaurus");
                    $("#treeThesaurus").jstree(true).deselect_node(data.node);
                }*/
                /*if(selected.node.parents.length >= 3 ){
                    var parentNode = $("#treeThesaurus").jstree(true).get_parent(selected.node.id);
                    var siblingNodes = $("#treeThesaurus").jstree(true).get_children_dom(parentNode);
                    var allChecked = true;
                    $(siblingNodes).each(function () {
                        if (!$(this).children('.jstree-anchor').hasClass('jstree-clicked')) allChecked = false;
                    });
                    if (allChecked) {
                        $(siblingNodes).each(function () {
                            $("#treeThesaurus").jstree(true).deselect_node(this);
                        });
                        $("#treeThesaurus").jstree(true).select_node(parentNode);
                    }
                }else{
                    $("#treeThesaurus").jstree(true).deselect_node(node);
                }*/
            //})
            .jstree({
            'core' : {
                'data' : {
                    'url' : PARAMETRES.url + '/get/tree/thesaurusexistant',
                    "dataType" : "json"
                },
                "themes":{
                    "icons":false,
                    "dots" : false,
                }
            },
            //"checkbox" : {three_state: false},
            //"checkbox" : {"keep_selected_style" : false, three_state: false},
            "plugins": ["checkbox"],
            "languages": ['fr']
        });

    })

    $("#searchThesaurus").click(function(){
        //Récupération des thesaurus du treeView
        var dataSearchThesaurus = [];
        var tabSearchThesaurus = $("#treeThesaurus").jstree("get_selected", true);
        tabSearchThesaurus.forEach(function(item) {
            if(item.id.includes('_')){
                dataSearchThesaurus.push(item.id);
            }
        });
        $("#dataSearchThesaurus").val(dataSearchThesaurus.join(","));
        $("#thesaurusModal").modal('toggle');
    })

    $(".cleanThesaurus").click(function(){
        $("#treeThesaurus").jstree();
        $("#treeThesaurus").jstree().destroy();
        $("#dataSearchThesaurus").val('');
    })

    
    if(PARAMETRES.linkSelectCommune){
        $("#selectDep").on( 'change', (elem) => {
            $( "#selectDep option:selected" ).each(function() {
                var dep_code = $( this ).data('code');
                var updateSelectedValue = $('#selectCom option:selected').val();
                if($('#selectCom option:selected').text().substring(0,2) != dep_code ){
                    updateSelectedValue = 'all';
                }
                $('#selectCom option').each(function() {
                    if ($( this ).text().substring(0,2) == dep_code || !dep_code){
                        $(this).show();
                        /*if (updateSelectedValue == null){
                            updateSelectedValue = $(this).val();
                        }*/
                    }else{
                        $(this).hide();
                    }
                });
                $('#selectCom').val(updateSelectedValue);
                $("#selectCom").trigger("chosen:updated");
            })
        })
    }

}());