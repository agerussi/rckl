<?php
session_start();
// =========== tests préalables
//teste si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
  header("Location: news.php");
}
// teste si les données sont en place
if (!isset($_SESSION['paiement-informations'])
    || !isset($_SESSION['paiement-selectedList'])
    || !isset($_SESSION['paiement-somme'])
    || !isset($_SESSION['paiement-description'])) {
  header("Location: frais_nouveau.php");
}

// données pré-calculées dans frais_validation.php
$informations=$_SESSION['paiement-informations'];
$selectedList=$_SESSION['paiement-selectedList'];
$somme=$_SESSION['paiement-somme'];

// rajout dans l'historique
require_once("dbconnect.php");
$query="INSERT INTO paiements (date, idAuteur, auteur, somme, commentaire, selected, auth, status) VALUES(CURDATE()";
$query.=",'".$_SESSION['userid']."'";
$query.=",'".$_SESSION['profilename']."'";
$query.=",".$somme;
$query.=",'".addslashes($_SESSION['paiement-description'])."'";
$query.=",'".serialize($selectedList)."'";
$query.=",'".serialize(array_pad(array(),count($selectedList),true))."'";
$query.=",'AUTH')";
//echo "debug: query=".$query.'<br/>';
mysql_query($query, $db) or die("erreur lors de l'ajout dans l'historique: ".mysql_error());
idleUpdate($_SESSION['userid']);

// annule tout pour éviter des problèmes éventuels
unset($_SESSION['paiement-informations']);
unset($_SESSION['paiement-selectedList']);
unset($_SESSION['paiement-somme']);
header("Location: frais_affichage.php");
?>
