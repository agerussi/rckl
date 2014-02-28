<?php // fonctions communes aux fichiers profile_*

// le fichier photo associé à un id
function photoPath($id) {
  return "TROMBI/photo-".$id.".jpg";
}

function message($name) {
  return '<span id="'.$name.'-message" class="profilemessage"></span>';
}

function profileEntry($label) {
  echo '<div class="profileentry">';
  echo '<label>'.$label.'</label>';
  for ($i=1; $i<func_num_args(); $i++) echo func_get_arg($i).PHP_EOL;
  echo '</div>'.PHP_EOL;
}


function commentaire($text) {
  return '<span class="commentaire">'.$text.'</span>';
}

function champTexte($name,$len,$value) {
  return '<input type="text" size="'.$len.'" name="'.$name.'" id="'.$name.'" value="'.stripslashes($value).'"/>';
}

function request($field) {
  if (!isset($_POST[$field])) die("Erreur: le champ ".$field." n'est pas défini.");
  return $_POST[$field];
}

function FtE($Fdate) {
  sscanf($Fdate,"%u-%u-%u",$jour,$mois,$annee);
  $Edate="$annee-$mois-$jour";
  return $Edate;
}
?>
