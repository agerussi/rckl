<?php 
////////////////////////////////////////////////////////////
// script AJAX rajoutant un message à la BD chat_messages //
////////////////////////////////////////////////////////////
// le corps du message est contenu dans $_POST['msgBody']

  session_start(); 
  // teste si l'utilisateur est connecté
  if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) exit(0);

  require_once("dbconnect.php");

  $query="INSERT INTO chat_messages (auteur,message) VALUES('".addslashes($_SESSION['profilename'])."','".addslashes($_POST['msgBody'])."')";
  mysql_query($query, $db) or die("Erreur lors de l'insertion d'un message dans chat_messages: ".mysql_error());
  
?>

