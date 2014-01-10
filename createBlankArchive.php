<?php
session_start();
// test de sécurité 
if ($_SESSION['login']!="root" || !isset($_GET['id'])) header("Location: news.php");
$id=$_GET['id'];

require("dbconnect.php");
$sql = "SELECT NOT EXISTS(SELECT 1 FROM archives WHERE id='".$id."')";
$req = mysql_query($sql) or die("erreur lors de la recherche d'un ID dans la table des archives.");
$data = mysql_fetch_array($req);

echo $data[0]; // renvoie 1 ou 0
mysql_free_result($req);

if ($data[0]==1) { //crée une archive "vierge"
  $xml='<date/><titre></titre><participants></participants><commentaire></commentaire>';
  $sql = "INSERT INTO archives (id, xml) VALUES('".$id."','".$xml."')";
  mysql_query($sql, $db) or die("Erreur lors de la création d'une archive vierge:".mysql_error());
}

?>

