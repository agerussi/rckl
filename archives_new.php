<?php
// crée une nouvelle archive «vierge» et lance son édition

session_start();
// test de sécurité 
if (!isset($_SESSION['userid'])) header("Location: news.php");
$userId=$_SESSION['userid'];

require("dbconnect.php");
// crée une archive "vierge" 
// avec l'auteur initialisé 
// et la date courante mise par défaut 

$date=getdate();
$dbDate=$date['year'].'-'.$date['mon'].'-'.$date['mday']; // date pour la BD
$xml='<date annee="'.$date['year'].'" mois="'.$date['mon'].'" jour="'.$date['mday'].'"/>';
$xml.='<titre></titre><participants></participants><commentaire></commentaire>';
$sql = "INSERT INTO archives (authId, date, xml) VALUES('".$userId."','".$dbDate."','".$xml."')";
mysql_query($sql, $db) or die("Erreur lors de la création d'une archive vierge:".mysql_error());

// récupère l'id de l'archive qui vient d'être créée
 $id=mysql_insert_id(); // ATTENTION: pour BD mySQL seulement

// lance l'édition de l'archive avec le drapeau "new"
header("Location: archives_edition.php?id=".$id."&new");
?>

