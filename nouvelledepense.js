// abonnement main()
window.addEventListener("load",main);

// variables globales
var boutonPlus; // le bouton d'ajout d'un membre extérieur
var champNbBenExt; // champ donnant le nombre de bénéficiaires extérieurs
var nbBenExt=0; // nombre de bénéficiaires extérieurs

// fonction principale
function main() {
  // abonnement du bouton +
  boutonPlus=document.getElementById("boutonPlus");
  boutonPlus.addEventListener("click", ajouterExterieur);

  // récupération champ nb bénéficiaires
  champNbBenExt=document.getElementById("nbBenExt");
}


// ajout d'un champ de saisie pour membre extérieur
// et augmentation du compteur
function ajouterExterieur() {
  // augmentation du compteur
  nbBenExt++;

  // ajout d'une zone input (sera nommée "exterieurXX")
  var input=document.createElement("input");
  input.setAttribute("type", "text");
  //input.setAttribute("name", "exterieur"+nbBenExt);
  champNbBenExt.parentNode.appendChild(input);
  
  // ajout du bouton d'annulation
  var croix=document.createElement("img");
  croix.setAttribute("src","FONDS/b_drop.png");
  croix.setAttribute("title","supprimer cet extérieur");
  croix.setAttribute("style","margin-right:2em");
  croix.addEventListener("click", supprimerExterieur);
  champNbBenExt.parentNode.appendChild(croix);
}

// supprime un participant extérieur
function supprimerExterieur() {
  // diminution du compteur
  nbBenExt--;

  // suppression
  this.parentNode.removeChild(this.previousSibling);
  this.parentNode.removeChild(this);
}
