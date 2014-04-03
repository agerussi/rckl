<?php
session_start();
if (!isset($_SESSION['userid']) || !isset($_GET['id'])) {
  header("Location: news.php");
}
// récupère l'id du membre [0] et celui de la note de frais [1]
$ids=explode(",",$_GET['id']);

// récupération de la note de frais
require_once("dbconnect.php");
$query="SELECT selected, auth FROM paiements WHERE id=" . $ids[1];
$listepaiement=mysql_query($query,$db) or die("Erreur lors d'une acceptation de frais: ".mysql_error());
if (count($listepaiement)!=1) die("La note de frais id=".$ids[1]." n'a pas été retrouvée");
$paiement=mysql_fetch_array($listepaiement);

// inversion du status
$index=array_search($ids[0],unserialize($paiement['selected']));
$auth=unserialize($paiement['auth']);
$auth[$index]=!$auth[$index];

// sauvegarde
$query="UPDATE paiements SET auth='".serialize($auth)."' WHERE id=".$ids[1];
mysql_query($query,$db) or die("Erreur lors de la mise à jour de la note de frais id=".$ids[1].": ".mysql_error());

header("Location: frais_affichage.php");
?>

