<?php 
//////////////////////////////////////////////////////////
// script AJAX supprimant l'utilisateur de chat_members //
//////////////////////////////////////////////////////////
  session_start(); 
  //teste si l'utilisateur est connecté
  if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) exit(0);

  require("dbconnect.php");

  // supprime l'utilisateur de la liste des connectés au chat
  $query="DELETE FROM chat_members WHERE id=".$_SESSION['userid'];
  mysql_query($query, $db) or die("Erreur lors de la suppression du membre de chat_members: ".mysql_error());
?>

