<?php
session_start();
require_once("magic_quotes_gpc_off.php");
// =========== tests préalables
//teste si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
  header("Location: news.php");
}
// teste si l'utilisateur provient de la page "nouveaupaiement.php"
if (!isset($_POST['validerpaiement'])) {
  header("Location: nouveaupaiement.php");
}
// connexion à la base de données
require_once("dbconnect.php");

// établit la liste et le nombre des membres sélectionnés
$query = 'SELECT id,nomprofil,solde FROM membres WHERE login<>"root"';
$result=mysql_query($query,$db);
$numMembres=0;
while($ligne = mysql_fetch_array($result)) {
  $nom=$ligne['nomprofil'];
  $id=$ligne['id'];
  $solde=$ligne['solde'];
  if ($_POST['id'.$id]) {
    $selectionnes[$numMembres]['nom']=$nom;
    $selectionnes[$numMembres]['id']=$id;
    $selectionnes[$numMembres]['solde']=$solde;
    $numMembres++;
  }
}

$numExt=$_POST['NB_BENEF_EXT'];
for ($i=1; $i<=$numExt; $i++) { // liste des extérieurs
  $exterieurs[$i]=$_POST['exterieur'.$i];
}

$numTotal=$numMembres+$numExt;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("menu_header.php"); ?>
</head>
<body>
<?php require("menu_body.php"); ?>
<h2>Récapitulatif du paiement</h2>
<div align="center">
<?php
$wrong=false;
if ($numTotal==0 || !isset($_POST['somme']) || !isset($_POST['commentaire'])) {
  echo 'Un des champs de la déclaration n\'a pas été rempli.';
  $wrong=true;
}
else {
  // remplace une éventuelle virgule par un point
  $somme=str_replace(',','.',$_POST['somme']);
  if (!is_numeric($somme) || $somme<=0) {
  echo 'Vous avez déclaré une somme invalide.';
  $wrong=true;
  }
}
if ($wrong) {
  echo '
    <p><form method="POST" action="nouvelledepense.php">
     <input type="submit" value="Recommencer une déclaration" />
    </form>
  ';
}  
else { // la demande semble correcte
  $_SESSION['paiement-selectionnes']=$selectionnes;
  $_SESSION['paiement-exterieurs']=$exterieurs;
  $_SESSION['paiement-numMembres']=$numMembres;
  $_SESSION['paiement-numExt']=$numExt;
  $_SESSION['paiement-numTotal']=$numTotal;
  $_SESSION['paiement-description']=trim($_POST['commentaire']);
  $_SESSION['paiement-somme']=$somme;

  echo '<p>Vous avez déclaré une somme de '.$somme.' €.</p>';
  echo '<p>Vous serez donc crédité de cette somme.</p>';
  if ($numTotal==1) echo '<p>La personne bénéficiaire est';
  else echo '<p>Les personnes bénéficiaires sont:';
  for ($i=0; $i<$numMembres; $i++) {
    echo ' '.$selectionnes[$i]['nom'];
    if ($i==$numTotal-2) echo ' et';
    else if ($i==$numTotal-1) echo '.';
    else echo ',';
  }
  for ($i=1; $i<=$numExt; $i++) {
    echo ' '.$exterieurs[$i].' (extérieur)';
    if ($numMembres+$i==$numTotal-1) echo ' et';
    else if ($numMembres+$i==$numTotal) echo '.';
    else echo ',';
  }
  $somme=round(100*$somme/$numTotal)/100;
  if ($numTotal==1) 
    echo '</p><p>Elle sera donc débitée de '.$somme.' €.</p>';
  else 
    echo '</p><p>Elles seront chacunes débitées de '.$somme.' €.</p>';

  echo '<p>
    <form method="post" action="confirmationpaiement.php"><input type="submit" value="Confirmer le paiement" /></form>
    ou
    <form action="nouvelledepense.php"><input type="submit" value="Recommencer la déclaration" /></form>
    ou
    <form action="gestiondesfrais.php"><input type="submit" value="Abandonner" /></form></p>
    ';
} 
?>
</div>
</body>
</html>
