<?php
session_start();

// test de sécurité 
if ($_SESSION['login']!="root" || !isset($_GET['id'])) header("Location: news.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("head.html"); ?>
<link rel="stylesheet" type="text/css" media="all" href="OUTILS/JSDATEPICK/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="OUTILS/JSDATEPICK/jsDatePick.min.1.3.js"></script>
<script type="text/javascript" src="editer-archive.js"></script>
<script type="text/javascript">
<?php // crée la liste des suggestions de membres (utilisée dans editer-archive.js)
 require("dbconnect.php");
 mysql_query("SET NAMES UTF8");
 $sql = 'SELECT nom FROM membres WHERE id>1';
 $req = mysql_query($sql) or die("erreur lors de la lecture des noms de membres".mysql_error());
 $data = mysql_fetch_array($req);
 echo 'var suggestions=["'.$data['nom'].'"';
 while ($data = mysql_fetch_array($req)) echo ',"'.$data['nom'].'"'; 
 echo '];';
 mysql_free_result($req);
?>
</script>
</head>
<body>
<?php
 if ($_GET['id']==-1) {
   // nouvelle sortie
 } else {
   echo '<h1>Édition de la sortie "'.$_GET['id'].'"</h1>';
 }
 // récupère l'archive sélectionnée
 require("dbconnect.php");
 mysql_query("SET NAMES UTF8");
 $sql = 'SELECT xml FROM archives WHERE id="'.$_GET['id'].'"';
 $req = mysql_query($sql) or die("erreur lors de la lecture de l'archive id=".$_GET['id'].": ".mysql_error());
 $data = mysql_fetch_array($req);

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


