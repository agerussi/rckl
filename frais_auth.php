<?php
session_start();
if (!isset($_SESSION['userid']) || !isset($_GET['id'])) {
  header("Location: news.php");
}
// récupère l'id du membre [0] et celui de la note de frais [1]
$ids=explode(",",$_GET['id']);

// récupération de la note de frais
require_once("dbconnect.php");
$query="SELECT idAuteur, selected, auth FROM paiements WHERE id=".$ids[1];
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

// mail à l'auteur pour le prévenir en cas de contestation
if (!$auth[$index]) {
  require_once("mail.php");
  $email=getEmailAddress($paiement['idAuteur']);
  $subject="information note de frais";
  $body="Une de vos déclarations de frais vient d'être contestée par {$_SESSION['profilename']}.\n\nRendez vous sur la page de gestion des frais pour plus de détails.";
  sendAutoMail($email, $subject, $body);
}

header("Location: frais_affichage.php");
?>

