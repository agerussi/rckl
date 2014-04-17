<?php 
// détermine un nouveau n° de salon

session_start(); 
// tests préalables
if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) header("Location: news.php");
require_once("dbconnect.php");
// détermine le numéro du salon 
$query="SELECT MAX(id) as idMax FROM chat_rooms";
$result=mysql_query($query, $db) or die("Erreur lors de la collecte des id salons: ".mysql_error());
$row=mysql_fetch_array($result);
$id=$row['idMax']+1;
mysql_free_result($result);

// GOTO salon
header("Location: chat_room.php?id={$id}");
?>
