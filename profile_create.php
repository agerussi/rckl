<?php 
session_start();
require("profile_common.php");
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
array_push($changes,$login);
// motdepasse
$motdepasse=request("motdepasse");
$md5pass=md5($motdepasse);
array_push($changes,$md5pass);
// nom
$nom=request("nom");
array_push($changes,addslashes($nom));
// prénom
$prenom=request("prenom");
array_push($changes,addslashes($prenom));
// nomprofil
$nomprofil=request("nomprofil");
array_push($changes,addslashes($nomprofil));
// datenaissance
$datenaissance=request("datenaissance");
array_push($changes,FtE($datenaissance));
// latitude
$latitude=request("latitude");
array_push($changes,$latitude);
// longitude
$longitude=request("longitude");
array_push($changes,$longitude);

// sauvegarde dans la base de données
require("dbconnect.php");
mysql_query("SET NAMES UTF8");
$query="INSERT INTO membres (login,motdepasse,nom,prenom,nomprofil,datenaissance,latitude,longitude) VALUES (";
array_walk($changes,addQuotes);
$query.=implode(", ",$changes);
$query.=")";
mysql_query($query,$db) or die("Erreur lors de la modification d'un profil: ".mysql_error());
echo $query;

// crée le fichier photo par défaut
$id=mysql_insert_id(); // ATTENTION: pour BD mySQL seulement
copy("TROMBI/missingM.jpg",photoPath($id));
mysql_close($db);

// retourne sur la page de l'archive modifiée
//header("Location: news.php");

// helper functions
function request($field) {
  if (!isset($_POST[$field])) die("Erreur lors de la création du profil: le champ ".$field." n'est pas défini.");
  return $_POST[$field];
}

function addQuotes(&$string) {
  $string="'".$string."'";
}

function FtE($Fdate) {
  sscanf($Fdate,"%u-%u-%u",$jour,$mois,$annee);
  $Edate="$annee-$mois-$jour";
  return $Edate;
}
?>
