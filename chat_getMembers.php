<?php 
//////////////////////////////////////////////////////////////////
// script AJAX renvoyant la liste des membres sous format XML   //
// et gère la liste des membres présents ainsi que l'effacement //
// de la liste des messages quand une nouvelle discussion est   //
// détectée                                                     // 
//////////////////////////////////////////////////////////////////

  session_start(); 
  // teste si l'utilisateur est connecté
  if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) exit(0);

  require("dbconnect.php");

  // récupère la liste des membres considérés présents  
  $query="SELECT id,nom FROM membres WHERE TIMESTAMPDIFF(SECOND,chattimestamp,NOW())<60";
  $result=mysql_query($query, $db) or die("Erreur lors de la collecte des membres présents: ".mysql_error());

  // si la liste est vide, une nouvelle conversation commence
  if (mysql_num_rows($result)==0) { // on efface les anciens messages
    $query="TRUNCATE TABLE chat_messages";
    mysql_query($query, $db) or die("Erreur lors de la suppression des messages de chat_messages: ".mysql_error());
  }

  // formate les membres sous format JSON
  unset($json);
  while ($row=mysql_fetch_array($result)) {
    $json[]='{"id":'.$row['id'].',"nom":"'.stripslashes($row['nom']).'"}';
  }

  // renvoie le JSON
  echo '{"memberlist":['.implode(',',$json).']}';

  // update le TIMESTAMP du membre qui vient d'appeler ce script
  $query="UPDATE membres SET chattimestamp=NOW() WHERE id='".$_SESSION['userid']."'";
  mysql_query($query, $db) or die("Erreur lors de la mise à jour de chattimestamp: ".mysql_error());
?>

