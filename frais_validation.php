<?php
session_start();
require_once("magic_quotes_gpc_off.php");
// =========== tests préalables
//teste si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
  header("Location: news.php");
}
// teste si l'utilisateur provient de la page "frais_nouveau.php"
if (!isset($_POST['validerpaiement'])) {
  header("Location: frais_nouveau.php");
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("menu_header.php"); ?>
</head>
<body>
<h2>Récapitulatif du paiement</h2>
<div align="center">
<?php
// établit la liste des membres sélectionnés
$selectedList=array();
foreach ($_POST as $key => $value) {
  $n=sscanf($key,"id%d",$id);
  if ($n>0 && $_POST[$key]) array_push($selectedList, $id);
}
//var_dump($selectedList);

// vérifie si les données sont correctes
$wrong=false;
if (count($selectedList)==0 || !isset($_POST['somme']) || !isset($_POST['commentaire'])) {
  echo 'Un des champs de la déclaration n\'a pas été rempli.';
  $wrong=true;
}
else {
  // récupère et vérifie la somme déclarée 
  $somme=str_replace(',','.',$_POST['somme']);
  if (!is_numeric($somme) || $somme<=0) {
    echo 'Vous avez déclaré une somme invalide.';
    $wrong=true;
  }
}
if ($wrong) {
  echo '<p><form method="POST" action="frais_nouveau.php">
    <input type="submit" value="Recommencer une déclaration" />
    </form></p>';
}  
else {
  
  // récupère les données des membres sélectionnés 
  require_once("dbconnect.php");
  $informations=array();
  foreach ($selectedList as $id) {
    $query ="SELECT id,nomprofil,solde FROM membres WHERE id='".$id."'";
    $result=mysql_query($query,$db);
    if (mysql_num_rows($result)!=1) die("Erreur lors de la récupération des informations d'un membre: ".mysql_error());
    $ligne = mysql_fetch_array($result);
    array_push($informations, 
      array('nom'=>$ligne['nomprofil'],
      'id'=>$ligne['id'],
      'solde'=>$ligne['solde']));
  }
  mysql_close($db);
  //var_dump($selectionnes);

  // sauvegarde les données pour frais_enregistrement.php
  $_SESSION['paiement-informations']=$informations;
  $_SESSION['paiement-selectedList']=$selectedList;
  $_SESSION['paiement-description']=trim($_POST['commentaire']);
  $_SESSION['paiement-somme']=$somme;

  $numMembres=count($selectedList);
  echo '<p>Vous avez déclaré une somme de '.$somme.' €.</p>';
  echo '<p>Vous serez donc crédité de cette somme.</p>';
  if ($numMembres==1) echo '<p>La personne bénéficiaire est';
  else echo '<p>Les personnes bénéficiaires sont:';
  for ($i=0; $i<$numMembres; $i++) {
    echo ' '.$informations[$i]['nom'];
    if ($i==$numMembres-2) echo ' et';
    else if ($i==$numMembres-1) echo '.';
    else echo ',';
  }
  $somme=round(100*$somme/$numMembres)/100;
  if ($numMembres==1) 
    echo '</p><p>Elle sera donc débitée de '.$somme.' €.</p>';
  else 
    echo '</p><p>Elles seront chacunes débitées de '.$somme.' €.</p>';

  echo '<p>
    <form method="post" action="frais_enregistrement.php"><input type="submit" value="Confirmer le paiement" /></form>
    ou
    <form action="frais_nouveau.php"><input type="submit" value="Recommencer la déclaration" /></form>
    ou
    <form action="frais_affichage.php"><input type="submit" value="Abandonner" /></form></p>
    ';
} 
?>
</div>
</body>
</html>
