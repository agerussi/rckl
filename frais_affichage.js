window.addEventListener("load", main);
/////////////////////////////////////////////////////

// annule une dépense
function doCancel() {
  if (confirm("Êtes-vous sûr(e) ?")) window.location.replace("frais_annulation.php?id="+this.id);
}

// réfute ou accepte une dépense
function doAuth() {
  window.location.replace("frais_auth.php?id="+this.id);
}

function main() {
  // abonnements des boutons d'annulation des frais
  var cancelIcons=document.getElementsByName("cancelIcon");
  for (var i=0; i<cancelIcons.length; i++) cancelIcons[i].addEventListener("click",doCancel);
  // abonnements des boutons d'auth
  var authIcons=document.getElementsByName("authIcon");
  for (var i=0; i<authIcons.length; i++) authIcons[i].addEventListener("click",doAuth);
}
