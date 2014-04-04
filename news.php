<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("menu_header.php"); ?>
</head>
<body>
<?php
require("background.html");
require("menu_body.php"); 
// si quelqu'un est loggé, proposer le rajout d'une news
if (isset($_SESSION['login'])) {
  echo '<p><a href="rcklrss.xml"><img border=0 src="ICONS/RSS-icon.png" class="icon" /></a></p>';
}
?>

<h1>NEWS</h1>

<?php
if (isset($_SESSION['login'])) {
  echo <<<EOL
<p>
<form action="news_input.php">
<input type="submit" value="Poster une news" title=""/>
</form>
</p>
EOL;
}
else echo '<p><a href="loginpage.php?target=news_input.php">Connectez-vous</a> pour poster une news.</p>';
?>

<?php
// on se connecte à la base
require_once("dbconnect.php");
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
  $xml.="<corps><![CDATA[".htmlspecialchars($data['texte'],ENT_QUOTES|ENT_XML1)."]]></corps>";
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
