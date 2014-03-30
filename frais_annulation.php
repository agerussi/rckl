<?php
session_start();
if (!isset($_SESSION['userid']) || !isset($_GET['id'])) {
  header("Location: news.php");
}
if ($_SESSION['userid']!=1) header("Location: news.php");
$idFrais=$_GET['id'];

// connexion à la base de données
require_once("dbconnect.php");
$query="SELECT idAuteur, somme, selected FROM paiements WHERE id=" . $idFrais;
$listepaiement=mysql_query($query,$db) or die("Erreur lors d'une annulation de frais: ".mysql_error());
if (count($listepaiement)!=1) die("La note de frais id=".$idFrais." n'a pas été retrouvée");
$paiement=mysql_fetch_array($listepaiement);
$selectedList=unserialize($paiement['selected']);
$somme=$paiement['somme'];

// remet l'argent aux ex-bénéficiaires des frais
$chacun=round(100*$somme/count($selectedList))/100;
foreach ($selectedList as $id) {
  $query="SELECT solde FROM membres WHERE id=" . $id;
  $listemembres=mysql_query($query,$db) or die("Erreur lors de la récupération du solde d'un membre: ".mysql_error());
  if (count($listemembres)!=1) 
    echo("warning: le membre id=".$id." n'a pu être retrouvé");
  else {
    $membre=mysql_fetch_array($listemembres);
    $membre['solde']+=$chacun; // mise à jour du solde

    $query="UPDATE membres SET solde=".$membre['solde']." WHERE id=".$id;
    mysql_query($query,$db) or die("Erreur lors de la mise à jour du solde du membre id=".$id.": ".mysql_error());
  }
}

// enlève l'argent à l'emmetteur de la dépense
$idAuteur=$paiement['idAuteur'];
$query="SELECT solde FROM membres WHERE id=" . $idAuteur;
$listemembres=mysql_query($query,$db) or die("Erreur lors de la récupération du solde d'un membre: ".mysql_error());
if (count($listemembres)!=1) 
  echo("warning: le membre id=".$idAuteur." n'a pu être retrouvé");
else {
  $membre=mysql_fetch_array($listemembres);
  $membre['solde']-=$somme; // mise à jour du solde

  $query="UPDATE membres SET solde=".$membre['solde']." WHERE id=".$idAuteur;
  mysql_query($query,$db) or die("Erreur lors de la mise à jour du solde du membre id=".$idAuteur.": ".mysql_error());
}


// efface la note de frais
$query="DELETE FROM paiements WHERE id=".$idFrais;
mysql_query($query,$db) or die("Erreur lors de l'effacement final d'une note de frais: ".mysql_error());

header("Location: frais_affichage.php");
?>

