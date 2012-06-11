<?php 
session_start();

// tests de sécurité
if ( !isset($_SESSION['userid']) 
  || ($_SESSION['userid']!=1)
) header("Location: news.php?menu");

// récupération des données, création de l'XML
// ===============
$xml="";
// la date
$fdate=$_POST['valeurdate'];
sscanf($fdate,"%u-%u-%u",$jour,$mois,$annee);
$edate="$annee-$mois-$jour"; // date pour la BDD
$xml.="<date>";
$xml.="<jour>".$jour."</jour>";
$xml.="<mois>".$mois."</mois>";
$xml.="<annee>".$annee."</annee>";
$textedate=$_POST['valeurtextedate'];
if (strlen($textedate)>0) $xml.="<texte>".$textedate."</texte>";
$xml.="</date>";

// le titre
$xml.="<titre>".$_POST['valeurtitre']."</titre>";

// le commentaire
$commentaire=$_POST['valeurcommentaire'];
if (strlen($commentaire)>0) $xml.="<commentaire>".$commentaire."</commentaire>";

// les participants
$xml.="<participants>".$_POST['listeparticipants']."</participants>";

// la liste des photos
// examen des variables retournées
foreach ($_POST as $key => $value) {
    echo $key."=".$value."<br/>";
}

//echo $xml;
?>
