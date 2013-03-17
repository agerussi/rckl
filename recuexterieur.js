// validation du formulaire avant envoi
function validation() {
  // récupérer la somme
  var champSomme=document.getElementById("somme");
  var sommeStr=champSomme.value.replace(",","."); 
  var sommeNum=parseFloat(sommeStr);

  if (sommeNum.toString()!=sommeStr) {
    alert("La somme saisie est invalide");
    return false;
  }
  
  return true;
}
