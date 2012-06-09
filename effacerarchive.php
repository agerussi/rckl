<?php
session_start();

if ($_SESSION['login']!="root" || !isset($_GET['id'])) header("Location: news.php?menu");

require("dbconnect.php");

// récupère l'année de la sortie pour pouvoir retourner à la bonne page d'archives !
  $query='SELECT DATE_FORMAT(date,"%Y") AS annee FROM archives WHERE id="'.$_GET['id'].'"';
  $result=mysql_query($query,$db) or die("Erreur lors de la récupération de l'année d'une archive: ".mysql_error());
  $annees=mysql_fetch_array($result);
  $year=$annees['annee'];

  // efface la sortie
  $query='DELETE FROM archives WHERE id="'.$_GET['id'].'"';
  //mysql_query($query,$db) or die("Erreur lors de la suppression d'une archive: ".mysql_error());
  
// retour à la page d'où l'effacement a été fait
  header("Location: _archives.php?menu&y=".$year);
?>
