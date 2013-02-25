<?php 
//////////////////////////////////////////////////////////
// script AJAX supprimant l'utilisateur de chat_members //
// et vidant la liste des messages le cas échéant       //
//////////////////////////////////////////////////////////
  session_start(); 
  // teste si l'utilisateur est connecté
  if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) exit(0);

  require("dbconnect.php");

  // supprime l'utilisateur de la liste des connectés au chat
  $query="DELETE FROM chat_members WHERE id=".$_SESSION['userid']." LIMIT 1";
  mysql_query($query, $db) or die("Erreur lors de la suppression du membre de chat_members: ".mysql_error());

  // compte le nombre de connectés restants
  $query="SELECT COUNT(*) FROM chat_members";
  $result=mysql_query($query, $db) or die("Erreur lors du comptage du nombre de membres dans chat_members: ".mysql_error());
  $row=mysql_fetch_row($result);
  $n=$row[0];
 
  // s'il ne reste plus personne, on efface la liste des messages
  if ($n==0) {
    $query="DELETE * FROM chat_messages";
    mysql_query($query, $db) or die("Erreur lors de la suppression des messages de chat_messages: ".mysql_error());
  }
   
?>

