// demande à ce que la fonction main() soit exécutée dès la fin du chargement de la page
window.addEventListener("load", main);
/////////////////////////////////////////////////////////////////////////////////////////
///////////////////// Fin du programme principal ////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////

// variables globales
var chatBox; // zone de saisie 
var chatPage; // page du chat (hors menu)
var bodyTag; // l'élément <body>
var htmlTag; // l'élément <html>
var mainMenu; // le menu en haut de page
var roomNum; // n° de salon

//////////////////////////////////////////////////////////
// fonction principale, essentiellement des abonnements //
//////////////////////////////////////////////////////////
function main() {
  // récupération des éléments interactifs
  chatBox=document.getElementById("chatbox");
  chatPage=document.getElementById("chatpage"); 
  bodyTag=document.getElementsByTagName("body")[0];
  mainMenu=document.getElementById("mainmenu");
  htmlTag=(document.getElementsByTagName("html"))[0];

  // récupération du n° de salon
  roomNum=(document.getElementsByName("roomNum"))[0].value;

  // abonnements de la gestion de la zone chatpage
  setHeight();
  window.addEventListener("resize",setHeight);

  // abonnement de la zone de saisie
  var isIE = /*@cc_on!@*/false; // comprends rien mais ça à l'air de marcher!!
  // if (isIE) chatBox.addEventListener("keypress", waitForEnter);
  // else chatBox.addEventListener("change", saisieMessage);
  
  chatBox.addEventListener("keypress", waitForEnter);
  
  // abonnement du bouton d'envoi (équivalent à 'enter' mais utile en cas de défaillance)
  document.getElementById("sendButton").addEventListener("click", saisieMessage);

  // abonnement de la collecte des membres
  getMembers();
  window.setInterval(getMembers,10*1000);

  // abonnement de la collecte de messages
  getMessages();
  window.setInterval(getMessages,4*1000);

}

/////////////////////////////////////////////////////////////////////////////////////
// solution pourrie pour contre-carrer les tarres d'IE sur les évènements 'change' //
/////////////////////////////////////////////////////////////////////////////////////
function waitForEnter(evt) {
  //displayMessage("system", "IE!");
  if (evt.keyCode==13) saisieMessage();
}

///////////////////////////////////////////////////////////
// adapte dynamiquement la valeur max-height de chatpage //
///////////////////////////////////////////////////////////
function setHeight() {
  var winHeight=htmlTag.clientHeight;
  var menuHeight=mainMenu.clientHeight;
  chatPage.style.maxHeight=(winHeight-menuHeight-20)+"px";

  //chatBox.style.width=winWidth+"px";
  chatPage.style.width=bodyTag.clientWidth+"px";
}

////////////////////////////////////////////////////////////
// demande au serveur la liste des membres et les affiche //
////////////////////////////////////////////////////////////
function getMembers() {
  var xhr=new XMLHttpRequest();
  xhr.onreadystatechange=function() {
    if (this.readyState==this.DONE && this.status==200) {
      // récupération sous format JSON 
      //if (JSON.parse) var json=JSON.parse(this.response);
      //else var json=eval("("+this.response+")");
      var json=JSON.parse(this.response);
      // traitement et affichage
      clearMembers(); // effacement de l'ancienne liste
      for (var i=0; i<json.memberlist.length; i++) displayMembre(json.memberlist[i].nom);
    }
  }
  xhr.open("POST", "chat_tools.php?cmd=getmbrs&id="+roomNum, true); // asynchrone pour ne pas avoir d'interruptions
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
  // abonnement
  span.addEventListener("click",addMembreAt);
}

///////////////////////////////////////////////////////////////
// ajoute un '@membre' devant le message en cours d'écriture //
///////////////////////////////////////////////////////////////
function addMembreAt() {
  chatBox.value="[@"+this.textContent+"] "+chatBox.value;
}

/////////////////////////////////////////////////////////////
// demande au serveur les nouveaux messages et les affiche //
/////////////////////////////////////////////////////////////
function getMessages() {
  var xhr=new XMLHttpRequest();
  xhr.onreadystatechange=function() {
    if (this.readyState==this.DONE && this.status==200) {
      // récupération sous format JSON
      //if (JSON.parse) var json=JSON.parse(this.response);
      //else var json=eval("("+this.response+")");
      var json=JSON.parse(this.response);
      // traitement
      for (var i=0; i<json.messagelist.length; i++) 
	displayMessage(json.messagelist[i].auteur,unescape(json.messagelist[i].corps));
    }
  }
  xhr.open("POST", "chat_tools.php?cmd=getmsg&id="+roomNum, true); // asynchrone pour ne pas avoir d'interruptions
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
  xhr.open("POST","chat_tools.php?cmd=sendmsg&id="+roomNum,true); 
  xhr.setRequestHeader("Content-Type","multipart/form-data; boundary=BoUnDaRy");
  var body='--BoUnDaRy\n';
  body+='Content-Disposition: form-data; name="msgBody"\n';
  body+='Content-Type: text/plain; charset=utf-8\n\n';
  body+=escape(chatBox.value)+'\n';
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
