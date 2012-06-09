/*
   TODO: gestion de la récupération des données (en particulier images) côté serveur
*/


function gestionAjoutImage(evt) { // ajout d'une image: evt.target.result contient l'URL
  var table=document.createElement("table");
  table.innerHTML=[
    '<tr><td>',
    '<img src="', evt.target.result, '" height="85px" name="photo" />',
    '</td></tr><tr><td>',
    '<img title="supprimer la photo" src="FONDS/b_drop.png" name="supprimerphoto"/>', 
    '<input type="hidden" value="1"/>',
    '<img title="éditer le commentaire" src="FONDS/b_edit.png" name="editercommentaire"/>',
    '<input type="hidden" value=""/>'
   ].join('');
  var input=document.getElementById("listePhotos");
  input.insertBefore(table,null); 
  abonnementsPhotos(); // pour la nouvelle image
}

function gestionAjoutFichiers(evt) { // gère tous les ajouts de fichiers (photo + vidéo)
  var listeFichiers = evt.target.files; // FileList object
  var fichier;

  for (var i=0; fichier=listeFichiers[i]; i++) { // traitement individuel de chaque fichier
    if (fichier.type.match('image.*')) {
      var reader = new FileReader();
      reader.onload = gestionAjoutImage;
      reader.readAsDataURL(fichier);
      continue;
    }
    if (fichier.type.match('video.*')) { // TODO
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

function supprimerPhoto() { // gère la suppression / réhabilitation de photos
  var imgPhoto = this.parentNode.parentNode.parentNode.firstChild.firstChild.firstChild;
  var input = this.nextSibling;
  input.value = 1-input.value;
  if (input.value==0) {
    this.setAttribute("src","FONDS/b_add.png");
    this.setAttribute("title","rajouter la photo");
    imgPhoto.style.opacity = "0.4";
  } else {
    this.setAttribute("src","FONDS/b_drop.png");
    this.setAttribute("title","supprimer la photo");
    imgPhoto.style.opacity = "1";
  } 
  //window.alert("fin supprimerPhoto");
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
  // la date
  if (document.getElementById("valeurdate").value.length==0) {
    window.alert("La date n'est pas définie!");
    return false;
  }

  // récolte des participants dans listeparticipants
  var listexml="";
  var liste=document.getElementsByName("participant");
  for (i=0; i<liste.length; i++) listexml += "<nom>"+liste[i].firstChild.data+"</nom>";

  document.getElementById("listeparticipants").value = listexml;  

  // les photos (TODO)
  return true;
}

// main() est appelée lorsque la page est chargée
window.addEventListener("load",main);
function main() {
  initGestionDate();
  initGestionParticipants();
  abonnementsPhotos();
  // gestion du bouton ajouterPhoto
  document.getElementById('ajoutFichiers').addEventListener('change', gestionAjoutFichiers, false);
}

function abonnementsPhotos() { // abonnements aux diverses fonctions
  var listePhotos = document.getElementsByName("photo");
  var i=0;
  while (listePhotos[i]) listePhotos[i++].addEventListener("click", deplacementPhoto);
  var listeSupprimerPhotos = document.getElementsByName("supprimerphoto");
  i=0;
  while (listeSupprimerPhotos[i]) listeSupprimerPhotos[i++].addEventListener("click", supprimerPhoto);
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
