<?php
// quelques fonctions et constantes utilisées à plusieurs endroits

// transforme un nom de fichier en sa miniature.
// ATTENTION: le fichier doit avoir une extension OU ne contenir aucun point!
function nomFichierMiniature($path) { 
  if ($pos=strrpos($path,'.')) return substr($path,0,$pos)."-mini.jpg";
  else return $path."-mini.jpg";
}

// énumération des différents types de médias
$TypeMedia=array("On"=>1, "Photo"=>2, "Video"=>4, "New"=>8, "Miniature"=>16, "Vimeo"=>32); 

// répertoire de stockage
$repStockage="IMGDB"; 

?>
