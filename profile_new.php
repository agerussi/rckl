<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
 <head>
  <?php require("menu_header.php"); ?>
  <script type="text/javascript">
    var newProfile=true;
  </script>
  <script type="text/javascript" src="profile_edition.js"></script>
 </head>
<body>
 <?php require("menu_body.php"); ?>
<!-- <a target="_blank" href="help_profile_new.php"><img class="helpIcon" src="ICONS/help.png" alt="Icône d'aide" title="Aide pour cette page"/></a> -->

  <h1>Création d'un compte RCKL</h1>

  <p>Pour créer votre nouveau compte au RCKL, il vous faut fournir certains renseignements <em>obligatoires</em> qui sont demandés ci-dessous. 
  Les renseignements marqués d'une étoile * sont <em>définitifs</em>: ils ne pourront pas être modifiés par la suite.
  D'autres informations complémentaires et facultatives vous seront demandées une fois cette étape passée.
  </p>
  <p>Veuillez fournir des informations sérieuses et réelles: le RCKL est un réseau de proximité, dans lequel les gens se rencontrent vraiment et batissent des relations basées sur la confiance.
  Aussi tout compte dont les informations sont douteuses sera supprimé. 
  </p>

  <form accept-charset="utf-8" method="post" action="profile_create.php" id="profileForm">

<?php
  // création du formulaire 
  profileEntry("Login*", commentaire("Votre login ne sert qu'à vous connecter, personne d'autre que vous n'en aura connaissance"),champTexte("login",10,""),message("login"));
  profileEntry("Mot de passe", password(), message("passwd")); 
  profileEntry("Nom*", champTexte("nom",16,""),message("nom"));
  profileEntry("Prénom*", champTexte("prenom",16,""),message("prenom"));
  profileEntry("Identifiant*", commentaire("Ceci est le nom sous lequel vous apparaissez aux autres membres. Vous pouvez modifier la proposition automatique ci-dessous, mais choisissez quelque chose ressemblant à vos véritables nom et prénom."),champTexte("nomprofil",16,""),message("nomprofil"));
  //profileEntry("Date de Naissance*", ..., message("datenaissance"));
?>
    <input type="button" id="submitbutton" value="Créer le compte" title="Relisez bien les informations avant de créer!" />
  </form>

  <form method="post" action="news.php">
    <input type="submit" name="cancel" value="Annuler" title="Annuler la création du compte" />
  </form>
 </body>
</html>

<?php // helper functions

function fileChooser() {
  return implode([
    '<input type="hidden" name="MAX_FILE_SIZE" value="20480" />',
    '<input type="file" id="fileChooser" name="photo" style="display:none"/>'
  ]);
}

function message($name) {
  return '<span id="'.$name.'-message" class="profilemessage"></span>';
}

function zoneTexte($name,$cols,$rows,$value) {
  return implode([
    '<textarea name="'.$name.'"', 
    ' cols="'.$cols.'"',
    ' rows="'.$rows.'">',
    $value,
    '</textarea>'
  ]);
}

function photo($path) { 
  return '<img id="photo" src="TROMBI/'.$path.'" title="cliquer pour modifier votre photo"/>';
}

function password() {
  return '<input id="motdepasse" name="motdepasse" type="password" size="20" value=""/>';
}

function profileEntry($label) {
  echo '<div class="profileentry">';
  echo '<label>'.$label.'</label>';
  for ($i=1; $i<func_num_args(); $i++) echo func_get_arg($i);
  echo '</div>'.PHP_EOL;
}

function valeur($text) {
  return '<span class="valeur">'.stripslashes($text).'</span>';
}

function commentaire($text) {
  return '<span class="commentaire">'.$text.'</span>';
}

function champTexte($name,$len,$value) {
  return '<input type="text" size="'.$len.'" name="'.$name.'" id="'.$name.'" value="'.stripslashes($value).'"/>';
}
?>
