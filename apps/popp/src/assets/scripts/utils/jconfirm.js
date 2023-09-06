
$.jConfirm.defaults.question = 'Êtes-vous sûr-e?';
$.jConfirm.defaults.confirm_text = 'Oui';
$.jConfirm.defaults.deny_text = 'Non';
$.jConfirm.defaults.theme = 'bootstrap-4';
$.jConfirm.defaults.backdrop = 'black';
$.jConfirm.defaults.position = 'top';

$(function(){
  initJConfirm()
});

export function initJConfirm(){
  $('[data-toggle="confirm"]').jConfirm().on('confirm', 'a.remove', function(e){
    var url = $(this).data('url');
    window.location = url;
  });
}