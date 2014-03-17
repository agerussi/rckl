<?php
// dbtools: outils AJAX pour la BD
// commandes disponibles:
//
// cmd=loginexists&login=<login>
// renvoie true ou false suivant que le login existe déjà ou non
//
// cmd=profileexists&profile=<login>
// renvoie l'id correspondant, ou 0 suivant que le nomprofil existe déjà ou non
//
// cmd=getuserid
// renvoie l'id de l'utilisateur connecté, ou 0 si personne n'est connecté


if (!isset($_GET['cmd'])) die("dbtools: commande non spécifiée.");

require_once("dbconnect.php");

switch ($_GET['cmd']) {
case "loginexists":
  if (!isset($_GET['login'])) die("dbtools/loginexists: login non spécifié.");
  echo loginexists($_GET['login']);
  break;
case "profileexists":
  if (!isset($_GET['profile'])) die("dbtools/profileexists: profile non spécifié.");
  echo profileexists($_GET['profile']);
  break;
case "getuserid":
  session_start();
  if (isset($_SESSION['userid'])) echo $_SESSION['userid'];
  else echo "0";
  break;
default:
  die("dbtools: commande inconnue.");
}


// teste si le login existe déjà ou non
// renvoie true si le login existe déjà, false sinon
function loginexists($login) {
  $sql = "SELECT EXISTS(SELECT 1 FROM membres WHERE login='".addslashes($login)."')";
  $req = mysql_query($sql) or die("dbtools: erreur lors de la recherche d'un login dans la table des membres.");
  $data = mysql_fetch_array($req);
  return ($data[0]==0) ? "false":"true";
}

function profileexists($profile) {
  $sql = "SELECT id FROM membres WHERE nomprofil='".addslashes($profile)."'";
  $req = mysql_query($sql) or die("dbtools: erreur lors de la recherche d'un nomprofil dans la table des membres.");
  if (mysql_num_rows($req)==0) return 0;
  $data = mysql_fetch_array($req);
  return $data['id'];
}

?>
