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

  // abonnement du bouton de validation pour mise en forme finale
  //document.getElementById("formulairePaiement").addEventListener("submit",validation);
}

// validation du formulaire avant envoi
function validation() {
  // inscrit le nombre d'extérieurs
  champNbBenExt.setAttribute("value", nbBenExt);

  // nomme les différents champs
  var champ=champNbBenExt.nextElementSibling;
  for (var i=1; i<=nbBenExt; i++) {
    var value=champ.value.trim()
    if (value.length==0) {
      alert("Veuillez nommer tous les bénéficiaires extérieurs");
      return false;
    }
    champ.value=value; // trimmed value 
    champ.setAttribute("name", "exterieur"+i);
    champ=champ.nextElementSibling.nextElementSibling;
  }

  return true;
}

// ajout d'un champ de saisie pour membre extérieur
// et augmentation du compteur
function ajouterExterieur() {
  // augmentation du compteur
  nbBenExt++;

  // ajout d'une zone input (sera nommée "exterieurXX")
  var input=document.createElement("input");
  input.setAttribute("type", "text");
  input.setAttribute("value", "");
  //input.setAttribute("name", "exterieur"+nbBenExt);
  champNbBenExt.parentNode.appendChild(input);
  
  // ajout du bouton d'annulation
  var croix=document.createElement("img");
  croix.setAttribute("src","ICONS/b_drop.png");
  croix.setAttribute("title","supprimer cet extérieur");
  croix.setAttribute("class","icon");
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
