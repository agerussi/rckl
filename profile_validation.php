<?php
require_once("dbconnect.php");
require_once("profile_common.php");


// 1) efface les comptes et les validations périmées
$query="SELECT id, idMembre FROM pending WHERE TIMESTAMPDIFF(HOUR,date,NOW())>=2";
$sqlData=mysql_query($query,$db) or die("Erreur lors de la recherche des entrées pending périmées: ".mysql_error());
while ($data=mysql_fetch_array($sqlData)) {
  deleteMember($data['idMembre']);
  deletePending($data['id']); 
}

// 2) examine les paramètres de la demande de validation
if (    !isset($_GET['id'])
     || !isset($_GET['code'])
   ) die();
$pid=$_GET['id'];
$code=$_GET['code'];

// 3) récupère les données dans la table pending et procède à la validation
$query="SELECT idMembre, code FROM pending WHERE id={$pid}";
$sqlData=mysql_query($query,$db) or die("Erreur lors de la récupération d'une validation: ".mysql_error());
$ok=(mysql_num_rows($sqlData)==1); // on doit avoir récupéré exactement une validation
if ($ok) {
  $uniqueRow=mysql_fetch_array($sqlData);
  if ($uniqueRow['code']==$code) { // le code de vérification est exact
    validateMember($uniqueRow['idMembre']);
    deletePending($pid);
  }
  else $ok=false;
}

// 4) arrête tout si pas de validation
if (!$ok) die("La validation a échoué: veuillez vérifier l'adresse utilisée, recommencer, ou contacter un administrateur.");

// 5) récupère nom, prenom et nomprofil du nouveau membre
$query="SELECT id, login, nom, prenom, nomprofil FROM membres WHERE id={$uniqueRow['idMembre']}";
$sqlData=mysql_query($query,$db) or die("Erreur lors de la récupération des infos d'un membre : ".mysql_error());
$row=mysql_fetch_array($sqlData);
$id=$row['id'];
$login=$row['login'];
$nom=$row['nom'];
$prenom=$row['prenom'];
$nomprofil=$row['nomprofil'];

// 6) annonce l'inscription du nouveau membre
// ajout de l'archive au flux RSS
require_once("rss.php");
$item="<item>";
$item.="<title>Nouveau membre!</title>";
$item.="<link>http://rckl.free.fr/trombinoscope.php</link>";
$item.="<description><![CDATA[";
$item.="{$prenom} {$nom} ({$nomprofil}) vient de s'inscrire au RCKL.";
$item.="]]></description>";
$item.="<pubDate>".date($rssdateformat)."</pubDate>";
$item.="</item>";
rssAdditem($item);
rssUpdate();

// annonce de la sortie dans les news
require_once("news_utils.php");
$newsbody="Souhaitons la bienvenue à un nouveau membre: {$prenom} {$nom} ({$nomprofil}) vient de s'inscrire au RCKL.";
insertNews("RCKL",$newsbody);
cleanNews();

// auto-login 
session_start();
$_SESSION['login']=$login; 
$_SESSION['userid']=$id;
$_SESSION['profilename']=$nomprofil;

header("Location: profile_done.php");

// helper functions
// efface les données relatives à un membre non validé
function deleteMember($id) {
  global $db; 
  $query="DELETE FROM membres WHERE id={$id}";
  mysql_query($query,$db) or die("Erreur lors de l'effacement d'un membre non validé: ".mysql_error());
  // efface le fichier photo
  unlink(photoPath($id));
}

// efface une entrée pending
function deletePending($id) {
  global $db; 
  $query="DELETE FROM pending WHERE id={$id}";
  mysql_query($query,$db) or die("Erreur lors de l'effacement d'une validation périmée: ".mysql_error());
}

// donne le status CANLOGIN à un membre, et donc valide son inscription
function validateMember($id) {
  global $db;
  $query="UPDATE membres SET status=@STATUS_CANLOGIN WHERE id={$id}";
  mysql_query($query,$db) or die("Erreur lors de la validation d'un membre: ".mysql_error());
}
?>
 