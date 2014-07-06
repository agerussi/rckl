<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("menu_header.php"); ?>
</head>

<body>
<?php 
  require("menu_body.php");

  echo "<h1>TROMBINOSCOPE</h1>";

  $connected=isset($_SESSION['login']);
  if ($connected) 
    echo '<p>Cliquez sur un nom pour visualiser son profil.</p>';
  else 
    echo '<p><a href="loginpage.php?target=trombinoscope.php">Connectez-vous</a> pour accéder aux informations sur un membre.</p>';

  // requiert une connection à la BD
  require_once("dbconnect.php");

  // construit le xml du trombinoscope
  $xml='<?xml version="1.0" encoding="utf-8"?>';
  $xml.='<trombinoscopes>';

  // le trombi des actifs
  $sql = "SELECT id, nomprofil FROM membres WHERE id<>1 AND idlesince>DATE_SUB(CURDATE(),INTERVAL 1 YEAR) AND NOT status&".$MEMBER_STATUS_PENDING;
  $req = mysql_query($sql) or die("erreur lors de la lecture des membres: ".mysql_error());
  $xml.='<trombinoscope id="actifs">'; 
  $xml.='<path>TROMBI</path>';
  $xml.=buildTrombi($req);
  $xml.='</trombinoscope>';
  mysql_free_result($req);

  // le trombi des anciens
  $sql = "SELECT id, nomprofil FROM membres WHERE id<>1 AND idlesince<=DATE_SUB(CURDATE(),INTERVAL 1 YEAR) AND NOT status&".$MEMBER_STATUS_PENDING;
  $req = mysql_query($sql) or die("erreur lors de la lecture des membres: ".mysql_error());
  $xml.='<trombinoscope id="anciens">'; 
  $xml.='<path>TROMBI</path>';
  $xml.=buildTrombi($req);
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
function buildTrombi($req) {
  //$connected=isset($_SESSION['login']);
  global $connected;
  while ($data = mysql_fetch_array($req)) {
    $xml.='<membre>';
    $xml.='<nom>'.htmlspecialchars(stripslashes($data['nomprofil'])).'</nom>';
    $xml.='<id>'.$data['id'].'</id>';
    if ($connected) $xml.='<profil/>';
    $xml.='</membre>';
  }
  return $xml;
}
?>
</body>
</html>
