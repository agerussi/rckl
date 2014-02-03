<?php 
session_start();

// tests de sécurité
if ( !isset($_SESSION['userid']) 
  || !isset($_GET['id'])  // recoit l'id de la sortie en paramètre
) header("Location: news.php");
$idSortie=$_GET["id"]; // identifiant de sortie

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
$xml.="<auteur>".$_SESSION['realname']."</auteur>";
$xml.="\n";

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
require("dbconnect.php");
mysql_query("SET NAMES UTF8");
$query="UPDATE archives SET ";
$query.="date='".$edate."'";
$query.=",xml='".$xml."'";
$query.=" WHERE id='".$idSortie."'";
mysql_query($query,$db) or die("Erreur lors de la création/modification d'une sortie: ".mysql_error());
//die("<pre>".htmlspecialchars($xml)."</pre>");
mysql_close($db);

// retourne sur la page de l'archive modifiée
header("Location: archives.php?y=".$annee);
 
?>
