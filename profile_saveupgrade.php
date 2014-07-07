<?php 
session_start();
require_once("magic_quotes_gpc_off.php");
require("profile_common.php");

// examen des variables retournées
foreach ($_POST as $key => $value) {
    //echo $key."=".$value."<br/>";
}

// traitement des données reçues et création de la requête de sauvegarde
// =====================================================================
$changes=array();
// nom
$nom=request("nom");
array_push($changes,"nom='".addslashes($nom)."'");
// prénom
$prenom=request("prenom");
array_push($changes,"prenom='".addslashes($prenom)."'");
// nomprofil
$nomprofil=request("nomprofil");
array_push($changes,"nomprofil='".addslashes($nomprofil)."'");
// datenaissance
$datenaissance=request("datenaissance");
array_push($changes,"datenaissance='".dateF2E($datenaissance)."'");
// latitude
$latitude=request("latitude");
array_push($changes,"latitude='".$latitude."'");
// longitude
$longitude=request("longitude");
array_push($changes,"longitude='".$longitude."'");
// indique que la mise à jour a été faite
array_push($changes,"status=status&~@STATUS_NEEDUPGRADE"); // TODO: vérifier si ca fonctionne !!
// met à jour idlesince
array_push($changes,"idlesince=CURDATE()");

// sauvegarde dans la base de données
require_once("dbconnect.php");
$query="UPDATE membres SET ";
$query.=implode(", ",$changes);
$query.=" WHERE id='".$_SESSION['userid']."'";
mysql_query($query,$db) or die("Erreur lors de la mise à jour d'un profil: ".mysql_error());
//echo $query;
mysql_close($db);

// auto-login 
// $_SESSION['login'] est déjà initialisé
// $_SESSION['userid'] est déjà initialisé
$_SESSION['profilename']=$nomprofil;
header("Location: news.php");
?>

