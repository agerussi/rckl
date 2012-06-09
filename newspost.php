<?php
session_start();
// =========== tests préalables
//teste si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
  header("Location: news.php?menu");
}
if (!isset($_POST['newsbody'])) {
  header( "Location: newspost-page.php" );
}

require("dbconnect.php");


//echo 'debug: poste la news suivante:<br/>'.$_POST['newsbody'];
$query = "INSERT INTO news (date, auteur, texte) VALUES(CURDATE(),'".$_SESSION['realname']."','".$_POST['newsbody']."')";
//echo $query;
mysql_query($query, $db) or die("Erreur lors de l'insertion de la news: ".mysql_error());

// efface la news la plus vieille (donc maintient le nb de news constant)
/*
$query = "SELECT id FROM news ORDER BY date";
$result = mysql_query($query, $db) or die("Erreur lors de la lecture de la table de news: ".mysql_error());
$first = mysql_fetch_array($result);
$query = "DELETE FROM news WHERE id=".$first['id'];
//echo $query;
mysql_query($query, $db) or die("Erreur lors de l'effacement de la news la plus ancienne: ".mysql_error());
 */
// fait le ménage dans la table rss (efface les messages datant de plus de 2 mois)
$query="DELETE FROM news WHERE date<DATE_SUB(CURDATE(),INTERVAL 2 MONTH)";
mysql_query($query,$db) or die("Erreur lors de l'effacement des anciennes news: ".mysql_error());

// rajoute la news au flux rss
require("rss.php");

$item='<item>';
$item.='<title>News de '.$_SESSION['realname'].'</title>';
$item.='<link>http://gl.aslslb.free.fr/news.php?menu</link>';
$item.='<description><![CDATA['.$_POST['newsbody'].']]></description>';
$item.='<pubDate>'.date($rssdateformat).'</pubDate>';
$item.='</item>';

rssAdditem($item);
rssUpdate();

header("Location: news.php?menu");
?> 
