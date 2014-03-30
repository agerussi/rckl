<?php
session_start();
// =========== tests préalables
//teste si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
  header("Location: news.php");
}
// teste si les données sont en place
if (!isset($_SESSION['paiement-informations'])
    || !isset($_SESSION['paiement-selectedList'])
    || !isset($_SESSION['paiement-somme'])) {
  header("Location: frais_nouveau.php");
}

// calcule les nouveaux soldes, la liste des variations 
// données pré-calculées dans frais_validation.php
$informations=$_SESSION['paiement-informations'];
$selectedList=$_SESSION['paiement-selectedList'];
$somme=$_SESSION['paiement-somme'];
$numMembres=count($selectedList);

$chacun=round(100*$somme/$numMembres)/100;
$listevariations=$_SESSION['profilename'].'(+'.$somme.'), ';
for ($i=0; $i<$numMembres; $i++) {
  $id=$informations[$i]['id'];
  $variation=-$chacun;
  $informations[$i]['solde']+=$variation; // nouveau solde
  $listevariations.=$informations[$i]['nom'].'('.$variation.')';
  if ($i!=$numMembres-1) $listevariations.=", ";
}
//echo "debug: listevariations=".$listevariations.'<br/>';

// rajout dans l'historique
require_once("dbconnect.php");
$query="INSERT INTO paiements (date, idAuteur, auteur, somme, variations, commentaire, selected) VALUES(CURDATE()";
$query.=",'".$_SESSION['userid']."'";
$query.=",'".$_SESSION['profilename']."'";
$query.=",".$somme;
$query.=",'".$listevariations."'";
$query.=",'".addslashes($_SESSION['paiement-description'])."'";
$query.=",'".serialize($selectedList)."')";
//echo "debug: query=".$query.'<br/>';
mysql_query($query, $db) or die("erreur lors de l'ajout dans l'historique: ".mysql_error());
idleUpdate($_SESSION['userid']);

// ajout de la transaction au flux rss
// remarque: obsolète, dans le nouveau système avec confirmation, les déclarations ne sont plus annoncées publiquement
require("rss.php");

$item='<item>';
$item.='<title>Déclaration de frais de '.$_SESSION['profilename'].'</title>';
$item.='<link>http://rckl.free.fr</link>';
$item.='<description><![CDATA[';
$item.='Somme déclarée: '.$somme.'€<br />';
$item.='Membres concernés et variations: '.$listevariations.'<br />';
$item.='Description: '.htmlspecialchars($_SESSION['paiement-description'],ENT_QUOTES|ENT_XML1);
$item.= ']]></description>';
$item.='<pubDate>'.date($rssdateformat).'</pubDate>';
$item.='</item>';

rssAdditem($item);
rssUpdate();

// mise à jour des soldes
// les bénéficiaires
for ($i=0; $i<$numMembres; $i++) {
  $query="UPDATE membres SET solde=".$informations[$i]['solde']." WHERE id=".$informations[$i]['id'];
  //echo "debug:".$query.'<br/>';
  mysql_query($query,$db) or die("erreur lors de la mise à jour d'un bénéficiaire: ".mysql_error());
}
// l'auteur
$query="SELECT solde FROM membres WHERE id=".$_SESSION['userid'];
$result=mysql_query($query,$db) or die("erreur lors de la récupération du solde de l'auteur: ".mysql_error());
$ligne=mysql_fetch_array($result) or die("erreur lors de la récupéraion du solde de l'auteur (2): ".mysql_error());
$newsolde=$ligne['solde']+$somme;
$query="UPDATE membres SET solde=".$newsolde." WHERE id=".$_SESSION['userid'];
//echo "debug:".$query.'<br/>';
mysql_query($query,$db) or die("erreur lors de la mise à jour du solde de l'auteur: ".mysql_error());

// annule tout pour éviter des problèmes éventuels
unset($_SESSION['paiement-informations']);
unset($_SESSION['paiement-selectedList']);
unset($_SESSION['paiement-somme']);
header("Location: frais_affichage.php");
?>
