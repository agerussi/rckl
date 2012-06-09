<?php
// supprime le membre de la sortie ids
session_start();
if (!isset($_SESSION['userid'])|| !isset($_GET['ids'])) header("Location: sorties.php?menu");
$idsortie=$_GET['ids']; // sortie

require("dbconnect.php");
$query="SELECT participants FROM sorties WHERE id=$idsortie";
$listesorties=mysql_query($query, $db) or die("Erreur lors de la recherche de la sortie: ".mysql_error());
$sortie=mysql_fetch_array($listesorties);
$liste=explode(',',$sortie['participants']); // la tableau des participants et leurs id

$newliste="";
$n=count($liste);
$rajoute=false;
for ($i=0; $i<$n; $i+=2) {
  if ($liste[$i+1]!=$_SESSION['userid']) {
    if ($rajoute) 
      $newliste.=",";
    else
      $rajoute=true;
    $newliste.=$liste[$i].",".$liste[$i+1];
  }
}
//echo $newliste."\n";

// met à jour la base
$query="UPDATE sorties SET participants=\"$newliste\" WHERE id=$idsortie";
if (!mysql_query($query,$db)) die("Erreur lors de la mise à jour des participants à une sortie: ".mysql_error());

header("Location: sorties.php?menu");
?>
