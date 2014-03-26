<?php
session_start();
if (!isset($_SESSION['userid']) 
  || !isset($_GET['ids'])
  || !isset($_POST['datedebut'])
  || !isset($_POST['datefin'])
  || !isset($_POST['deadline'])
) header("Location: calendrier.php");

require_once("magic_quotes_gpc_off.php");
require_once("dbconnect.php");

if ($_GET['ids']==-1) { // nouvelle sortie
  // ajout de la sortie au flux RSS
  require("rss.php");
  $item='<item>';
  $item.='<title>Nouvelle sortie de '.$_SESSION['profilename'].'</title>';
  $item.='<link>http://rckl.free.fr/calendrier.php</link>';
  $item.='<description><![CDATA[';
  $item.='Date départ: '.$_POST['datedebut'].'<br />';
  $item.='Deadline: '.$_POST['deadline'].'<br />';
  $item.='Destination: '.$_POST['destination'].'<br />';
  $item.='Description: '.$_POST['description'];
  $item.= ']]></description>';
  $item.='<pubDate>'.date($rssdateformat).'</pubDate>';
  $item.='</item>';
  rssAdditem($item);
  rssUpdate();

  // annonce de la sortie dans les news
  require_once("news_utils.php");
  $newsbody=$_SESSION['profilename']." propose une nouvelle sortie, ";
  $newsbody.="départ le ".$_POST['datedebut'].", ";
  $newsbody.="destination: ".$_POST['destination'].".";
  insertNews("RCKL",$newsbody);
  cleanNews();

  // query pour l'ajout de la sortie à la base
  $query="INSERT INTO sorties (idresponsable,deadline,datedebut,datefin,destination,objet,modalites) VALUES ";
  $query.="(".$_SESSION['userid'];
  $query.=",'".FtE($_POST['deadline'])."'";
  $query.=",'".FtE($_POST['datedebut'])."'";
  $query.=",'".FtE($_POST['datefin'])."'";
  $query.=",'".addslashes(trim($_POST['destination']))."'";
  $query.=",'".addslashes(trim($_POST['description']))."'";
  $query.=",'".addslashes(trim($_POST['modalites']))."')";
}
else { // modification d'une sortie existante
  $query="UPDATE sorties SET ";
  //$query.="idresponsable=".$_SESSION['userid']; // ne change pas l'organisateur de la sortie (utile si root édite)
  $query.="deadline='".FtE($_POST['deadline'])."'";
  $query.=",datedebut='".FtE($_POST['datedebut'])."'";
  $query.=",datefin='".FtE($_POST['datefin'])."'";
  $query.=",destination='".addslashes(trim($_POST['destination']))."'";
  $query.=",objet='".addslashes(trim($_POST['description']))."'";
  $query.=",modalites='".addslashes(trim($_POST['modalites']))."'";
  $query.=" WHERE id=".$_GET['ids'];
}

mysql_query($query,$db) or die("Erreur lors de la création/modification d'une sortie: ".mysql_error());
//die ($query);
idleUpdate($_SESSION['userid']);

header("Location: calendrier.php");

// helper functions
function FtE($Fdate) {
  sscanf($Fdate,"%u-%u-%u",$jour,$mois,$annee);
  $Edate="$annee-$mois-$jour";
  return $Edate;
}
?>

