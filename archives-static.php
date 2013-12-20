<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php
  if (isset($_GET['menu'])) require("menuh.php"); 
  else require("head.html");
?>
  <script type="text/javascript" src="OUTILS/SLIMBOX/js/mootools.js"></script>
  <script type="text/javascript" src="OUTILS/SLIMBOX/js/slimbox.js"></script>
  <link rel="stylesheet" href="OUTILS/SLIMBOX/css/slimbox.css" type="text/css" media="screen" />
</head>
<body>
<?php
  if (isset($_GET['menu'])) require("menub.php"); 
?>
<h1>ARCHIVES DES SORTIES DU GL</h1>
En cas de problème de lecture des vidéos, essayez <b>un vrai lecteur</b>: <a href="http://www.mplayerhq.hu">MPlayer</a>.<br/>
Pas de source pour votre torrent ? <a href="mailto:rckl@free.fr">envoyez un courrier</a>
<?php
  // le titre en fonction de l'année demandée
 if (isset($_GET['y'])) $year = $_GET['y'];
 else $year = "@home";
 if ($year=="@home") $titre = "À LA MAISON";
 else $titre = "ANNÉE ".$year;
 echo "<h2>".$titre."</h2>";

 // affichage de l'archive
 $xml = new DOMDocument;
 $xml->load("ARCHIVES/archives".$year.".xml");
 $xsl = new DOMDocument;
 $xsl->load("ARCHIVES/archives.xsl");

 $proc = new XSLTProcessor;
 $proc->importStyleSheet($xsl); 
 echo htmlspecialchars_decode($proc->transformToXML($xml));
?>
</body>
</html>
