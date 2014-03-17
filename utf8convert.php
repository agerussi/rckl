<?php
// il faut désactiver le SET NAMES général

$source='participants';
$cible='u8participants';

require_once("dbconnect.php");
$query = 'SELECT id, '.$source.' FROM sorties';
$result=mysql_query($query, $db);

mysql_query('SET NAMES UTF8');

while($ligne = mysql_fetch_array($result)) {
  echo $ligne['id'].":".$ligne[$source].'<br/>';
  $query="UPDATE sorties SET ".$cible."='".addslashes($ligne[$source])."' WHERE id=".$ligne['id'];
  mysql_query($query,$db);
}


?>
