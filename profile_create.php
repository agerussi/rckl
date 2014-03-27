<?php 
require_once("magic_quotes_gpc_off.php");
require("profile_common.php");
require_once("dbconnect.php");

// examen des variables retournées
foreach ($_POST as $key => $value) {
    //echo $key."=".$value."<br/>";
}

// traitement des données reçues et création de la requête de sauvegarde
// =====================================================================
$changes=array();
// login
$login=request("login");
array_push($changes,$login);
// motdepasse
$motdepasse=request("motdepasse");
$md5pass=md5($motdepasse);
array_push($changes,$md5pass);
// nom
$nom=request("nom");
array_push($changes,addslashes($nom));
// prénom
$prenom=request("prenom");
array_push($changes,addslashes($prenom));
// nomprofil
$nomprofil=request("nomprofil");
array_push($changes,addslashes($nomprofil));
// datenaissance
$datenaissance=request("datenaissance");
array_push($changes,dateF2E($datenaissance));
// latitude
$latitude=request("latitude");
array_push($changes,$latitude);
// longitude
$longitude=request("longitude");
array_push($changes,$longitude);

// sauvegarde dans la base de données
$query="INSERT INTO membres (login,motdepasse,nom,prenom,nomprofil,datenaissance,latitude,longitude,idlesince) VALUES (";
array_walk($changes,addQuotes);
array_push($changes,"CURDATE()");
$query.=implode(", ",$changes);
$query.=")";
mysql_query($query,$db) or die("Erreur lors de la modification d'un profil: ".mysql_error());
//echo $query;

// crée le fichier photo par défaut
$id=mysql_insert_id(); // ATTENTION: pour BD mySQL seulement
copy("TROMBI/missingM.jpg",photoPath($id));

// ajout de l'archive au flux RSS
require_once("rss.php");
$item="<item>";
$item.="<title>Nouveau membre!</title>";
$item.="<link>http://rckl.free.fr/trombinoscope.php</link>";
$item.="<description><![CDATA[";
$item.=$prenom." ".$nom." (".$nomprofil.") vient de s'inscrire au RCKL.";
$item.="]]></description>";
$item.="<pubDate>".date($rssdateformat)."</pubDate>";
$item.="</item>";
rssAdditem($item);
rssUpdate();

// annonce de la sortie dans les news
require_once("news_utils.php");
$newsbody="Souhaitons la bienvenue à un nouveau membre: ".$prenom." ".$nom." (".$nomprofil.") vient de s'inscrire au RCKL.";
insertNews("RCKL",$newsbody);
cleanNews();

mysql_close($db);

// auto-login 
session_start();
$_SESSION['login']=$login; 
$_SESSION['userid']=$id;
$_SESSION['profilename']=$nomprofil;
header("Location: profile_done.php");

// helper functions
function addQuotes(&$string) {
  $string="'".$string."'";
}

?>

