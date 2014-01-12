<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php
  require("menuh.php"); 
?>
  <script type="text/javascript" src="OUTILS/jQuery.js"></script>
  <script type="text/javascript" src="OUTILS/slimbox/js/slimbox2.js"></script>
  <link rel="stylesheet" href="OUTILS/slimbox/css/slimbox2.css" type="text/css" media="screen" />
  <script type="text/javascript">
    function areYouSure(id) {
      if (confirm("Effacement de l'archive !\n\n Êtes-vous sûr(e) ?")) window.location.replace("effacerarchive.php?id="+id);
    }
  </script>
</head>
<body>
<?php
 require("menub.php"); 
  
  // le titre en fonction de l'année demandée
 if (isset($_GET['y'])) $year = $_GET['y'];
 else $year = "@home";
 if ($year=="@home") {
    $titre = "À LA MAISON";
    $year= "1973"; // année de la sortie spéciale "@home"
 }
 else $titre = "ANNÉE ".$year;
 echo "<h1>ARCHIVES DES ACTIVITÉS DU RCKL</h1>";
 echo "<h2>".$titre."</h2>";

 // récupère les archives de l'année sélectionnée
 require("dbconnect.php");
 mysql_query("SET NAMES UTF8");
 $sql = 'SELECT id, authId, xml FROM archives WHERE DATE_FORMAT(date,"%Y")='.$year;
 $req = mysql_query($sql) or die("erreur lors de la lecture des archives: ".mysql_error());

  // collecte les sorties au format xml
  $xmltext="<?xml version=\"1.0\" encoding=\"utf-8\"?>";
  $xmltext.="<archive>";
  $xmltext.="<path>IMGDB</path> <mini>-mini</mini>";

  while ($data = mysql_fetch_array($req)) {
    $xmltext.="<sortie ";
    $xmltext.='id="'.$data['id'].'"';
    $editable=(isset($_SESSION['userid']) && $_SESSION['userid']==$data['authId']);
    $xmltext.=' edit="'.($editable ? "yes":"no").'"';
    $xmltext.=">";  
    $xmltext.=$data['xml'];
    $xmltext.="</sortie>";
  }
  $xmltext.="</archive>";

  // on libère l'espace mémoire alloué à cette requête
  mysql_free_result($req);
  // on ferme la connexion à la base de données
  mysql_close();

  // conversion xml -> html
  $xml = new DOMDocument; 
  $xml->loadXML($xmltext);
  $xsl = new DOMDocument;
  $xsl->load("archives_display.xsl");
  $proc = new XSLTProcessor;
  $proc->importStyleSheet($xsl); 
  echo htmlspecialchars_decode($proc->transformToXML($xml)); 
?>
</body>
</html>
