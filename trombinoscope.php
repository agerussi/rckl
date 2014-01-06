<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php
if (isset($_GET['menu'])) require("menuh.php");
else require("head.html");
?>
</head>

<body>
<?php 
  //require("background.php");
  if (isset($_GET['menu'])) require("menub.php"); 


  echo "<h1>TROMBINOSCOPE</h1>";

  $connected=isset($_SESSION['login']);
  if ($connected) 
    echo '<p>Cliquez sur un nom de membre pour lui envoyer un email.</p>';
  else 
    echo '<p><a href="loginpage.php?target=trombinoscope.php?menu">Connectez-vous</a> pour contacter un membre par email.</p>';

  // requiert une connection à la BD
  require("dbconnect.php");

  // construit le xml du trombinoscope
  $xml='<?xml version="1.0" encoding="utf-8"?>';
  $xml.='<trombinoscopes>';

  // le trombi des actifs
  $sql = "SELECT nom, photo, email FROM membres WHERE trombi='actif';";
  $req = mysql_query($sql) or die("erreur lors de la lecture des membres: ".mysql_error());
  $xml.='<trombinoscope id="actifs">'; 
  $xml.='<path>TROMBI</path>';
  $xml.=buildTrombi($req,true);
  $xml.='</trombinoscope>';
  mysql_free_result($req);

  // le trombi des anciens
  $sql = "SELECT nom, photo, email FROM membres WHERE trombi='ancien';";
  $req = mysql_query($sql) or die("erreur lors de la lecture des membres: ".mysql_error());
  $xml.='<trombinoscope id="anciens">'; 
  $xml.='<path>TROMBI</path>';
  $xml.=buildTrombi($req,false);
  // rajoute les anciens codés 'statiquement'
  $xml.=file_get_contents("trombinoscope-anciens.xml");
  $xml.='</trombinoscope>';
  mysql_free_result($req);

  // fin du trombi
  $xml.='</trombinoscopes>';
  mysql_close();
  //echo $xml;

  // transforme le xml en html
  $xsl = new DOMDocument;
  $xsl->load("trombinoscope.xsl");

  $proc = new XSLTProcessor;
  $proc->importStyleSheet($xsl); 
  echo $proc->transformToXML(new SimpleXMLElement($xml));

// lit la requête et construit l'xml
// effet de bord: $req
function buildTrombi($req, $mail) {
  $connected=isset($_SESSION['login']);
  while ($data = mysql_fetch_array($req)) {
    $xml.='<membre>';
    $xml.='<nom>'.$data['nom'].'</nom>';
    $xml.='<photo>'.$data['photo'].'</photo>';
    if ($mail && $connected && $data['email']!=NULL) $xml.='<email>'.$data['email'].'</email>';
    $xml.='</membre>';
  }
  return $xml;
}
?>
</body>
</html>
