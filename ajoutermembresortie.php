<?php
// ajoute le membre id à la sortie ids
session_start();
if (!isset($_SESSION['userid']) || !isset($_GET['ids'])) header("Location: calendrier.php?menu");

$idsortie=$_GET['ids']; // sortie

require("dbconnect.php");
$query="SELECT participants FROM sorties WHERE id=$idsortie";
$listesorties=mysql_query($query, $db) or die("Erreur lors de la recherche de la sortie: ".mysql_error());
$sortie=mysql_fetch_array($listesorties);
$liste=$sortie['participants']; 
if (!empty($liste)) $newliste=$liste.",";
else $newliste="";
$newliste.=$_SESSION['realname'].",".$_SESSION['userid'];

// met à jour la base
$query="UPDATE sorties SET participants=\"$newliste\" WHERE id=$idsortie";
if (!mysql_query($query,$db)) die("Erreur lors de la mise à jour des participants à une sortie: ".mysql_error());

header("Location: calendrier.php?menu");
?>
