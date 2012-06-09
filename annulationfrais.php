<?php
session_start();
if (!isset($_SESSION['userid']) || !isset($_GET['id'])) {
  header("Location: news.php?menu");
}
if ($_SESSION['userid']!=1) header("Location: news.php?menu");
$id=$_GET['id'];

// connexion à la base de données
require("dbconnect.php");
$query="SELECT cancel FROM paiements WHERE id=" . $id;
$listepaiement=mysql_query($query,$db) or die("Erreur lors d'une annulation de frais: ".mysql_error());
if (count($listepaiement)!=1) die("La note de frais id=".$id." n'a pas été retrouvée");
$paiement=mysql_fetch_array($listepaiement);
$cancel=$paiement['cancel'];

$liste=explode(',',$cancel);
for ($i=0; $i<count($liste); $i+=2) {
  $query="SELECT solde FROM membres WHERE id=" . $liste[$i];
  $listemembres=mysql_query($query,$db) or die("Erreur lors de la récupération du solde d'un membre: ".mysql_error());
  if (count($listemembres)!=1) 
    echo("warning: le membre id=".$liste[$i]." n'a pu être retrouvé");
  else {
    $membre=mysql_fetch_array($listemembres);
    $membre['solde']+=$liste[$i+1]; // mise à jour du solde

    $query="UPDATE membres SET solde=".$membre['solde']." WHERE id=".$liste[$i];
    mysql_query($query,$db) or die("Erreur lors de la mise à jour du solde du membre id=".$liste[$i].": ".mysql_error());
  }
}

// efface la note de frais
$query="DELETE FROM paiements WHERE id=".$id;
mysql_query($query,$db) or die("Erreur lors de l'effacement final d'une note de frais: ".mysql_error());

header("Location: gestiondesfrais.php");
?>

