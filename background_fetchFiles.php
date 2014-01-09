<?php
  // récupère tous les fichiers .jpg du répertoire $_GET['path'] et renvoie un tableau au format JSON

  $dir=$_GET['path'];
  $files=scandir($dir);
  unset($json);
  foreach ($files as $file) {
    if (strcmp(pathinfo($file,PATHINFO_EXTENSION),"jpg")==0) $json[]='"'.$dir.'/'.$file.'"';
  } 

  // renvoie le tableau JSON
  echo '['.implode(',',$json).']';
  //echo '[ "FONDS/AUTO/dsc_3839-reduced.jpg", "FONDS/AUTO/DSC_7981-reduced.jpg", "FONDS/AUTO/DSC_9356-reduced.jpg" ]';
?>

