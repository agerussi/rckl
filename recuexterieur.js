// validation du formulaire avant envoi
function validation() {
  // récupérer la somme
  var champSomme=document.getElementById("somme");
  var sommeStr=champSomme.value.replace(",","."); 
  var sommeNum=parseFloat(sommeStr);

  if (sommeNum.toString()!=sommeStr || sommeNum<=0) {
    alert("La somme saisie est invalide");
    return false;
  }
 
  return confirm("Vous déclarez avoir reçu "+sommeNum+" €. Confirmez-vous ?"); 
}
