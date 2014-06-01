<?php
// récupère les notes arrivées à échéance
require_once("dbconnect.php");
$query = "SELECT * FROM paiements WHERE status='AUTH' AND date<=DATE_SUB(CURDATE(),INTERVAL 20 DAY)";
$resultPending=mysql_query($query, $db) or die("Erreur lors de la récupération des paiements: ".mysql_error());

while ($paiement=mysql_fetch_array($resultPending)) {
  // teste si le paiement a été accepté par tous
  $auth=unserialize($paiement['auth']);
  $authorized=true;
  foreach ($auth as $decision)
    if (!$decision) {
      $authorized=false;
      break;
    }

  // calcule combien chacun des bénéficiaires devra
  $selectedList=unserialize($paiement['selected']);
  $isAuthorIncluded=in_array($paiement['idAuteur'],$selectedList); // regarde si l'auteur est un bénéficiaire
  $somme=$paiement['somme'];
  $numMembres=count($selectedList);
  $chacun=round(100*$somme/$numMembres)/100;

  // informations sur les bénéficiaires (et l'auteur)
  $informations=array();
  if (!$isAuthorIncluded) array_push($selectedList,$paiement['idAuteur']); // rajout artificiel de l'auteur
  foreach ($selectedList as $id) {
    $query ="SELECT id,nomprofil,solde FROM membres WHERE id='{$id}'";
    $result=mysql_query($query,$db);
    if (mysql_num_rows($result)!=1) die("Erreur lors de la récupération des informations d'un membre: ".mysql_error());
    $ligne = mysql_fetch_array($result);
    $informations[$ligne['id']]= array('nom'=>$ligne['nomprofil'], 'solde'=>$ligne['solde']);
  }

  // application des variations si l'ensemble est autorisé
  if ($authorized) {
    // calcule les nouveaux soldes, la liste des variations 
    $listevariations=array();
    foreach ($informations as $id=>$infos) {
      if ($id==$paiement['idAuteur']) {
	$variation=$somme; 
	if ($isAuthorIncluded) $variation-=$chacun;
      }
      else $variation=-$chacun;
      $informations[$id]['solde']+=$variation; // nouveau solde
      array_push($listevariations, $infos['nom'].'('.(($variation>0) ? "+":"").$variation.')');
    }
    //echo "debug: listevariations=".$listevariations.'<br/>';

    // modifie la facture
    $query="UPDATE paiements SET ";
    $query.="variations='".addslashes(implode(', ',$listevariations))."'";
    $query.=",status='DONE'";
    $query.=" WHERE id=".$paiement['id'];
    //echo "debug: query=".$query.'<br/>';
    mysql_query($query, $db) or die("erreur lors de l'ajout dans l'historique: ".mysql_error());

    // mise à jour des soldes
    foreach ($informations as $id=>$infos) {
      $query="UPDATE membres SET solde={$infos['solde']} WHERE id={$id}";
      //echo "debug:".$query.'<br/>';
      mysql_query($query,$db) or die("erreur lors de la mise à jour d'un solde: ".mysql_error());
    }

    // envoie un mail à l'auteur pour confirmer
    require_once("mail.php");
    $email=getEmailAddress($paiement['idAuteur']);
    $subject="information note de frais";
    $body="Votre déclaration de frais de {$somme} € du ".formatDate($paiement['date'])." est arrivée à échéance et a bien été prise en compte.";
    sendAutoMail($email, $subject, $body);
  }
  else { // la note n'a pas été acceptée
    // supprime la note
    $query="DELETE FROM paiements WHERE id=".$paiement['id'];
    mysql_query($query,$db) or die("Erreur lors de la suppression d'une note de frais: ".mysql_error()); 
    // envoie un mail à l'auteur
    require_once("mail.php");
    $email=getEmailAddress($paiement['idAuteur']);
    $subject="information note de frais";
    $body="Votre déclaration de frais de {$somme} € du ".formatDate($paiement['date'])." est arrivée à échéance mais n'a pas été prise en compte, et supprimée.\n";
    $body.="En effet celle-ci a été réfutée par le(s) membre(s) suivant(s): ";
    $members=array();
    for ($i=0; $i<count($selectedList); $i++) 
      if (!$auth[$i]) {
	$id=$selectedList[$i];
	array_push($members,$informations[$id]['nom']);
      }
    $body.=implode(', ',$members).".\n\n";
    $body.="Il vous appartient à présent de régler cet éventuel conflit avec les intéressés puis de refaire une déclaration de frais.\n";
    $body.="En dernier recours, contactez un administrateur pour envisager une remédiation.";
    sendAutoMail($email, $subject, $body);
  }
}

function formatDate($str) {
  sscanf($str,"%u-%u-%u",$annee,$mois,$jour);
  return $jour.'/'.$mois.'/'.$annee%100;
}
?>
