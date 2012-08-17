// constantes et variables globales
var beeingUploaded=0; // nombre de médias en cours d'upload
var uploadLIMIT=2; // nombre maximal de connexions simultanées
var chunkSize=256*1024; // 256 KB
var MediaType = { // enumération pour le type de media
  On: 1, Photo: 2, Video: 4, New: 8, Miniature: 16
};

// gère le bouton choixMiniature.
// se contente de déclencher le input
function choisirMiniature() {
  var input=this.nextSibling.nextSibling;
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

  // signale que la miniature a changé
  var typeMedia=domMove(img,"PPnnccn");
  typeMedia.value = typeMedia.value|MediaType.Miniature;

  // lecture du fichier pour affichage 
  var reader=new FileReader();
  reader.onload = makeAffichageMiniature(img);
  reader.readAsDataURL(fichier); // lecture asynchrone => atterri dans AffichageMiniature
}}

// se déplace dans l'arbre DOM et renvoie le noeud correspondant
// p=previousSibling, P=parentNode, n=nextSibling, c=child
function domMove(noeud, deplacements) { 
  for (var i=0; i<deplacements.length; i++) {
    switch (deplacements.charAt(i)) {
      case "p": noeud=noeud.previousSibling; break;
      case "P": noeud=noeud.parentNode; break;
      case "n": noeud=noeud.nextSibling; break;
      case "c": noeud=noeud.firstChild; break;
    }
  }
  return noeud;
}

function makeAffichageMiniature(img) {
return function(evt) { // evt.target.result contient l'URL 
    var miniature=domMove(img,"p");
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
    if ((listeTypes[i].value&MediaType.New)!=0) xmlList+="<file>"+nomDuFichier(listeNoms[i].value)+"</file>"; 
  }
  xmlList+="</delete>";
  // appelle le php qui va effacer les fichiers
  var xhr = new XMLHttpRequest();
  xhr.open("POST","tempMediaDelete.php",false); 
  xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  xhr.send("xml="+xmlList);
  window.back();
}

// récupère la partie "nom" dans une chaine de type "ext/nom"
function nomDuFichier(nom) {
  var n=nom.indexOf("/");
  return nom.substr(n+1);
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

// supprime un média de l'affichage à partir de son numéro
function supprimeMedia(mediaNum) {
  var mediaTable = document.getElementById("table"+mediaNum);
  mediaTable.parentNode.removeChild(mediaTable);	
}

// media=le fichier à uploader
// mediaNum=le numéro sous lequel le media est enregistré (permet de retrouver les labels)
// obtient un nom pour le media puis découpe le media en packet qui seront reconstitués sur le serveur à la fin
function uploadAsynchroneByChunks(mediaFile, mediaNum) { 
  if (beeingUploaded==uploadLIMIT) { // il faut retarder l'upload
    setTimeout(function(){uploadAsynchroneByChunks(mediaFile,mediaNum);},5*1000);
    return;
  }

  var fileName=getMediaName();
 
  beeingUploaded++;
  var xhr=new XMLHttpRequest(); 
  makeUploadByChunk(xhr,mediaFile,mediaNum,fileName,1)(null);
}

function getMediaName() {
  var name="tmp/file-";
  for (var i=0; i<6; i++) name+=Math.floor(10*Math.random());
  return name;
}

function deleteUploadedChunks(baseName) {
  var xhr=new XMLHttpRequest();
  xhr.open("POST", "chunkUpload.php?cmd=delete&name="+baseName, false);
  xhr.send();
  return (xhr.status==200);
}

function mergeChunks(baseName) {
  var xhr=new XMLHttpRequest();
  xhr.open("POST", "chunkUpload.php?cmd=merge&name="+baseName, false);
  xhr.send();
  return (xhr.status==200);
}

function makeUploadByChunk(xhr,mediaFile, mediaNum, serverFileName, numChunk) {
return function(evt) {
  if (xhr.readyState==0 || (xhr.readyState==4 && xhr.status==200)) { // il faut commencer ou continuer l'upload
    if (mediaFile.size==0) { // il faut reconstituer le fichier à partir de ses bouts
      var affichage=document.getElementById("progresMedia"+mediaNum);     
      affichage.innerHTML="merging chunks..."
      if (mergeChunks(serverFileName)) { // succès
        var inputNomMedia=document.getElementById("nomMedia"+mediaNum);
        inputNomMedia.value+=serverFileName;
        affichage.innerHTML="upload OK";
      }
      else { // échec
	window.alert(mediaFile.name+": échec lors du réassemblage: xhr.status="+newxhr.status);
        supprimeMedia(mediaNum);
        deleteUploadedChunks(serverFileName);
      }
      beeingUploaded--;
      return;
    }

    var chunk=mediaFile.mozSlice(0,chunkSize);
    var rest=mediaFile.mozSlice(chunkSize);

    // déclenche l'upload de chunk
    var newxhr=new XMLHttpRequest();
    //newxhr.open("POST", "uploadChunk.php?chunkname="+serverFileName+numChunk, true);
    newxhr.open("POST", "chunkUpload.php?cmd=upload&name="+serverFileName+numChunk, true);

    // affichage de la progression de l'upload 
    var eventSource = newxhr.upload || newxhr;
    eventSource.addEventListener("progress", makeUploadChunkProgressHandler(mediaNum,numChunk)); 

    newxhr.onreadystatechange = makeUploadByChunk(newxhr,rest,mediaNum,serverFileName,numChunk+1); 
    newxhr.send(chunk);
    return;
  }

  if (xhr.readyState==4 && xhr.status!=200) { // erreur dans l'envoi de chunk, on annule tout
    window.alert(mediaFile.name+": échec de l'upload d'un chunk: xhr.status="+xhr.status);
    supprimeMedia(mediaNum);
    deleteUploadedChunks(serverFileName);
    beeingUploaded--;
    return;
  }
}}

// affichage de la progression d'un upload
function makeUploadChunkProgressHandler(mediaNum,chunkNum) { // int, int
return function(evt) {
  var position = evt.position || evt.loaded;
  var total = evt.totalSize || evt.total;
  var percentage = Math.round(100*position/total);

  var progressSpan=document.getElementById("progresMedia"+mediaNum);     
  progressSpan.innerHTML="chunk n°"+chunkNum+": "+percentage+"%"; 
}}

var filesToProcess=0;
var numeroMedia=1;

// ajout d'une vidéo
// on utilise la miniature par défaut et on lance l'upload du fichier
function gestionAjoutVideo(fichier) { 
  // création d'une nouvelle vidéo
  var table=document.createElement("table");
  table.setAttribute("id", "table"+numeroMedia);
  table.setAttribute("class", "mediaTable");
  table.innerHTML=[
    '<tr><td>',
    '<img src="IMG/video-default-mini.jpg" height="85px" name="miniatureVideo" />',
    '<img title="choisir une miniature" src="FONDS/insert_image.png" name="choisirminiature"/>',
    '<input type="hidden" name="MAX_FILE_SIZE" value="10240" />',
    '<input type="file" style="display:none" name="ajoutMiniature"/>',
    '</td></tr><tr><td>', 
    '<label>#VIMEO</label><input type="text" size="10" name="vimeo" value=""/>',
    '</td></tr><tr><td>', 
    '<img title="supprimer la vidéo" src="FONDS/b_drop.png" name="supprimervideo"/>', 
    '<input type="hidden" name="typeMedia" value="',MediaType.On|MediaType.Video|MediaType.New,'"/>',
    '<img title="éditer le commentaire" src="FONDS/b_edit.png" name="editercommentaire"/>',
    '<input type="hidden" name="commentaireMedia" value=""/>',
    '<input type="hidden" id="nomMedia',numeroMedia,'" name="nomMedia" value="',extension(fichier.name),'/"/>',
    '<span id="progresMedia',numeroMedia,'">chargement...</span>',
    '</td></tr>'
   ].join('');
  // insertion de l'image dans la liste
  var input=document.getElementById("listeVideos");
  input.insertBefore(table,null); 
  abonnementsVideos(); // abonnements aux diverses fonctions
  
  // démarre le chargement du fichier
  uploadAsynchroneByChunks(fichier,numeroMedia++);
}

function extension(nom) { // récupère l'extension à partir du nom de fichier
  var n=nom.lastIndexOf(".");
  return nom.substr(n+1);
}

function makeGestionAjoutImage(fichier) { // renvoie la fonction qui va s'occuper du rajout de l'image lorsqu'elle sera chargée
return function(evt) { // ajout d'une image: evt.target.result contient l'URL
  // création d'un nouveau média
  var table=document.createElement("table");
  table.setAttribute("id", "table"+numeroMedia);
  table.setAttribute("class", "mediaTable");
  table.innerHTML=[
    '<tr><td>',
    '<img src="', evt.target.result, '" height="85px" name="photo" />',
    '</td></tr><tr><td>',
    '<img title="supprimer la photo" src="FONDS/b_drop.png" name="supprimerphoto"/>', 
    '<input type="hidden" name="typeMedia" value="',MediaType.On|MediaType.Photo|MediaType.New,'"/>',
    '<img title="éditer le commentaire" src="FONDS/b_edit.png" name="editercommentaire"/>',
    '<input type="hidden" name="commentaireMedia" value=""/>',
    '<input type="hidden" id="nomMedia',numeroMedia,'" name="nomMedia" value="jpg/"/>',
    '<span id="progresMedia',numeroMedia,'">chargement...</span>'
   ].join('');
  // insertion de l'image dans la liste
  var input=document.getElementById("listePhotos");
  input.insertBefore(table,null); 
  
  // démarre le chargement du fichier
  uploadAsynchroneByChunks(fichier,numeroMedia++);

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
  var imgPhoto = domMove(commentaireCourant,"PPPccc");
  imgPhoto.title = commentaire; 
}



function supprimerMedia() { // gère la suppression / réhabilitation de photos ou vidéos
  var imgMedia = domMove(this,"PPPccc");
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
  // return false; // à décommenter pour empêcher les mises à jour intempestives 
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

  // nomme les différentes zones input dans l'ordre 
  var listeTypes=document.getElementsByName("typeMedia"); 
  var listeCommentaires=document.getElementsByName("commentaireMedia"); 
  var listeNoms=document.getElementsByName("nomMedia"); 
  var listeVimeos=document.getElementsByName("vimeo");
  var listeAjouts=document.getElementsByName("ajoutMiniature");
  var vids=listeVimeos.length-1;
  for (var i=listeTypes.length-1; i>=0; i--) {
    if ((listeTypes[i].value&MediaType.Video)!=0) { // le média est une vidéo
      listeAjouts[vids].setAttribute("name","ajoutMiniature"+i);
      listeVimeos[vids--].setAttribute("name","vimeo"+i);
    }
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
 document.getElementById("ligneparticipants").appendChild(span);
 //this.parentNode.insertBefore(span,input);
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
 suggestion.setAttribute("class","suggestionParticipant");
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
