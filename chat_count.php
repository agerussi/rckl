<?php 
/////////////////////////////////////////////////////////////////
// script AJAX renvoyant le nombre de connectés et de messages //
/////////////////////////////////////////////////////////////////
  session_start(); 
  // teste si l'utilisateur est connecté
  if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) exit(0);

  require("dbconnect.php");

  // compte le nombre de connectés restants
  $query="SELECT COUNT(*) FROM chat_members";
  $result=mysql_query($query, $db) or die("Erreur lors du comptage du nombre de membres dans chat_members: ".mysql_error());
  $row=mysql_fetch_row($result);
  $n=$row[0];

  // compte le nombre de messages
  $query="SELECT COUNT(*) FROM chat_messages";
  $result=mysql_query($query, $db) or die("Erreur lors du comptage du nombre de messages dans chat_messages: ".mysql_error());
  $row=mysql_fetch_row($result);
  $m=$row[0];

  // valeurs renvoyées au script JS
  echo $n." ".$m; 
?>

