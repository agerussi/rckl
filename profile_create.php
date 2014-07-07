<?php 
require_once("magic_quotes_gpc_off.php");
require_once("profile_common.php");
require_once("dbconnect.php");

// examen des variables retournées
foreach ($_POST as $key => $value) {
    //echo $key."=".$value."<br/>";
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
array_push($changes,dateF2E($datenaissance));
// latitude
$latitude=request("latitude");
array_push($changes,$latitude);
// longitude
$longitude=request("longitude");
array_push($changes,$longitude);
// email de validation
$email=request("email");

// sauvegarde dans la base de données
$query="INSERT INTO membres (login,motdepasse,nom,prenom,nomprofil,datenaissance,latitude,longitude,idlesince,status) VALUES (";
array_walk($changes,addQuotes);
array_push($changes,"CURDATE()");
array_push($changes,$STATUS_PENDING);
$query.=implode(", ",$changes);
$query.=")";
mysql_query($query,$db) or die("Erreur lors de la modification d'un profil: ".mysql_error());
$id=mysql_insert_id(); // ATTENTION: pour BD mySQL seulement
//echo $query;

// crée le fichier photo par défaut
copy("TROMBI/missingM.jpg",photoPath($id));

// crée une entrée de validation dans la table pending
$code=randomString(16);
$query="INSERT INTO pending (idMembre,code) VALUES ('{$id}','{$code}')";
mysql_query($query,$db) or die("Erreur lors de la création d'une entrée de validation: ".mysql_error());
$pid=mysql_insert_id();

// envoie un email pour validation
require_once("mail.php");
$subject="validation de votre inscription au RCKL";
$body="Vous avez solicité une inscription au RCKL, vous devez à présent la confirmer en cliquant sur l'adresse suivante:\n\n";
$body.="http://rckl.free.fr/profile_validation.php?id={$pid}&code={$code}";
$body.="\n\nVous disposez de deux heures pour effectuer cette validation, après quoi votre demande d'inscription sera annulée et vous devrez en faire une nouvelle.";
$body.="\n\nSi un clic de souris sur l'adresse ne déclenche aucune action, vous pouvez également recopier l'adresse complète dans votre navigateur."; 
$body.="\n\nEn cas de problèmes persistants, contactez un administrateur du RCKL à l'adresse rckl@free.fr.";
sendAutoMail($email, $subject, $body);

// helper functions
function addQuotes(&$string) {
  $string="'".$string."'";
}

function randomString($length) {
  $rS="";
  while ($length>0) {
    $rS.=rand(0,9);
    $length--; 
  }
  return $rS;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("menu_header.php"); ?>
</head>
<body>
  <?php require("menu_body.php"); ?>
  <h2><u>Un courrier de validation vient de vous être envoyé à l'adresse <?php echo $email;?>.</u></h2>
  <h4>Vous devriez le recevoir d'ici quelques minutes. 
  Vous disposez d'un délai de deux heures pour effectuer la procédure décrite dans le courrier.
  </h4>
  <form action="news.php">
    <input type="submit" value="J'ai compris!"/>
  </form>
</body>
</html>