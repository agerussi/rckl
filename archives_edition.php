<?php
session_start();

// test de sécurité 
if (!isset($_SESSION['userid']) || !isset($_GET['id'])) header("Location: news.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("head.html"); ?>
<link rel="stylesheet" type="text/css" media="all" href="OUTILS/JSDATEPICK/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="OUTILS/JSDATEPICK/jsDatePick.min.1.3.js"></script>
<script type="text/javascript" src="archives_edition.js"></script>
<script type="text/javascript">
<?php 
// initialisation de certaines variables utilisées dans archives_edition.js

// crée la liste des suggestions de membres 
 require("dbconnect.php");
 $sql = 'SELECT nomprofil FROM membres WHERE id>1';
 $req = mysql_query($sql) or die("erreur lors de la lecture des identifiants de membres".mysql_error());
 $data = mysql_fetch_array($req);
 echo 'var suggestions=["'.$data['nomprofil'].'"';
 while ($data = mysql_fetch_array($req)) echo ',"'.$data['nomprofil'].'"'; 
 echo '];';
 mysql_free_result($req);

 // positionne le drapeau isNewArchive  
 $js="var isNewArchive=";
 $js.=isset($_GET['new']) ? "true;":"false;";
 echo $js;

 // transmet l'id de l'archive
 $js="var idArchive=".$_GET['id'].';';
 echo $js;
?>
</script>
</head>
<body>
<a target="_blank" href="help_archives_edition.php"><img class="helpIcon" src="ICONS/help.png" alt="Icône d'aide" title="Aide pour cette page"/></a>
<?php
  echo "<h1>Édition d'une archive RCKL</h1>";
  // récupère l'archive sélectionnée
  require("dbconnect.php");
  $sql = 'SELECT authId, xml FROM archives WHERE id="'.$_GET['id'].'"';
  $req = mysql_query($sql) or die("erreur lors de la lecture de l'archive id=".$_GET['id'].": ".mysql_error());
  $data = mysql_fetch_array($req);

  // récupère authId pour la sauvegarde future
  $_SESSION['authId']=$data['authId']; 

  // crée le xml de la sortie
  $xmltext="<?xml version=\"1.0\" encoding=\"utf-8\"?>";
  $xmltext.='<editsortie id="'.$_GET['id'].'">';
  $xmltext.="<path>IMGDB</path> <mini>-mini</mini>";
  $xmltext.=$data['xml'];
  $xmltext.="</editsortie>";

  // on libère l'espace mémoire alloué à cette requête
  mysql_free_result($req);
  mysql_close();

  // conversion xml -> html
  $xml = new DOMDocument; 
  $xml->loadXML($xmltext);
  $xsl = new DOMDocument;
  $xsl->load("archives_edit.xsl");
  $proc = new XSLTProcessor;
  $proc->importStyleSheet($xsl); 
  echo htmlspecialchars_decode($proc->transformToXML($xml)); 
?>
</body>
</html>


