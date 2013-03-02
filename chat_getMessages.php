<?php 
/////////////////////////////////////////////////////////////////
// script AJAX renvoyant les nouveaux messages sous format XML //
/////////////////////////////////////////////////////////////////

  session_start(); 
  // teste si l'utilisateur est connecté
  if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) exit(0);

  require("dbconnect.php");

  // récupère les messages numérotés > $_SESSION['numsent']
  $query="SELECT auteur,message FROM chat_messages WHERE num > ".$_SESSION['numsent']." ORDER BY num";
  $result=mysql_query($query, $db) or die("Erreur lors de la collecte de messages dans chat_messages: ".mysql_error());

  // formate les messages sous forme JSON
  unset($json);
  while ($row=mysql_fetch_array($result)) {
    $message='{"auteur":"'.stripslashes($row['auteur']).'",';
    $message.='"corps":"'.stripslashes($row['message']).'"}';
    $json[]=$message;
    $_SESSION['numsent']++; // compte le nombre de messages envoyés
  }

  // renvoie le JSON
  echo '{"messagelist":['.implode(',',$json).']}';
?>

