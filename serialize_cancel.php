<?php
require_once("dbconnect.php");

$query = 'SELECT id,cancel FROM paiements ORDER BY date DESC';
$resultPaiements=mysql_query($query, $db) or die("Erreur lors de la récupération des paiements: ".mysql_error());

while ($ligne=mysql_fetch_array($resultPaiements)) {
  $cancel=$ligne['cancel'];
  $cancelTab=explode(',',$cancel);
  for ($i=0; $i<count($cancelTab); $i++) {
    $cancelTab[$i]=($i%2==0) ? intval($cancelTab[$i]):floatval($cancelTab[$i]);
  }
  //var_dump($cancelTab);
  $cancelSerial=serialize($cancelTab);
  $query="UPDATE paiements SET cancel='".$cancelSerial."' WHERE id='".$ligne['id']."'";
  mysql_query($query,$db);
}


?>
