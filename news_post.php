<?php
session_start();
// =========== tests préalables
//teste si l'utilisateur est connecté
if (!isset($_SESSION['userid'])) {
  header("Location: news.php");
}
if (!isset($_POST['newsbody'])) {
  header( "Location: news_input.php" );
}

require_once("magic_quotes_gpc_off.php");
require_once("news_utils.php");
require_once("rss.php");

// insertion de la news
insertNews($_SESSION['profilename'],$_POST['newsbody']);

// fait le ménage dans la table des news 
cleanNews();

// met à jour idlesince
idleUpdate($_SESSION['userid']);

// rajoute la news au flux rss
$item='<item>';
$item.='<title>News de '.$_SESSION['profilename'].'</title>';
$item.='<link>http://rckl.free.fr/news.php</link>';
$item.='<description><![CDATA['.htmlspecialchars(trim($_POST['newsbody']),ENT_XML1).']]></description>';
$item.='<pubDate>'.date($rssdateformat).'</pubDate>';
$item.='</item>';

rssAdditem($item);
rssUpdate();

header("Location: news.php");
?> 
