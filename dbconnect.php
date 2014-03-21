<?php // se connecte à la base de données
require_once("dbsecret.php");

//connect to the database
$db = mysql_connect("$dbHost", "$dbUser", "$dbPass") or die("Erreur lors de la connexion à la base: ".mysql_error());

mysql_select_db("$dbDatabase", $db) or die("Erreur lors de la sélection de la base: ".mysql_error());
mysql_query("SET NAMES UTF8");

// fonctions courantes en relation avec la base de données
//
// met à jour le champ idlesince de l'utilisateur $id
function idleUpdate($id) {
  $query="UPDATE membres SET idlesince=CURDATE() WHERE id='".$id."'";
  mysql_query($query);
}
?>
