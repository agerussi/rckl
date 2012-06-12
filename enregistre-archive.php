<?php 
session_start();

// tests de sécurité
if ( !isset($_SESSION['userid']) 
  || ($_SESSION['userid']!=1)
  || !isset($_GET['id'])  // recoit l'id de la sortie en paramètre
) header("Location: news.php?menu");

require("dbconnect.php");

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
      //creer1080p($fichier); 
      creerMiniature($fichier);
    }
    $xml.='<photo fichier="'.$fichier.'" ';
    $commentaire=trim($_POST["commentaireMedia".$i]);
    $xml.=(strlen($commentaire)==0) ? "/>" : 'commentaire="'.$commentaire.'" />';
  }
  else effaceFichier($fichier, $isNew);
  $i++;
} // fin du parcours

// sauvegarde de l'xml dans la base de données
$query="UPDATE archives SET ";
$query.="date='".$edate."'";
$query.=",xml='".$xml."'";
$query.=" WHERE id='".$idSortie."'";
mysql_query($query,$db) or die("Erreur lors de la création/modification d'une sortie: ".mysql_error());
mysql_close($db);

// retourne sur la page de l'archive modifiée
header("Location: _archives.php?menu&y=".$annee);

// #################### HELPER FUNCTIONS
// #####################################

// crée l'image en taille standard 1080p
function creer1080p($fichier) { // TODO: cette fonction plante sur un problème de taille mémoire...
  // calcul des nouvelles dimensions
  $standardHeight = 1080; // hauteur des miniatures
  list($width, $height) = getimagesize($fichier);
  if ($height<=$standardHeight) return; // ne touche pas l'image si elle est de taille inférieure
  $standardWidth = $width*$standardHeight/$height;

  // Redimensionnement
  $image_p = imagecreatetruecolor($standardWidth, $standardHeight);
  $image = imagecreatefromjpeg($fichier);
  imagecopyresampled($image_p, $image, 0, 0, 0, 0, $standardWidth, $standardHeight, $width, $height);
  imagedestroy($image);

  // écrasement
  imagejpeg($image_p, $fichier, 80);
  imagedestroy($image_p);
}

// crée la miniature associée à l'image $fichier
function creerMiniature($fichier) {
  // calcul des nouvelles dimensions
  $miniHeight = 96; // hauteur des miniatures
  list($width, $height) = getimagesize($fichier);
  $miniWidth = $width*$miniHeight/$height;

  // Redimensionnement
  $image_p = imagecreatetruecolor($miniWidth, $miniHeight);
  $image = imagecreatefromjpeg($fichier);
  imagecopyresampled($image_p, $image, 0, 0, 0, 0, $miniWidth, $miniHeight, $width, $height);
  imagedestroy($image);

  // sauvegarde
  imagejpeg($image_p, nomFichierMiniature($fichier), 75);
  imagedestroy($image_p);
}

// choisi un nouveau nom en fonction de l'id de la sortie et des noms existants
// puis renomme le fichier et le place dans le répertoire de stockage ($repStockage)
function renommerFichier($idSortie, $fichier) { 
  // détermine un nouveau nom
   global $repStockage;
   $basePath=$repStockage."/".$idSortie;
   $i=1;
   while (file_exists($newName=$basePath.sprintf("%02d",$i).".jpg")) $i++;

   // $newName contient un nom valide
   rename($fichier,$newName);

   return $newName; 
}

// efface le fichier et sa miniature si le fichier n'est pas nouveau
function effaceFichier($fichier, $isNew) { 
  if ($isNew) { // effacer simplement le fichier
    unlink($fichier);
  }
  else { // effacer le fichier et sa miniature dans le répertoire de stockage
    global $repStockage;
    $path=$repStockage."/".$fichier;
    $pathMini=nomFichierMiniature($path);
    unlink($path);
    unlink($pathMini);
  } 
}

// transforme un nom de fichier en sa miniature.
// ATTENTION: le fichier doit avoir une extension OU ne contenir aucun point!
function nomFichierMiniature($path) { 
  if ($pos=strrpos($path,'.')) return substr($path,0,$pos)."-mini.jpg";
  else return $path."-mini.jpg";
}
?>
