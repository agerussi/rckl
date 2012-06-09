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
  if (isset($_GET['menu'])) require("menub.php"); 
  
  // transforme le xml en html
  $xml = new DOMDocument;
  $xml->load("trombinoscope.xml");
  $xsl = new DOMDocument;
  $xsl->load("trombinoscope.xsl");

  $proc = new XSLTProcessor;
  $proc->importStyleSheet($xsl); 
  echo $proc->transformToXML($xml);
?>
</body>
</html>
