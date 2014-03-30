<?php
require_once("dbconnect.php");

$query = 'SELECT id,cancel FROM paiements ORDER BY date DESC';
$resultPaiements=mysql_query($query, $db) or die("Erreur lors de la récupération des paiements: ".mysql_error());

while ($ligne=mysql_fetch_array($resultPaiements)) {
  $oldCancelTab=unserialize($ligne['cancel']);
  $newCancelTab=array();
  for ($i=0; $i<count($oldCancelTab); $i+=2) {
    $var=$oldCancelTab[$i+1];
    if ($var<0) $id=$oldCancelTab[$i];
    if ($var>0) array_push($newCancelTab, $oldCancelTab[$i]);
  }
  //var_dump($newCancelTab);
  //echo "idAuteur=".$id.PHP_EOL;
  $cancelSerial=serialize($newCancelTab);
  $query="UPDATE paiements SET idAuteur=".$id.", cancel='".$cancelSerial."' WHERE id='".$ligne['id']."'";
  echo $query.PHP_EOL;
  mysql_query($query,$db);
}


?>
