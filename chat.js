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
  // abonnements de la gestion de la zone chatpage
  setHeight();
  window.addEventListener("resize",setHeight);

  // abonnements de la fonction de déconnexion 
  window.addEventListener("beforeunload",confirmation);
  window.addEventListener("unload",deconnexion);

  // abonnement de la zone de saisie
  chatBox=document.getElementById("chatbox");
  chatBox.addEventListener("change", saisieMessage);

  // abonnement de la collecte de messages
  window.setInterval(getMessages,5*1000);

  // abonnement de la collecte des membres
  window.setInterval(getMembers,10*1000);

  // lance immédiatement le rafraîchissement
  getMembers();
  getMessages();
}

function setHeight() {
  var html=(document.getElementsByTagName("html"))[0];
  var winHeight=html.clientHeight;
  var menu=document.getElementById("mainmenu");
  var menuHeight=menu.clientHeight;
  //displayMessage("system",html.clientHeight+"/"+html.scrollHeight+"/"+html.offsetHeight+"/"+html.height);
  var chatpage=document.getElementById("chatpage"); 
  chatpage.style.maxHeight=(winHeight-menuHeight-20)+"px";
}

////////////////////////////////////////////////////////////
// demande au serveur la liste des membres et les affiche //
////////////////////////////////////////////////////////////
function getMembers() {
  var xhr=new XMLHttpRequest();
  xhr.onreadystatechange=function() {
    if (this.readyState==this.DONE && this.status==200) {
      // récupération sous format texte XML 
      var listeMembresXML=this.response;
      // traitement et affichage
      clearMembers(); // effacement de l'ancienne liste
      var memberlist=new XML(listeMembresXML);
      for (var i=0; i<memberlist.member.length(); i++) displayMembre(memberlist.member[i].nom);
    }
  }
  xhr.open("POST", "chat_getMembers.php", true); // asynchrone pour ne pas avoir d'interruptions
  xhr.send();
}

///////////////////////////////////////////////
// efface les membres de la liste de membres //
///////////////////////////////////////////////
function clearMembers() {
  var chatmembers=document.getElementsByName("chatmember");
  for (var i=chatmembers.length-1; i>=0; i--) chatmembers[i].parentNode.removeChild(chatmembers[i]);
}

/////////////////////////////////////////////
// ajoute un membre à la liste des membres //
/////////////////////////////////////////////
function displayMembre(nom) {
  // création du span approprié
  var span=document.createElement("span");
  span.setAttribute("class", "chatmember");
  span.setAttribute("name", "chatmember");
  span.textContent=nom;
  // insertion du span à la fin de la liste
  document.getElementById("chatmembers").appendChild(span);
}

/////////////////////////////////////////////////////////////
// demande au serveur les nouveaux messages et les affiche //
/////////////////////////////////////////////////////////////
function getMessages() {
  var xhr=new XMLHttpRequest();
  xhr.onreadystatechange=function() {
    if (this.readyState==this.DONE && this.status==200) {
      // récupération sous format texte XML 
      var listeMessagesXML=this.response;
      // traitement et affichage
      var messagelist=new XML(listeMessagesXML);
      for (var i=0; i<messagelist.message.length(); i++) displayMessage(messagelist.message[i].auteur,messagelist.message[i].corps);

    }
  }
  xhr.open("POST", "chat_getMessages", true); // asynchrone pour ne pas avoir d'interruptions
  xhr.send();
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

  // appelle le serveur afin que le message soit affiché immédiatement
  //getMessages();
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
    '<span class="chatmessagebody"></span>' 
   ].join('');
  div.lastChild.textContent=message;
  // insertion du div juste avant chatfooter
  var chatfooter=document.getElementById("chatfooter");
  chatfooter.parentNode.insertBefore(div,chatfooter);

  chatfooter.scrollIntoView(false);
}

//////////////////////////////////////////////////////////////////////////
// regarde si les messages doivent être effacés et demande confirmation //
//////////////////////////////////////////////////////////////////////////
// détermine la valeur de doClear, utilisée par deconnexion()
var doClear; // variable globale
function confirmation(evt) {
  //displayMessage("Bomb Flow","Running the stout !");
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
