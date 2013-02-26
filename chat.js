// demande à ce que la fonction main() soit exécutée dès la fin du chargement de la page
window.addEventListener("load", main);
/////////////////////////////////////////////////////////////////////////////////////////
///////////////////// Fin du programme principal ////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////
// fonction principale, essentiellement des abonnements //
//////////////////////////////////////////////////////////
function main() {
  // abonnements de la fonction de déconnexion 
  window.addEventListener("beforeunload",confirmation);
  window.addEventListener("unload",deconnexion);
}

//////////////////////////////////////////////////////////////////////////
// regarde si les messages doivent être effacés et demande confirmation //
//////////////////////////////////////////////////////////////////////////
// détermine la valeur de doClear, utilisée par deconnexion()
var doClear; // variable globale
function confirmation(evt) {
  // récupère le nombre de connectés et de messages
  var nums=ajax("chat_count.php").split(" ");
  // nums[0] contient le nombre de connectés restants
  // nums[1] contient le nombre de messages de la session
  if (nums[0]==1 && nums[1]>0) { // on va effacer les messages, demandons confirmation
    doClear=true;
    var evt = evt || window.event;
    evt.returnValue = "Vous êtes le dernier à partir, les messages vont être effacés.";
  }
  else doClear=false;
}

/////////////////////////////////////////////////////////////////////////////
// déconnecte l'utilisateur et éventuellement efface la liste des messages //
/////////////////////////////////////////////////////////////////////////////
// cette fonction est toujours appelée après confirmation(), qui détermine la valeur de doClear
function deconnexion() {
  // déconnexion
  ajax("chat_deconnexion.php");
  // effacement des messages
  if (doClear) ajax("chat_clear.php");
}

///////////////////////////////////////////////////////////////
// appel d'un script AJAX et renvoi de la réponse éventuelle //
///////////////////////////////////////////////////////////////
function ajax(scriptName) {
  var xhr=new XMLHttpRequest();
  xhr.open("POST", scriptName, false);
  xhr.send();
  if (xhr.status!=200) alert("Erreur lors de l'appel de "+scriptName);
  return xhr.response;
}
