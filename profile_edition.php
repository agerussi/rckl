<?php
session_start();
require("profile_common.php");

// test de sécurité 
if (!isset($_SESSION['userid'])) header("Location: news.php");
$id=$_SESSION['userid'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
 <head>
  <?php require("menu_header.php"); ?>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDyvgizLu1uatxqXBPomR4EHsMDipLin4s&sensor=false&langage=fr&region=FR"></script>
  <script type="text/javascript">
    var newProfile=false;
    var upgradeProfile=false;
  </script>
  <script type="text/javascript" src="profile_edition.js"></script>
 </head>
<body>
 <?php require("menu_body.php"); ?>
<!-- <a target="_blank" href="help_profile_edition.php"><img class="helpIcon" src="ICONS/help.png" alt="Icône d'aide" title="Aide pour cette page"/></a> -->
  <h1>Édition de votre profil personnel</h1>

<p>
Les données ci-dessous, à l'exception de celles marquées par une étoile *, sont facultatives et peuvent être modifiées à tout moment.
</p>

  <form accept-charset="utf-8" encType="multipart/form-data" method="post" action="profile_save.php" id="profileForm">

<?php
  // récupère l'ensemble des infos du profil
  require_once("dbconnect.php");
  $sql = 'SELECT * FROM membres WHERE id="'.$id.'"';
  $req = mysql_query($sql) or die("erreur lors de la lecture du profil: ".mysql_error());
  $data = mysql_fetch_array($req);
  if ($data['id']!=$id) die("erreur: le profil récupéré ne correspond pas.");

  // création du formulaire pré-rempli
  profileEntry("Mot de passe*", 
    commentaire("Ne remplissez ce champ que si vous désirez <em>modifier</em> votre mot de passe, sinon laissez-le vierge."),
    '<input id="motdepasse" name="motdepasse" type="password" size="20" value=""/>',
    message("passwd")
  ); 
  profileEntry("Photo",
    commentaire("Dans l'idéal la photo devrait être au format 3:4 (par exemple: 150x200). Taille maximale: 20KB"),
    photo(photoPath($data['id'])),
    '<input type="hidden" name="MAX_FILE_SIZE" value="20480" />',
    '<input type="file" id="fileChooser" name="photo" style="display:none"/>',
    message("photo")
  ); 
  profileEntry("E-mail", 
    champTexte("email",25,$data['email'])
  );
  profileEntry("Ville ou adresse", 
    champTexte("adresse",30,$data['adresse'])
  );
  profileEntry("Téléphone", 
    champTexte("telephone",16,$data['telephone'])
  );
  profileEntry("Renseignements divers", 
    zoneTexte("divers",80,6,$data['divers'])
  ); 
  profileEntry("Coordonnées GPS*",
    commentaire("Placez le pointeur <em>grosso modo</em> sur votre domicile avec un clic-droit. Une précision «au kilomètre» est suffisante."),
    '<div id="map-canvas"></div>',
    message("gps"),
    '<input id="latitude" name="latitude" type="text" size="10" style="display:none">',
    '<input id="longitude" name="longitude" type="text" size="10" style="display:none">'
  ); 

  // communique la latitude et longitude actuelle
  echo implode(array(
    '<script type="text/javascript">',
    'latitude=',$data['latitude'],';',
    'longitude=',$data['longitude'],';',
    '</script>'
  ));
?>
    <input type="button" id="submitbutton" value="Modifier" title="sauvegarder les changements" />
  </form>
  <form method="post" action="news.php">
    <input type="submit" name="cancel" value="Annuler" title="Annuler tous les changements" />
  </form>
  
 </body>
</html>

<?php // helper functions
function photo($path) { 
  return '<img id="photo" src="'.$path.'" title="cliquer pour modifier votre photo"/>';
}

function zoneTexte($name,$cols,$rows,$value) {
  return implode(array(
    '<textarea name="'.$name.'"', 
    ' cols="'.$cols.'"',
    ' rows="'.$rows.'">',
    $value,
    '</textarea>'
  ));
}

?>
