<?php 
session_start();

// tests de sécurité
if (!isset($_SESSION['userid'])) header("Location: news.php");

require("dbconnect.php");


// examen des variables retournées
foreach ($_POST as $key => $value) {
    echo $key."=".$value."<br/>";
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
// login
$login=request("login");
if (!empty($login)) array_push($changes,"login='".$login."'");
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

// sauvegarde dans la base de données
require("dbconnect.php");
mysql_query("SET NAMES UTF8");
$query="UPDATE membres SET ";
$query.=implode(", ",$changes);
$query.=" WHERE id='".$_SESSION['userid']."'";
mysql_query($query,$db) or die("Erreur lors de la création/modification d'une sortie: ".mysql_error());
//echo $query;
mysql_close($db);

// traitement de la photo

// retourne sur la page de l'archive modifiée
header("Location: news.php");

// helper functions
function request($field) {
  if (!isset($_POST[$field])) die("Erreur lors de la sauvegarde du profil: le champ ".$field." n'est pas défini.");
  return $_POST[$field];
}

?>
