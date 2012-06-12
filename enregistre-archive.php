<?php 
session_start();

// tests de sécurité
if ( !isset($_SESSION['userid']) 
  || ($_SESSION['userid']!=1)
  || !isset($_GET['id'])  // recoit l'id de la sortie en paramètre
) header("Location: news.php?menu");

// examen des variables retournées
/*
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

// la liste des medias (photos/vidéos)

$TypeMedia=array("On"=>1, "Photo"=>2, "Video"=>4, "New"=>8); // énumération des différents types de médias
$repStockage="IMG"; // répertoire de stockage

$idSortie=$_GET["id"]; // identifiant de sortie

$i=0;
while (isset($_POST["typeMedia".$i])) { // parcours de l'ensemble des médias
  $type=$_POST["typeMedia".$i];
  $isNew = $type & $TypeMedia["New"];
  $fichier=$_POST["nomMedia".$i];

  if ($type & $TypeMedia["On"]) { // si le média est sélectionné
    if ($isNew) { 
      $fichier=renommerFichier($idSortie, $fichier); 
      creerMiniature($fichier);
    }
    $commentaire=$_POST["commentaireMedia".$i];
    $xml.='(photo fichier="'.$fichier.'" commentaire="'.$commentaire.'" /)';
  }
  else effaceFichier($fichier, $isNew);
  $i++;
} // fin du parcours

echo $xml;

// crée une miniature dans le répertoire de stockage $IMG
function creerMiniature($fichier) {
  // TODO
}

// choisi un nouveau nom en fonction de l'id de la sortie et des noms existants
// puis renomme le fichier et le place dans le répertoire de stockage ($repStockage)
function renommerFichier($idSortie, $fichier) { // TODO
  // détermine un nouveau nom
   global $repStockage;
   $basePath=$repStockage."/".$idSortie;
   $i=1;
   while (file_exists($newName=$basePath.sprintf("%02d",$i).".jpg")) $i++;

   // $newName contient un nom valide
   echo "rename($fichier,$newName)";

   return $newName;
}

// efface le fichier et sa miniature si le fichier n'est pas nouveau
function effaceFichier($fichier, $isNew) { 
  if ($isNew) { // effacer simplement le fichier
    echo "unlink($fichier)";
  }
  else { // effacer le fichier et sa miniature dans le répertoire de stockage
    global $repStockage;
    $path=$repStockage."/".$fichier;
    $pos=strrpos($path,'.'); // position du . de l'extension
    $pathMini=substr($path,0,$pos)."-mini".substr($path,$pos);
    echo "unlink($path)";
    echo "unlink($pathMini)";
  } 
}
?>
