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
require("background.html");
if (isset($_GET['menu'])) require("menub.php"); 
// si quelqu'un est loggé, proposer le rajout d'une news
if (isset($_SESSION['login'])) {
  echo '<p><a href="glrss.xml"><img border=0 src="ICONS/RSS-icon.png" /></a></p>';
}
?>

<h1>NEWS</h1>

<?php
// on se connecte à la base
require("dbconnect.php");
//
$sql = 'SELECT date, auteur, texte FROM news ORDER BY date DESC;';
$req = mysql_query($sql) or die("erreur lors de la lecture des news: ".mysql_error());

$xml="<newslist>";
while ($data = mysql_fetch_array($req)) {
// on décompose la date
  sscanf($data['date'], "%4s-%2s-%2s", $an, $mois, $jour);

  $xml.="<news>";
  $xml.="<date>".$jour."/".$mois."/".($an%100)."</date>";
  $xml.="<auteur>".$data['auteur']."</auteur>";
  $xml.="<corps>".$data['texte']."</corps>";
  $xml.="</news>";
}
$xml.="</newslist>";
// on libère l'espace mémoire alloué à cette requête
mysql_free_result($req);
// on ferme la connexion à la base de données
mysql_close();

// conversion xml -> html
$proc = new XSLTProcessor;
$xsl = new DOMDocument;
$xsl->load("news.xsl");
$proc->importStyleSheet($xsl); 
echo htmlspecialchars_decode($proc->transformToXML(new SimpleXMLElement($xml))); 
?>
</body>
</html>
