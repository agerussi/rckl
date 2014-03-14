<?php 
session_start();
require("profile_common.php");

// tests de sécurité
if (!isset($_SESSION['userid'])) header("Location: news.php");

require("dbconnect.php");


// examen des variables retournées
foreach ($_POST as $key => $value) {
    //echo $key."=".$value."<br/>";
}

// traitement anti-magic_quotes_gpc
if (get_magic_quotes_gpc()) {
    function stripslashes_gpc(&$value)
    {
        $value = stripslashes($value);
    }
    array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
}

// traitement des données reçues et création de la requête de sauvegarde
// =====================================================================
$changes=array();
// motdepasse
$motdepasse=request("motdepasse");
if (!empty($motdepasse)) {
  $md5pass=md5($motdepasse);
  array_push($changes,"motdepasse='".$md5pass."'");
}
// photo: uniquement après que la requête de modification ait réussie.
// email
$email=request("email");
array_push($changes,"email='".addslashes($email)."'");
// adresse
$adresse=request("adresse");
array_push($changes,"adresse='".addslashes($adresse)."'");
// téléphone
$telephone=request("telephone");
array_push($changes,"telephone='".addslashes($telephone)."'");
// divers
$divers=request("divers");
array_push($changes,"divers='".addslashes($divers)."'");
// latitude
$latitude=request("latitude");
if (!empty($latitude)) array_push($changes,"latitude='".$latitude."'");
// longitude
$longitude=request("longitude");
if (!empty($longitude)) array_push($changes,"longitude='".$longitude."'");

// sauvegarde dans la base de données
require("dbconnect.php");
$query="UPDATE membres SET ";
$query.=implode(", ",$changes);
$query.=" WHERE id='".$_SESSION['userid']."'";
mysql_query($query,$db) or die("Erreur lors de la modification d'un profil: ".mysql_error());
//echo $query;
idleUpdate($_SESSION['userid']);
mysql_close($db);

// traitement de la photo
if ($_FILES['photo']['error']==UPLOAD_ERR_OK
 && $_FILES['photo']['type']=="image/jpeg"
 && $_FILES['photo']['size']<20*1024  
 && is_uploaded_file($_FILES['photo']['tmp_name'])) {
   $filename=photoPath($_SESSION['userid']);
   if (!move_uploaded_file($_FILES['photo']['tmp_name'],$filename)) die("Erreur lors du chargement de la nouvelle photo.");
}

// retourne sur la page de l'archive modifiée
header("Location: news.php");

?>
