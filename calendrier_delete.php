<?php

session_start();
if (isset($_SESSION['userid']) && isset($_GET['ids'])) {
  $query="DELETE FROM sorties WHERE id=".$_GET['ids'];
  require_once("dbconnect.php");
  mysql_query($query,$db) or die("Erreur lors de la suppression d'une sortie: ".mysql_error());
}
header("Location: calendrier.php");

?>
