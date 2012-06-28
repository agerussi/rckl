<?php 
session_start();

// tests de sécurité
if ( !isset($_SESSION['userid']) 
  || ($_SESSION['userid']!=1)
  || !isset($_GET['id'])  // recoit l'id de la sortie en paramètre
) header("Location: news.php?menu");

require("dbconnect.php");

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
$xml.='annee="'.$annee.'" ';
$textedate=$_POST['valeurtextedate'];
if (strlen($textedate)>0) $xml.='texte="'.$textedate.'"';
$xml.="/>";

// le titre
$titre=trim($_POST['valeurtitre']);
if (strlen($titre)!=0) $xml.="<titre>".$titre."</titre>";

// le commentaire
$commentaire=$_POST['valeurcommentaire'];
if (strlen($commentaire)!=0) $xml.="<commentaire>".$commentaire."</commentaire>";

// les participants
$participants=$_POST['listeparticipants'];
if (strlen($participants)!=0) $xml.="<participants>".$participants."</participants>";

// la liste des medias (photos/vidéos)
require("helper.php");

$idSortie=$_GET["id"]; // identifiant de sortie

$i=0;
while (isset($_POST["typeMedia".$i])) { // parcours de l'ensemble des médias
  $type=$_POST["typeMedia".$i];
  $isNew = $type & $TypeMedia["New"];
  $isMin = $type & $TypeMedia["Miniature"];
  $fichier=getFileName($_POST["nomMedia".$i]);
  $extension=getExtension($_POST["nomMedia".$i]);

  if ($type & $TypeMedia["Photo"]) { // c'est une photo
    if ($type & $TypeMedia["On"]) { // si le média est sélectionné
      if ($isNew) { 
	// établit le nouveau nom du fichier photo
	$nouveauNom=nouveauNomFichier($idSortie,$extension); 
	$path=$repStockage."/".$nouveauNom; // chemin complet
	// renomme le fichier photo temporaire
	rename($fichier,$path);
	$fichier=$nouveauNom;
	//creer1080p($path); 
	// crée la miniature de la photo
	creerMiniature($path);
      }
      // création de l'XML
      $xml.='<photo fichier="'.$fichier.'" ';
      $commentaire=trim($_POST["commentaireMedia".$i]);
      $xml.=(strlen($commentaire)==0) ? "/>" : 'commentaire="'.$commentaire.'" />';
    }
    else effaceFichier($fichier, $isNew);
  }
  if ($type & $TypeMedia["Video"]) { // c'est une vidéo
    if ($type & $TypeMedia["On"]) { // si le média est sélectionné
      if ($isNew) { // crée le nouveau fichier vidéo
	$nouveauNom=nouveauNomFichier($idSortie."-video",$extension); 
	$path=$repStockage."/".$nouveauNom; 
	rename($fichier,$path); 
	$fichier=$nouveauNom;
      } 
      else $path=$repStockage."/".$fichier; 
      // gestion de la miniature
      if ($isNew || $isMin) { // la miniature doit être créée/changée 
	if ($_FILES["ajoutMiniature".$i]['size']==0) // pas de fichier uploadé, copie la miniature par défaut
	  copy("IMG/video-default-mini.jpg",nomFichierMiniature($path));
	else // récupère la miniature sélectionnée
	  move_uploaded_file($_FILES["ajoutMiniature".$i]['tmp_name'], nomFichierMiniature($path)) or die("erreur lors de la récupération d'une miniature");
      } 
      // création de l'XML
      $xml.='<video fichier="'.$fichier.'"';
      $vimeo=trim($_POST["vimeo".$i]);
      if (strlen($vimeo)!=0) $xml.=' vimeo="'.$vimeo.'"';
      $commentaire=trim($_POST["commentaireMedia".$i]);
      $xml.=(strlen($commentaire)==0) ? "/>" : ' commentaire="'.$commentaire.'" />';
    }
    else effaceFichier($fichier, $isNew);
  }
  $i++;
} // fin du parcours

// sauvegarde de l'xml dans la base de données
mysql_query("SET NAMES UTF8");
$query="UPDATE archives SET ";
$query.="date='".$edate."'";
$query.=",xml='".$xml."'";
$query.=" WHERE id='".$idSortie."'";
mysql_query($query,$db) or die("Erreur lors de la création/modification d'une sortie: ".mysql_error());
mysql_close($db);

// retourne sur la page de l'archive modifiée
header("Location: archives.php?menu&y=".$annee);
 
// #################### HELPER FUNCTIONS
// #####################################

function getFileName($chaine) { // récupère la partie "nom" dans une chaine de type "ext/nom"
 $n=strpos($chaine, "/");
 return substr($chaine,$n+1); 
}

function getExtension($chaine) { // récupère la partie "ext" dans une chaine de type "ext/nom"
 $n=strpos($chaine, "/");
 return substr($chaine,0,$n); 
}

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

// cherche le premier nom libre sous la forme idsortieXX.$ext
function nouveauNomFichier($idSortie,$ext) { 
  // détermine un nouveau nom
   global $repStockage;
   $i=0;
   do {
     $i++;
     $nouveauNom = $idSortie.sprintf("%02d",$i).".".$ext;
   }
   while (file_exists($repStockage."/".$nouveauNom));

   return $nouveauNom;
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

?>
