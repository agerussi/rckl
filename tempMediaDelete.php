<?php
session_start();

  if ($_SESSION["login"]!="root" || !isset($_POST["xml"])) header("Location: news.php");

  $xml=$_POST["xml"]; 
  // analyse l'xml pour obtenir les fichiers à effacer
  $document=new DOMDocument();
  $document->loadXML($xml);
  $fichiers = $document->getElementsByTagName("file");
  foreach ($fichiers as $fichier) { // efface chaque fichier
    $path=$fichier->nodeValue;
    unlink($path);
    //echo $path." ";
  }
?>
