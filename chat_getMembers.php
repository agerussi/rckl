<?php 
////////////////////////////////////////////////////////////////
// script AJAX renvoyant la liste des membres sous format XML //
////////////////////////////////////////////////////////////////

  session_start(); 
  // teste si l'utilisateur est connecté
  if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) exit(0);

  require("dbconnect.php");

  // récupère les messages plus vieux que le dernier TIME_STAMP
  $query="SELECT DISTINCT id,nom FROM chat_members";
  $result=mysql_query($query, $db) or die("Erreur lors de la collecte de messages dans chat_messages: ".mysql_error());

  // formate les messages sous forme XML
  $xml="<memberlist>";
  while ($row=mysql_fetch_array($result)) {
    $xml.="<member>";
    $xml.="<id>".$row['id']."</id>";
    $xml.="<nom>".$row['nom']."</nom>";
    $xml.="</member>";
  }
  $xml.="</memberlist>";

  // renvoie le XML
  echo $xml;
?>

