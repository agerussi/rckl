var beeingUploaded=0; // nombre de médias en cours d'upload

var MediaType = { // enumération pour le type de media
    On: 1, Photo: 2, Video: 4, New: 8
};

// gère le bouton choixMiniature.
// se contente de déclencher le input
function choisirMiniature() {
  var input=this.nextSibling;
  input.addEventListener("change", makeGestionAjoutMiniature(this), false);
  input.click(); // déclenche le input
  // on récupère le fil dans gestionAjoutMiniature si l'utilisateur a sélectionné un fichier
}

// chargement d'une miniature + affichage
// se contente de déclencher la lecture asynchrone du fichier
function makeGestionAjoutMiniature(img) { // img est l'image sur laquelle on a cliqué 
return function(evt) { 
  // récupération du fichier
  var fichier = evt.target.files[0]; // objet File 

  // teste si le fichier est acceptable 
  if (!fichier.type.match('image.*')) {
    window.alert("Le fichier "+fichier.name+" n'est pas un fichier image");
    return;
  }
  if (fichier.size>10*1024)  {  // 10 KB MAX
    window.alert("Le fichier "+fichier.name+" est trop gros pour une miniature!");
    return;
  }
  // TODO: le champ 'value' de l'input n'est pas mis à zéro lorsque le fichier est inacceptable, donc 
  // en théorie il sera uploadé et traité... 
  // mais en pratique l'utilisateur va re-sélectionner un autre fichier miniature.

  // lecture du fichier pour affichage 
  var reader=new FileReader();
  reader.onload = makeAffichageMiniature(img);
  reader.readAsDataURL(fichier); // lecture asynchrone => atterri dans gestionFinLectureMiniature()
}}

function makeAffichageMiniature(img) {
return function(evt) { // evt.target.result contient l'URL 
    var miniature=img.previousSibling;
    miniature.setAttribute("src",evt.target.result);
}}

// annule les modifications de l'archive
// c'est-à-dire efface les éventuels fichiers temporaires uploadés
function gestionAnnulation() {
  if (!uploadFini()) return;
 
  // efface les médias fraîchement uploadés
  var listeTypes=document.getElementsByName("typeMedia"); 
  var listeNoms=document.getElementsByName("nomMedia"); 
  var xmlList="<delete>";
  for (var i=listeTypes.length-1; i>=0; i--) {
    if ((listeTypes[i].value&MediaType.New)!=0) xmlList+="<file>"+listeNoms[i].value+"</file>"; //effaceSurServeur(listeNoms[i].value);
  }
  xmlList+="</delete>";
  // appelle le php qui va effacer les fichiers
  var xhr = new XMLHttpRequest();
  xhr.open("POST","tempMediaDelete.php",false); 
  xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  xhr.send("xml="+xmlList);
  window.back();
}

// teste s'il reste des fichiers en cours d'upload
// et affiche un message
function uploadFini() {
  if (beeingUploaded!=0) {
    var msg=(beeingUploaded==1) ? "Un fichier est " : beeingUploaded+" fichiers sont ";
    window.alert(msg+"en cours d'upload!");
    return false;
  }
  else return true;
}

// media=le fichier à uploader
// mediaNum=le numéro sous lequel le media est enregistré (permet de retrouver les labels)
function uploadAsynchrone(mediaFile, mediaNum) { 
  // create XHR instance
  xhr = new XMLHttpRequest();
  xhr.open("POST", 'media-upload.php', true);

  // affichage de la progression de l'upload
  var eventSource = xhr.upload || xhr;
  eventSource.addEventListener("progress", makeUploadProgressHandler(mediaNum));

  // gestion de la fin de l'upload
  xhr.onreadystatechange = makeOnReadyChangeHandler(xhr,mediaFile,mediaNum); 
   
  // démarrage de l'upload
  beeingUploaded++;
  xhr.send(mediaFile);
}

// affichage de la progression d'un upload
function makeUploadProgressHandler(mediaNum) { // int
return function(evt) {
  var position = evt.position || evt.loaded;
  var total = evt.totalSize || evt.total;
  var percentage = Math.round(100*position/total);

  var progressSpan=document.getElementById("progresMedia"+mediaNum);     
  progressSpan.innerHTML=percentage+"%"; 
}};

// gestion de la fin d'un upload
// en cas de succès, on récupère le nom de fichier temporaire
// en cas d'échec, on élimine le média
function makeOnReadyChangeHandler(xhr,mediaFile,mediaNum) { // XMLHttprequest, File, int
return function() {
  if(xhr.readyState == 4) {
    if(xhr.status == 200) { // succès
      //window.alert(_mediaFile.name+": succès: "+xhr.responseText);
      var progressSpan=document.getElementById("progresMedia"+mediaNum);     
      //progressSpan.innerHTML="upload OK: "+xhr.responseText;
      progressSpan.innerHTML="upload OK";
      var inputNomMedia=document.getElementById("nomMedia"+mediaNum);
      inputNomMedia.value=xhr.responseText;
    } 
    else { // erreur: on élimine le média de la liste
      window.alert(mediaFile.name+": échec de l'upload: xhr.status="+xhr.status);
      var mediaTable = document.getElementById("table"+mediaNum);
      mediaTable.parentNode.removeChild(mediaTable);	
    }
    beeingUploaded--;
  }
}}

var filesToProcess=0;
var numeroMedia=1;

// ajout d'une vidéo
// on utilise la miniature par défaut et on lance l'upload du fichier
function gestionAjoutVideo(fichier) { 
  // création d'une nouvelle vidéo
  var table=document.createElement("table");
  table.setAttribute("id", "table"+numeroMedia);
  table.innerHTML=[
    '<tr><td>',
    '<img src="IMG/video-default-mini.jpg" height="85px" name="miniatureVideo" />',
    '<img title="choisir une miniature" src="FONDS/insert_image.png" name="choisirminiature"/>',
    '<input type="file" style="display:none" name="ajoutMiniature"/>',
    '</td></tr><tr><td>',
    '<img title="supprimer la vidéo" src="FONDS/b_drop.png" name="supprimervideo"/>', 
    '<input type="hidden" name="typeMedia" value="',MediaType.On|MediaType.Video|MediaType.New,'"/>',
    '<img title="éditer le commentaire" src="FONDS/b_edit.png" name="editercommentaire"/>',
    '<input type="hidden" name="commentaireMedia" value=""/>',
    '<input type="hidden" id="nomMedia',numeroMedia,'" name="nomMedia" value=""/>',
    '<span id="progresMedia',numeroMedia,'">chargement...</span>'
   ].join('');
  // insertion de l'image dans la liste
  var input=document.getElementById("listeVideos");
  input.insertBefore(table,null); 
  
  // démarre le chargement du fichier
  uploadAsynchrone(fichier,numeroMedia);
  numeroMedia++;
}

function makeGestionAjoutImage(fichier) { // renvoie la fonction qui va s'occuper du rajout de l'image lorsqu'elle sera chargée
return function(evt) { // ajout d'une image: evt.target.result contient l'URL
  // création d'un nouveau média
  var table=document.createElement("table");
  table.setAttribute("id", "table"+numeroMedia);
  table.innerHTML=[
    '<tr><td>',
    '<img src="', evt.target.result, '" height="85px" name="photo" />',
    '</td></tr><tr><td>',
    '<img title="supprimer la photo" src="FONDS/b_drop.png" name="supprimerphoto"/>', 
    '<input type="hidden" name="typeMedia" value="',MediaType.On|MediaType.Photo|MediaType.New,'"/>',
    '<img title="éditer le commentaire" src="FONDS/b_edit.png" name="editercommentaire"/>',
    '<input type="hidden" name="commentaireMedia" value=""/>',
    '<input type="hidden" id="nomMedia',numeroMedia,'" name="nomMedia" value=""/>',
    '<span id="progresMedia',numeroMedia,'">chargement...</span>'
   ].join('');
  // insertion de l'image dans la liste
  var input=document.getElementById("listePhotos");
  input.insertBefore(table,null); 
  
  // démarre le chargement du fichier
  uploadAsynchrone(fichier,numeroMedia);
  numeroMedia++;

  // gestion des abonnements pour les nouveaux fichiers
  filesToProcess--;
  if (filesToProcess==0) abonnementsPhotos(); // pour l'ensemble des nouvelles images
}}

function gestionAjoutFichiers(evt) { // gère tous les ajouts de fichiers (photo + vidéo)
  var listeFichiers = evt.target.files; // FileList object
  var fichier;

  for (var i=0; fichier=listeFichiers[i]; i++) { // traitement individuel de chaque fichier
    if (fichier.type.match('image.*')) {
      var reader = new FileReader();
      reader.onload = makeGestionAjoutImage(fichier);
      filesToProcess++;
      reader.readAsDataURL(fichier); // lecture asynchrone => atterri dans gestionAjoutImage()
      continue;
    }
    if (fichier.type.match('video.*')) { 
      gestionAjoutVideo(fichier);
      continue;
    } 
    // arrivé ici, le fichier n'a pas été traité
    window.alert("Le fichier '"+fichier.name+"' est d'un type inconnu - non traité");
  }
}

var commentaireCourant; // permet de sauvegarder le pointeur vers le commentaire a modifier pendant l'aller/retour de l'édition
function editerCommentaire(param) { // affiche la zone de saisie
  document.getElementById("inputCommentaire").value = this.nextSibling.value;
  commentaireCourant = this;
  document.getElementById("zoneSaisie").style.display = "inline";
}

function enregistrerCommentaire(change) { // récupère le commentaire et l'attribue à la photo
  document.getElementById("zoneSaisie").style.display = "none";
  if (!change) return; 
  var commentaire=document.getElementById("inputCommentaire").value;
  var input = commentaireCourant.nextSibling;
  input.value = commentaire;
  var imgPhoto = commentaireCourant.parentNode.parentNode.parentNode.firstChild.firstChild.firstChild;
  imgPhoto.title = commentaire; 
}



function supprimerMedia() { // gère la suppression / réhabilitation de photos ou vidéos
  var imgMedia = this.parentNode.parentNode.parentNode.firstChild.firstChild.firstChild;
  var input = this.nextSibling;
  var type=(input.value&MediaType.Photo) ? "la photo":"la vidéo";
  if (input.value&MediaType.On) {
    input.value = input.value&(~MediaType.On);
    this.setAttribute("src","FONDS/b_add.png");
    this.setAttribute("title","rajouter "+type);
    imgMedia.style.opacity = "0.5";
  } else {
    input.value = input.value|MediaType.On;
    this.setAttribute("src","FONDS/b_drop.png");
    this.setAttribute("title","supprimer "+type);
    imgMedia.style.opacity = "1";
  } 
  //window.alert("fin supprimerMedia");
}
  
// TODO: buggy, à remanier: le problème est apparemment que lorsque l'on clone, on perd les infos
// sur les abonnements (et peut-être même sur d'autres attributs ?) 
var switchDeplacement=false, tableauSource, tableauSourceClone;
function deplacementPhoto() { // gestion du déplacement d'une photo (1er ou 2e clic)
 // récupère le noeud "tableau" correspondant à l'image 
 var tableau=this.parentNode.parentNode.parentNode;
 if (switchDeplacement) {
  if (tableau==tableauSource) {
    this.style.border = "0px";
  } else {
    var tableauDestClone=tableau.cloneNode(true); 
    tableauSource.parentNode.replaceChild(tableauDestClone,tableauSource);
    tableau.parentNode.replaceChild(tableauSourceClone,tableau);
    abonnementsPhotos();
  } 
 } else {
   tableauSource=tableau;
   tableauSourceClone = tableauSource.cloneNode(true);
   this.style.border = "5px solid black";
 }
 switchDeplacement = !switchDeplacement;
}

function validationArchive() { // vérification et préparation avant soumission de l'archive
  return false; // TEMP!
  if (!uploadFini()) return false;
  // la date
  if (document.getElementById("valeurdate").value.length==0) {
    window.alert("La date n'est pas définie!");
    return false;
  }

  // récolte des participants dans listeparticipants
  var listexml="";
  var liste=document.getElementsByName("participant");
  for (var i=0; i<liste.length; i++) listexml += "<nom>"+liste[i].firstChild.data+"</nom>";

  document.getElementById("listeparticipants").value = listexml;  

  // les photos
  // nomme les différentes zones input dans l'ordre
  var listeTypes=document.getElementsByName("typeMedia"); 
  var listeCommentaires=document.getElementsByName("commentaireMedia"); 
  var listeNoms=document.getElementsByName("nomMedia"); 
  for (var i=listeTypes.length-1; i>=0; i--) {
    listeTypes[i].setAttribute("name","typeMedia"+i);
    listeCommentaires[i].setAttribute("name","commentaireMedia"+i);
    listeNoms[i].setAttribute("name","nomMedia"+i);
  }

  return true;
}

// main() est appelée lorsque la page est chargée
window.addEventListener("load",main);
function main() {
  initGestionDate();
  initGestionParticipants();
  abonnementsPhotos();
  abonnementsVideos();
  // gestion du bouton d'ajout de fichiers
  document.getElementById("ajoutFichiers").addEventListener("change", gestionAjoutFichiers, false);
  // annulation des modifs
  document.getElementById("cancel").addEventListener("click", gestionAnnulation, false);
}

function abonnementsVideos() { // abonnements aux diverses fonctions
  var listeVideos = document.getElementsByName("video");
  var i=0;
  var listeSupprimerVideos = document.getElementsByName("supprimervideo");
  while (listeSupprimerVideos[i]) listeSupprimerVideos[i++].addEventListener("click", supprimerMedia);
  i=0;
  var listeEditerCommentaires = document.getElementsByName("editercommentaire");
  while (listeEditerCommentaires[i]) listeEditerCommentaires[i++].addEventListener("click", editerCommentaire);
  i=0;
  var listeChoisirMiniatures = document.getElementsByName("choisirminiature");
  while (listeChoisirMiniatures[i]) listeChoisirMiniatures[i++].addEventListener("click", choisirMiniature);
}

function abonnementsPhotos() { // abonnements aux diverses fonctions
  var listePhotos = document.getElementsByName("photo");
  var i=0;
  while (listePhotos[i]) listePhotos[i++].addEventListener("click", deplacementPhoto);
  var listeSupprimerPhotos = document.getElementsByName("supprimerphoto");
  i=0;
  while (listeSupprimerPhotos[i]) listeSupprimerPhotos[i++].addEventListener("click", supprimerMedia);
  var listeEditerCommentaires = document.getElementsByName("editercommentaire");
  i=0;
  while (listeEditerCommentaires[i]) listeEditerCommentaires[i++].addEventListener("click", editerCommentaire);
}

function initGestionDate() { // règle les paramètres du chooser de date
  new JsDatePick({
	  useMode:2,
	  target:"valeurdate",
	  dateFormat:"%d-%m-%Y"
  });
}

function initGestionParticipants() { // déclenche les annulations et l'ajout
  // ajout
  document.getElementById("ajouterparticipant").addEventListener("click", ajouterParticipant);
  document.getElementById("nouveauparticipant").addEventListener("keyup", suggestionParticipant);
  // annulations
  var listeSupprimer = document.getElementsByName("supprimerparticipant");
  var i=0;
  while (listeSupprimer[i]) listeSupprimer[i++].addEventListener("click", supprimerParticipant);
}

function supprimerParticipant() { // supprime un participant de la liste
  var span=this.parentNode;
  span.parentNode.removeChild(span);
}

function ajouterParticipant() { // ajoute un participant de la liste
 // récupère le nom
 var input=document.getElementById("nouveauparticipant");
 var nom=input.value;
 if (nom.length==0) return;
 input.value=""; // efface le nom
 effaceSuggestions();

 // crée le nom
 var span=document.createElement("span");
 span.setAttribute("class","participant");
 span.setAttribute("name","participant");
 var croix=document.createElement("img");
 croix.src="FONDS/b_drop.png";
 croix.title="supprimer ce participant";
 croix.addEventListener("click", supprimerParticipant);

 span.appendChild(document.createTextNode(nom));
 span.appendChild(croix);
 this.parentNode.insertBefore(span,input);
}

function suggestionParticipant() { // affiche des suggestions sélectionnables à partir du nom
 var nom=this.value.toLowerCase();
 effaceSuggestions();
 if (nom.length==0) return;

 // parcours le tableau des noms à la recherche de suggestions
 for (var i=0; i<suggestions.length; i++) {
   var sugg=suggestions[i].toLowerCase();
   if (sugg.indexOf(nom)==0) ajouteSuggestion(suggestions[i]);
 }
}

function ajouteSuggestion(nom) { // ajoute le nom comme suggestion
 var affichage=document.getElementById("suggestions");
 
 var suggestion=document.createElement("span");
 suggestion.setAttribute("name","suggestionParticipant");
 suggestion.appendChild(document.createTextNode(nom));
 suggestion.addEventListener("click",ecritSuggestion);
 affichage.appendChild(suggestion);
}

function effaceSuggestions() { // clair !
 var liste=document.getElementsByName("suggestionParticipant");
 for (var i=liste.length-1; i>=0; i--) liste[i].parentNode.removeChild(liste[i]);
}

function ecritSuggestion() { // réagit lorsqu'on a cliqué sur une suggestion
  document.getElementById("nouveauparticipant").value = this.innerHTML;
}
