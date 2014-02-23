<?php
session_start();

// test de sécurité 
if (!isset($_SESSION['userid'])) header("Location: news.php");
$id=$_SESSION['userid'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
 <head>
  <?php require("menu_header.php"); ?>
  <script type="text/javascript" src="profile_edition.js"></script>
 </head>
<body>
 <?php require("menu_body.php"); ?>
<!-- <a target="_blank" href="help_profile_edition.php"><img class="helpIcon" src="ICONS/help.png" alt="Icône d'aide" title="Aide pour cette page"/></a> -->
  <h1>Édition de votre profil personnel</h1>

  <form accept-charset="utf-8" encType="multipart/form-data" method="post" action="profile_save.php" id="profileForm">

<?php
  // récupère l'ensemble des infos du profil
  require("dbconnect.php");
  mysql_query("SET NAMES UTF8");
  $sql = 'SELECT * FROM membres WHERE id="'.$id.'"';
  $req = mysql_query($sql) or die("erreur lors de la lecture du profil: ".mysql_error());
  $data = mysql_fetch_array($req);
  if ($data['id']!=$id) die("erreur: le profil récupéré ne correspond pas.");

  // création du formulaire pré-rempli
  profileEntry("Login", champTexte("login",10,$data['login']),message("login"));
  profileEntry("Mot de passe", password(), message("passwd")); 
  //profileEntry("Nom", valeur($data['nom']));
  //profileEntry("Prénom", valeur($data['prenom']));
  //profileEntry("Date de Naissance", valeur($data['datenaissance']));
  //profileEntry("Nom de profil", valeur($data['nomprofil']));
  //profileEntry("Solde", valeur($data['solde']),"€");
  profileEntry("Photo", photo($data['photo']), fileChooser(), message("photo")); 
  profileEntry("E-mail", champTexte("email",25,$data['email']));
  profileEntry("Ville ou adresse", champTexte("adresse",30,$data['adresse']));
  profileEntry("Téléphone", champTexte("telephone",16,$data['telephone']));
  profileEntry("Renseignements divers", zoneTexte("divers",80,6,$data['divers'])); 
?>
    <input type="button" id="submitbutton" value="Modifier" title="sauvegarder les changements" />
  </form>
  <form method="post" action="news.php">
    <input type="submit" name="cancel" value="Annuler" title="Annuler tous les changements" />
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
  echo '</div>';
}

function valeur($text) {
  return '<span class="valeur">'.stripslashes($text).'</span>';
}

function champTexte($name,$len,$value) {
  return '<input type="text" size="'.$len.'" name="'.$name.'" id="'.$name.'" value="'.stripslashes($value).'"/>';
}
?>
