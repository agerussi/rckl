<?php 
/////////////////////////////////////////////////////////////////
// script AJAX renvoyant les nouveaux messages sous format XML //
/////////////////////////////////////////////////////////////////

  session_start(); 
  // teste si l'utilisateur est connecté
  if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) exit(0);

  require("dbconnect.php");

  // récupère les messages plus vieux que le dernier TIME_STAMP
  //$query="SELECT auteur,message FROM chat_messages WHERE TIMESTAMPDIFF(SECOND,'".$_SESSION['timestamp']."',time)>=0 ORDER BY time";
  $query="SELECT auteur,message FROM chat_messages ORDER BY time LIMIT ".$_SESSION['numsent'].", 10000";
  //$query="SELECT auteur,message FROM chat_messages ORDER BY time";
  $result=mysql_query($query, $db) or die("Erreur lors de la collecte de messages dans chat_messages: ".mysql_error());

  // formate les messages sous forme XML
  $xml="<messagelist>";
  while ($row=mysql_fetch_array($result)) {
    $xml.="<message>";
    $xml.="<auteur>".stripslashes($row['auteur'])."</auteur>";
    $xml.="<corps>".stripslashes($row['message'])."</corps>";
    $xml.="</message>";
    $_SESSION['numsent']++; // compte le nombre de messages envoyés
  }
  $xml.="</messagelist>";

  // renvoie le XML
  echo $xml;

  // sauvegarde le TIME_STAMP actuel
/*
  $query="SELECT NOW()";
  $result=mysql_query($query, $db) or die("Erreur lors de la collecte du timestamp: ".mysql_error());
  $row=mysql_fetch_row($result);
  $_SESSION['timestamp']=$row[0];
*/
?>
