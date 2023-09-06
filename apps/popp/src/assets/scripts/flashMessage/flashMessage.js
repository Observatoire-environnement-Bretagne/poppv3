export function flashMessage(type, strong, message, timeout=null){
  var elementStrong = $('<strong>').html(strong);
  var elementClose = $('<button>', {
    type:"button",
    class:"close",
  })
    
    .attr('aria-label', 'close')
    .attr('data-dismiss', 'alert')

  //créer un élément span
  var elementSpan = $('<span>')
    //ajout d'un attribut
    .attr('aria-hidden', 'true')
    //insérer un contenu à l'élément span
    .append("&times;")
    //insérer elementSpan dans le bouton (elementClose)
    .appendTo(elementClose)
 
  
  var divAlert = $('<div>',  {
    class:"alert alert-dismissible alert-" + type + " show fade",
    role: "alert",
    
  })
  .append(elementStrong)
  .append(" " + message)
  .append(elementClose)
  .appendTo('#js-message');

  if(timeout){
    setTimeout(function(){
      divAlert.fadeOut()
    }, timeout)
  }
}