<?php 
// fonctions concernant la gestion des news

require_once("dbconnect.php");

function insertNews($auteur,$body) {
  $query = "INSERT INTO news (date, auteur, texte) VALUES(CURDATE(),'".addslashes($auteur)."','".addslashes(trim($body))."')";
  //echo $query;
  global $db;
  mysql_query($query, $db) or die("insertNews: erreur lors de l'insertion de la news: ".mysql_error());
}

function cleanNews() {
  $query="DELETE FROM news WHERE date<DATE_SUB(CURDATE(),INTERVAL 2 MONTH)";
  global $db;
  mysql_query($query,$db) or die("cleanNews: erreur lors de l'effacement des anciennes news: ".mysql_error());
}
