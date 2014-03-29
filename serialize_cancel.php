<?php
require_once("dbconnect.php");

$query = 'SELECT id,cancel FROM paiements ORDER BY date DESC';
$resultPaiements=mysql_query($query, $db) or die("Erreur lors de la récupération des paiements: ".mysql_error());

while ($ligne=mysql_fetch_array($resultPaiements)) {
  $cancel=$ligne['cancel'];
  $cancelTab=explode(',',$cancel);
  var_dump($cancelTab);
  $cancelSerial=serialize($cancelTab);
  echo $cancelSerial;
}


?>
