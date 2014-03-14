<?php // se connecte à la base de données
// $db = la base
$dbHost = "localhost";
$dbUser = "alexandre";
$dbPass = "alexsql";
$dbDatabase = "rckl";

//connect to the database
$db = mysql_connect("$dbHost", "$dbUser", "$dbPass") or die("Erreur lors de la connexion à la base: ".mysql_error());

mysql_select_db("$dbDatabase", $db) or die("Erreur lors de la sélection de la base: ".mysql_error());
mysql_query("SET NAMES UTF8");
?>
