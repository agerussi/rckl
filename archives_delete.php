<?php
session_start();

if (!isset($_SESSION['userid']) || !isset($_GET['id'])) header("Location: news.php");
$userId=$_SESSION['userid'];
$id=$_GET['id'];

require("dbconnect.php");
require("helper.php");

// récupère l'année de la sortie pour pouvoir retourner à la bonne page d'archives !
// récupère authId pour sécuriser le principe
  mysql_query("SET NAMES UTF8");
  $query='SELECT authId, DATE_FORMAT(date,"%Y") AS annee, xml FROM archives WHERE id="'.$id.'"';
  $result=mysql_query($query,$db) or die("Erreur lors de la lecture d'une archive: ".mysql_error());
  $row=mysql_fetch_array($result);
  $year=$row['annee']; 
  $xml="<sortie>".$row['xml']."</sortie>";
  //echo $year."//".$xml;

  // test de sécurité (ne peut arriver en temps normal)
  if ($userId!=$row['authId']) header("Location: archives.php?y=".$year);

  // efface la sortie
  $query='DELETE FROM archives WHERE id="'.$id.'"';
  mysql_query($query,$db) or die("Erreur lors de la suppression d'une archive: ".mysql_error());

  // analyse l'xml pour obtenir les fichiers à effacer
  $document=new DOMDocument();
  $document->loadXML($xml);
  $photos = $document->getElementsByTagName('photo');
  efface($photos);
  $videos = $document->getElementsByTagName('video');
  efface($videos);

  // retour à la page d'archives d'où l'effacement a été fait
  header("Location: archives.php?y=".$year);

function efface($medias) { // DOMNodeList $medias
  global $repStockage;
  foreach ($medias as $media) { // efface chaque fichier
    $fichier=$repStockage."/".$media->getAttribute("fichier");
    unlink($fichier);
    unlink(nomFichierMiniature($fichier));
  }
}
?>
