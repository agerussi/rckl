<?php 
///////////////////////////////////////
// script AJAX effaçant les messages //
///////////////////////////////////////
  session_start(); 
  // teste si l'utilisateur est connecté
  if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) exit(0);

  require("dbconnect.php");

  $query="DELETE FROM chat_messages";
  mysql_query($query, $db) or die("Erreur lors de la suppression des messages de chat_messages: ".mysql_error());
?>

