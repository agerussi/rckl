<?php 
session_start();
require_once("magic_quotes_gpc_off.php");

// tests de sécurité
if ( !isset($_SESSION['userid']) 
  || !isset($_SESSION['authId'])
  || !isset($_GET['id'])  // recoit l'id de la sortie en paramètre
) header("Location: news.php");
$idSortie=$_GET["id"]; // identifiant de sortie

require_once("dbconnect.php");

/*
// examen des variables retournées
foreach ($_POST as $key => $value) {
    echo $key."=".$value."<br/>";
}
*/
// récupération des données, création de l'XML
// ===============
$xml="";
// la date
$fdate=$_POST['valeurdate'];
sscanf($fdate,"%u-%u-%u",$jour,$mois,$annee);
$edate="$annee-$mois-$jour"; // date pour la BDD
$xml.="<date ";
$xml.='jour="'.$jour.'" ';
$xml.='mois="'.$mois.'" ';
$xml.='annee="'.$annee.'"';
$textedate=htmlspecialchars(trim($_POST['valeurtextedate']),ENT_QUOTES|ENT_XML1);
if (strlen($textedate)>0) $xml.=' texte="'.$textedate.'"';
$xml.="/>";
$xml.="\n";

// le titre
$titre=htmlspecialchars(trim($_POST['valeurtitre']),ENT_QUOTES|ENT_XML1);
if (strlen($titre)!=0) $xml.="<titre><![CDATA[".$titre."]]></titre>";
$xml.="\n";

// l'auteur
$sql = "SELECT nomprofil FROM membres WHERE id='".$_SESSION['authId']."'";
$req = mysql_query($sql) or die("erreur lors de la lecture des archives: ".mysql_error());
$data = mysql_fetch_array($req);
$xml.="<auteur>".$data['nomprofil']."</auteur>";
$xml.="\n";
mysql_free_result($req);

// le commentaire
$commentaire=addslashes(trim($_POST['valeurcommentaire']));
if (strlen($commentaire)!=0) $xml.="<commentaire><![CDATA[".$commentaire."]]></commentaire>";
$xml.="\n";

// les participants
$participants=$_POST['listeparticipants']; // la liste est déjà passée par htmlspecialchars() version JS
if (strlen($participants)!=0) $xml.="<participants>".$participants."</participants>";
$xml.="\n";

// la liste des medias (photos/vidéos)
$xml.=$_POST['xmlmedias'];

// sauvegarde de l'xml dans la base de données
$query="UPDATE archives SET ";
$query.="date='".$edate."'";
$query.=",xml='".$xml."'";
$query.=" WHERE id='".$idSortie."'";
mysql_query($query,$db) or die("Erreur lors de la création/modification d'une archive: ".mysql_error());
//die("<pre>".htmlspecialchars($xml)."</pre>");
idleUpdate($_SESSION['userid']);

if (isset($_GET['new'])) {
  // ajout de l'archive au flux RSS
  require_once("rss.php");
  $item="<item>";
  $item.="<title>Nouvelle archive de ".$_SESSION['profilename']."</title>";
  $item.="<link>http://rckl.free.fr/archives.php?y=".$annee."</link>";
  $item.="<description><![CDATA[";
  $item.="Titre: ".$titre."<br />";
  $item.="Date de l'activité: ".(strlen($textedate)>0 ? $textedate." ".$annee:$fdate);
  $item.="]]></description>";
  $item.="<pubDate>".date($rssdateformat)."</pubDate>";
  $item.="</item>";
  rssAdditem($item);
  rssUpdate();

  // annonce de la sortie dans les news
  require_once("news_utils.php");
  $newsbody=$_SESSION['profilename']." a créé une archive de l'activité intitulée «".trim($_POST['valeurtitre'])."»";
  $newsbody.=" du ".$fdate.".";
  insertNews("RCKL",$newsbody);
  cleanNews();
}

mysql_close($db);
// retourne sur la page de l'archive modifiée
header("Location: archives.php?y=".$annee);
 
?>
