// demande à ce que la fonction main() soit exécutée dès la fin du chargement de la page
window.addEventListener("load", main);
/////////////////////////////////////////////////////////////////////////////////////////
///////////////////// Fin du programme principal ////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////
// fonction principale, essentiellement des abonnements //
//////////////////////////////////////////////////////////
var chatBox; // zone de saisie 
function main() {
  // abonnements de la fonction de déconnexion 
  window.addEventListener("beforeunload",confirmation);
  window.addEventListener("unload",deconnexion);

  // abonnement de la zone de saisie
  chatBox=document.getElementById("chatbox");
  chatBox.addEventListener("change", saisieMessage);

  // essai
  displayMessage("père Noël", "Joyeux Noël à tous!");
}

/////////////////////////////////////////////////
// envoie le message au serveur pour diffusion //
/////////////////////////////////////////////////
function saisieMessage() {
  // on n'envoie pas un message vide
  if (chatBox.value.length==0) return;

  // envoi du message sous la forme d'un POST de variable 'msgBody'
  var xhr = new XMLHttpRequest();
  xhr.open("POST","chat_sendMessage.php",true); 
  xhr.setRequestHeader("Content-Type","multipart/form-data; boundary=BoUnDaRy");
  var body='--BoUnDaRy\n';
  body+='Content-Disposition: form-data; name="msgBody"\n';
  body+='Content-Type: text/plain; charset=utf-8\n\n';
  body+=chatBox.value+'\n';
  body+='--BoUnDaRy\n';
  xhr.send(body);

  // efface le texte précédent
  chatBox.value="";
}

////////////////////////////////////////////////////////
// ajoute un message à la liste des messages affichés //
////////////////////////////////////////////////////////
function displayMessage(auteur, message) {
  // création du div approprié
  var div=document.createElement("div");
  div.setAttribute("class", "chatmessage");
  div.innerHTML=[
    '<span class="chatauteur">',auteur,'</span>',
    '<span class="chatmessagebody">',message,'</span>' 
   ].join('');
  // insertion du div juste avant chatBox
  chatBox.parentNode.insertBefore(div,chatBox);
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
