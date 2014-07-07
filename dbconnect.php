<?php // se connecte à la base de données
require_once("dbsecret.php");

//connect to the database
$db = mysql_connect("$dbHost", "$dbUser", "$dbPass") or die("Erreur lors de la connexion à la base: ".mysql_error());

mysql_select_db("$dbDatabase", $db) or die("Erreur lors de la sélection de la base: ".mysql_error());
mysql_query("SET NAMES UTF8");

// constantes composant le champ 'status' d'un membre
$STATUS_CANLOGIN=1; 
$STATUS_NEEDUPGRADE=2;
$STATUS_PENDING=4;
$query="SET @STATUS_CANLOGIN={$STATUS_CANLOGIN}, @STATUS_NEEDUPGRADE={$STATUS_NEEDUPGRADE}, @STATUS_PENDING={$STATUS_PENDING}";
mysql_query($query,$db);

// fonctions courantes en relation avec la base de données
//
// met à jour le champ idlesince de l'utilisateur $id
function idleUpdate($id) {
  global $db;
  $query="UPDATE membres SET idlesince=CURDATE() WHERE id='".$id."'";
  mysql_query($query,$db);
}
?>
