<?php
session_start();
// =========== tests préalables
//teste si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
  header("Location: news.php?menu");
}
// teste si l'utilisateur provient de la page "nouveaupaiement.php"
if (!isset($_POST['validerpaiement'])) {
  header("Location: nouveaupaiement.php");
}
// connexion à la base de données
require("dbconnect.php");

// établit la liste et le nombre des sélectionnés
$query = 'SELECT id,nom,solde FROM membres WHERE login<>"root"';
$result=mysql_query($query,$db);
$num=0;
while($ligne = mysql_fetch_array($result)) {
  $nom=$ligne['nom'];
  $id=$ligne['id'];
  $solde=$ligne['solde'];
  if ($_POST['id'.$id]) {
    $selectionnes[$num]['nom']=$nom;
    $selectionnes[$num]['id']=$id;
    $selectionnes[$num]['solde']=$solde;
    $num++;
  }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php
  if (isset($_GET['menu'])) require("menuh.php"); 
  else require("head.html");
?>
</head>
<body>
<h2>Récapitulatif du paiement</h2>
<div align="center">
<?php
$wrong=false;
if ($num==0 || !isset($_POST['somme']) || !isset($_POST['commentaire'])) {
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
    <p><form method="POST" action="nouveaupaiement.php">
     <input type="submit" value="Recommencer une déclaration" />
    </form>
  ';
}  
else { // la demande semble correcte
  $_SESSION['paiement-selectionnes']=$selectionnes;
  $_SESSION['paiement-num']=$num;
  $_SESSION['paiement-description']=$_POST['commentaire'];
  // $somme=$_POST['somme'];
  $_SESSION['paiement-somme']=$somme;

  echo '<p>Vous avez déclaré une somme de '.$somme.' €.</p>';
  echo '<p>Vous serez donc crédité de cette somme.</p>';
  if ($num==1) 
    echo '<p>La personne bénéficiaire est';
  else 
    echo '<p>Les personnes bénéficiaires sont:';
  for ($i=0; $i<$num; $i++) {
    echo ' '.$selectionnes[$i]['nom'];
    if ($i==$num-2) echo ' et';
    else if ($i==$num-1) echo '.';
    else echo ',';
  }
  $somme=round(100*$somme/$num)/100;
  if ($num==1) 
    echo '</p><p>Elle sera donc débitée de '.$somme.' €.</p>';
  else 
    echo '</p><p>Elles seront chacunes débitées de '.$somme.' €.</p>';

  echo '<p>
    <form method="post" action="confirmationpaiement.php"><input type="submit" value="Confirmer le paiement" /></form>
    ou
    <form action="nouveaupaiement.php"><input type="submit" value="Recommencer la déclaration" /></form>
    ou
    <form action="gestiondesfrais.php"><input type="submit" value="Abandonner" /></form></p>
    ';
} 
?>
</div>
</body>
</html>
