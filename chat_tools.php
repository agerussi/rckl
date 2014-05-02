<?php 
//////////////////////////////////////////////////////
// fonctions AJAX permettant d'interagir avec la BD //
//////////////////////////////////////////////////////
// cmd=getmbrs,getmsg,sendmsg
// id=n° du salon

session_start(); 
// tests préalables
if (!isset($_SESSION['userid']) || empty($_SESSION['userid']) || !isset($_GET['cmd']) || !isset($_GET['id'])) exit(0);
$id=$_GET['id'];

require_once("dbconnect.php");

switch($_GET['cmd']) {
  case "getmbrs":
    updateTS($id,$_SESSION['userid']);
    getMembers($id);
    break;
  case "getmsg":
    $_SESSION["lastread"][$id]=getMessages($id,$_SESSION["lastread"][$id]);
    break;
  case "sendmsg":
    sendMessage($id,$_SESSION['profilename']);
    break;
  default:
}

// envoie un message sur le salon spécifié
function sendMessage($id,$nomprofil) {
  global $db;
  $query="INSERT INTO chat_messages (idsalon,auteur,message) VALUES({$id}, '".addslashes($nomprofil)."','".addslashes($_POST['msgBody'])."')";
  mysql_query($query, $db) or die("Erreur lors de l'insertion d'un message dans chat_messages: ".mysql_error());
} 

// récupère les messages numérotés > $lastread
// renvoie l'id du dernier lu
function getMessages($id,$lastread) {
  global $db;
  $query="SELECT id,auteur,message FROM chat_messages WHERE idsalon={$id} AND id>{$lastread} ORDER BY id";
  $result=mysql_query($query, $db) or die("Erreur lors de la collecte de messages dans chat_messages: ".mysql_error());

  // formate les messages sous forme JSON
  unset($json);
  while ($row=mysql_fetch_array($result)) {
    $message='{"auteur":"'.stripslashes($row['auteur']).'",';
    $message.='"corps":"'.stripslashes($row['message']).'",';
    $message.='"id":'.$row['id'].'}';
    $json[]=$message;
    $lastId=$row['id'];
  }

  // renvoie le JSON
  if (isset($json)) 
    echo '{"messagelist":['.implode(',',$json).']}';
  else
    echo '{"messagelist":[]}';

  return (isset($lastId) ? $lastId:$lastread);
}

// update le TIMESTAMP du membre qui vient d'appeler ce script
function updateTS($id,$idmembre) {
  global $db;
  $query="UPDATE chat_rooms SET timestamp=NOW() WHERE id={$id} AND idmembre={$idmembre}";
  mysql_query($query, $db) or die("Erreur lors de la mise à jour de timestamp: ".mysql_error());
}

// récupère la liste des membres considérés présents dans le salon spécifié
function getMembers($id) {
  global $db;
  $query="SELECT idmembre, nomprofil FROM chat_rooms WHERE TIMESTAMPDIFF(SECOND,timestamp,NOW())<15 AND id={$id}";
  $result=mysql_query($query, $db) or die("Erreur lors de la collecte des membres présents: ".mysql_error());

  // formate les membres sous format JSON
  unset($json);
  while ($row=mysql_fetch_array($result)) {
    $json[]='{"id":'.$row['idmembre'].',"nom":"'.stripslashes($row['nomprofil']).'"}';
  }

  // renvoie le JSON
  echo '{"memberlist":['.implode(',',$json).']}';
}
?>

