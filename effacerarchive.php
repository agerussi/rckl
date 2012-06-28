<?php
session_start();

if ($_SESSION['login']!="root" || !isset($_GET['id'])) header("Location: news.php?menu");

require("dbconnect.php");
require("helper.php");

// récupère l'année de la sortie pour pouvoir retourner à la bonne page d'archives !
  mysql_query("SET NAMES UTF8");
  $query='SELECT DATE_FORMAT(date,"%Y") AS annee, xml FROM archives WHERE id="'.$_GET['id'].'"';
  $result=mysql_query($query,$db) or die("Erreur lors de la lecture d'une archive: ".mysql_error());
  $row=mysql_fetch_array($result);
  $year=$row['annee']; 
  $xml="<sortie>".$row['xml']."</sortie>";
  //echo $year."//".$xml;

  // efface la sortie
  $query='DELETE FROM archives WHERE id="'.$_GET['id'].'"';
  mysql_query($query,$db) or die("Erreur lors de la suppression d'une archive: ".mysql_error());

  // analyse l'xml pour obtenir les fichiers à effacer
  $document=new DOMDocument();
  $document->loadXML($xml);
  $photos = $document->getElementsByTagName('photo');
  efface($photos);
  $videos = $document->getElementsByTagName('video');
  efface($videos);

  // retour à la page d'archives d'où l'effacement a été fait
  header("Location: archives.php?menu&y=".$year);

function efface($medias) { // DOMNodeList $medias
  global $repStockage;
  foreach ($medias as $media) { // efface chaque fichier
    $fichier=$repStockage."/".$media->getAttribute("fichier");
    unlink($fichier);
    unlink(nomFichierMiniature($fichier));
  }
}
?>
