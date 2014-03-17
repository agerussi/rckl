<?php
session_start();
require("profile_common.php");

// test de sécurité 
if (!isset($_SESSION['userid'])
  || !isset($_GET['id'])
  ) header("Location: news.php");
$id=$_GET['id'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
 <head>
  <?php require("menu_header.php"); ?>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDyvgizLu1uatxqXBPomR4EHsMDipLin4s&sensor=false&langage=fr&region=FR"></script>
  <script type="text/javascript">
    window.addEventListener("load",displayMap);
    function displayMap() {
      var mapOptions = {
	center: new google.maps.LatLng(displayMap.latitude,displayMap.longitude),
	zoom: 8,
	streetViewControl: false
      }
      var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
      var markerOptions = {
	map: map,
	position: new google.maps.LatLng(displayMap.latitude,displayMap.longitude)
      }
      var marker=new google.maps.Marker(markerOptions);
    } 
  </script>
 </head>
 <body>
<?php require("menu_body.php"); ?>

<?php
  // récupère l'ensemble des infos du profil
  require_once("dbconnect.php");
  $sql = 'SELECT * FROM membres WHERE id="'.$id.'"';
  $req = mysql_query($sql) or die("erreur lors de la lecture du profil: ".mysql_error());
  $data = mysql_fetch_array($req);
  if ($data['id']!=$id) die("erreur: le profil récupéré ne correspond pas.");

  if ($data['needupgrade']=='yes') die("<h2>".$data['nomprofil']." n'a pas encore mis à jour son profil.</h2>");

  // titre de la page
  echo '<h1>Profil de '.$data['nomprofil'].'</h1>';

  // photo
  profileEntry("",
    '<img src="'.photoPath($data['id']).'"/>'
  ); 
  // prénom nom
  profileEntry("Nom véritable",
    stripslashes($data['prenom']),
    " ",
    stripslashes($data['nom'])
  ); 
  // âge
  profileEntry("Âge",
    calcAge($data['datenaissance']),
    ' ans'
  ); 
  // dernière activité
  sscanf($data['idlesince'],"%u-%u-%u",$annee,$mois,$jour);
  profileEntry("Dernière activité",
    $jour.'/'.$mois.'/'.$annee
  ); 
  // solde
  profileEntry("Solde",
    $data['solde'],
    ' €'
  ); 
  // email
  $email=$data['email'];
  if (!empty($email)) {
    profileEntry("E-mail", 
      '<a href="mailto:'.$email.'">'.$email.'</a>'
    );
  }
  // téléphone
  $tel=$data['telephone'];
  if (!empty($tel)) {
    profileEntry("Téléphone", $tel);
  }
  // adresse
  $adresse=$data['adresse'];
  if (!empty($adresse)) {
    profileEntry("Adresse", $adresse);
  }
  // divers
  $divers=$data['divers'];
  if (!empty($divers)) {
    profileEntry("Divers", 
      $divers
    );
  }
  // localisation
  echo implode(array(
    '<script type="text/javascript">',
    'displayMap.latitude=',$data['latitude'],';',
    'displayMap.longitude=',$data['longitude'],';',
    '</script>'
    ));
  profileEntry("Localisation",
    '<div id="map-canvas"></div>'
  ); 
?>
 </body>
</html>

<?php // helper functions
function calcAge($dateString) {
  sscanf($dateString,"%u-%u-%u",$annee,$mois,$jour);
  sscanf(date('Y-n-d'),"%u-%u-%u",$anneeC,$moisC,$jourC);
  $age=$anneeC-$annee;
  if ($moisC<$mois) $age--;
  if ($moisC==$mois && $jourC<$jour) $age--;
  return $age;
}
?>
