<?php

$rssdateformat="D, d M Y H:i:s T";

function rssAdditem($item) {
  global $db;
  $query="INSERT INTO rss (date,item) VALUES (CURDATE(),'".$item."')";
  mysql_query($query,$db) or die("Erreur lors de la création du flux rss: ".mysql_error());
}

function rssUpdate() {
  global $db;
  // écrit le fichier rss
  $query="SELECT * FROM rss ORDER BY date DESC";
  $itemlist=mysql_query($query,$db) or die("Erreur lors de l'ouverture de la table rss: ".mysql_error());

  $rssfile=fopen("glrss.xml","w") or die("Erreur lors de l'écriture du flux rss.");
  $header='<?xml version="1.0"?>';
  $header.='<rss version="2.0">';
  $header.='<channel><title>Direct\'GL</title>';
  $header.='<link>http://gl.aslslb.free.fr/</link>';
  $header.='<description>En direct du Groupe Loisirs de Saint-Laurent-Blangy</description>';
  $header.='<language>fr</language>';
  $header.='<pubDate>'.date($rssdateformat).'</pubDate>';
  $header.='<webMaster>gl.aslslb@free.fr</webMaster>';
  fwrite($rssfile,$header);

  while ($item=mysql_fetch_array($itemlist)) fwrite($rssfile,$item['item']);

  $footer='</channel></rss>';
  fwrite($rssfile,$footer);
  fclose($rssfile);

  // fait le ménage dans la table rss (efface les messages datant de plus de 2 mois)
  $query="DELETE FROM rss WHERE date<DATE_SUB(CURDATE(),INTERVAL 2 MONTH)";
  mysql_query($query,$db) or die("Erreur lors de l'effacement d'items anciens: ".mysql_error());
}

