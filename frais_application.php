<?php
// récupère les notes arrivées à échéance
require_once("dbconnect.php");
$query = "SELECT * FROM paiements WHERE status='AUTH' AND date<DATE_SUB(CURDATE(),INTERVAL 20 DAY)";
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
  if ($authorized) {
    // récupère les données des membres sélectionnés 
    $selectedList=unserialize($paiement['selected']);
    $informations=array();
    foreach ($selectedList as $id) {
      $query ="SELECT id,nomprofil,solde FROM membres WHERE id='".$id."'";
      $result=mysql_query($query,$db);
      if (mysql_num_rows($result)!=1) die("Erreur lors de la récupération des informations d'un membre: ".mysql_error());
      $ligne = mysql_fetch_array($result);
      array_push($informations, 
	array('nom'=>$ligne['nomprofil'],
	'id'=>$ligne['id'],
	'solde'=>$ligne['solde']));
    }
    $somme=$paiement['somme'];
    $numMembres=count($selectedList);

    // calcule les nouveaux soldes, la liste des variations 
    $chacun=round(100*$somme/$numMembres)/100;
    $listevariations=""; 
    for ($i=0; $i<$numMembres; $i++) {
      $id=$informations[$i]['id'];
      $variation=-$chacun;
      if ($id==$paiement['idAuteur']) $variation+=$somme;
      $informations[$i]['solde']+=$variation; // nouveau solde
      $listevariations.=$informations[$i]['nom'].'('.(($variation>0) ? "+":"").$variation.')';
      if ($i!=$numMembres-1) $listevariations.=", ";
    }
    //echo "debug: listevariations=".$listevariations.'<br/>';

    // modifie la facture
    $query="UPDATE paiements SET ";
    $query.="variations='".addslashes($listevariations)."'";
    $query.=",status='DONE'";
    $query.=" WHERE id=".$paiement['id'];
    //echo "debug: query=".$query.'<br/>';
    mysql_query($query, $db) or die("erreur lors de l'ajout dans l'historique: ".mysql_error());

    // mise à jour des soldes
    // les bénéficiaires
    for ($i=0; $i<$numMembres; $i++) {
      $query="UPDATE membres SET solde=".$informations[$i]['solde']." WHERE id=".$informations[$i]['id'];
      //echo "debug:".$query.'<br/>';
      mysql_query($query,$db) or die("erreur lors de la mise à jour d'un bénéficiaire: ".mysql_error());
    }
    // l'auteur
    $query="SELECT solde FROM membres WHERE id=".$paiement['idAuteur'];
    //echo "debug:".$query.'<br/>';
    $result=mysql_query($query,$db) or die("erreur lors de la récupération du solde de l'auteur: ".mysql_error());
    $ligne=mysql_fetch_array($result) or die("erreur lors de la récupéraion du solde de l'auteur (2): ".mysql_error());
    $newsolde=$ligne['solde']+$somme;
    $query="UPDATE membres SET solde=".$newsolde." WHERE id=".$paiement['idAuteur'];
    //echo "debug:".$query.'<br/>';
    mysql_query($query,$db) or die("erreur lors de la mise à jour du solde de l'auteur: ".mysql_error());
  }
  else { // la note n'a pas été acceptée
    // supprime la note
    $query="DELETE FROM paiements WHERE id=".$paiement['id'];
    mysql_query($query,$db) or die("Erreur lors de la suppression d'une note de frais: ".mysql_error()); 
    // envoie un mail à l'auteur
    // TODO
  }
}
?>
