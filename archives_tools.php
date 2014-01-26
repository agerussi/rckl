<?php
// mode photo:
//   transforme un fichier photo fraîchement uploadé (dans tmp)
//   en un fichier au nom correct et sa miniature
// mode video:
//   idem mais simple copie de la miniature par défaut
// arguments:
// mode = photo ou video
// name = chemin du fichier temporaire
// ext = extension du fichier à créer
// pre = préfixe du fichier à créer

if (isset($_GET['mode'])) $mode=$_GET['mode'];
else die("argument 'mode' is missing.");
if (isset($_GET['name'])) $name=$_GET['name'];
else die("argument 'name' is missing.");
if (isset($_GET['ext'])) $ext=$_GET['ext'];
else die("argument 'ext' is missing.");
if (isset($_GET['pre'])) $pre=$_GET['pre'];
else die("argument 'pre' is missing.");

// cherche le nouveau nom
$nouveauNom=nouveauNomFichier($pre, $ext); 
// renomme le fichier temporaire
rename($name,$nouveauNom); 
if ($mode=="photo") { // crée la miniature 
  creerMiniature($nouveauNom);
}
if ($mode=="video") { // copie la miniature
  copy("ICONS/video-default-mini.jpg",nomFichierMiniature($nouveauNom));
}
// renvoie le nouveau nom, mais sans le chemin
echo basename($nouveauNom);

////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
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

// cherche le premier nom libre sous la forme pre-XX.$ext
function nouveauNomFichier($pre,$ext) { 
   $i=0;
   do {
     $i++;
     $nouveauNom = $pre.sprintf("%02d",$i).".".$ext;
   }
   while (file_exists($nouveauNom));

   return $nouveauNom;
}

// transforme un nom de fichier en sa miniature.
// ATTENTION: le fichier doit avoir une extension OU ne contenir aucun point!
function nomFichierMiniature($path) { 
  if ($pos=strrpos($path,'.')) return substr($path,0,$pos)."-mini.jpg";
  else return $path."-mini.jpg";
}

