// demande à ce que la fonction main() soit exécutée dès la fin du chargement de la page
window.addEventListener("load", main);
/////////////////////////////////////////////////////////////////////////////////////////
///////////////////// Fin du programme principal ////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////

// fonction exécutée dès que la page est prête
function main() {
  // abonnement de la fonction de déconnexion 
  window.addEventListener("beforeunload",deconnexion);
}


// signale au serveur que l'on se déconnecte
function deconnexion() {
  var xhr=new XMLHttpRequest();
  xhr.open("POST", "chat_deconnexion.php", false);
  xhr.send();
  if (xhr.status!=200) {
    alert("Erreur de déconnexion");
  }
}
