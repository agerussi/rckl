<?php
session_start();
// =========== tests préalables
//teste si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
  header("Location: news.php");
}
require_once("dbconnect.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
  <?php require("menu_header.php"); ?>
  <script type="text/javascript">
    function doCancel(id) {
      if (confirm("Êtes-vous sûr(e) ?")) window.location.replace("frais_annulation.php?id="+id);
    }
  </script>
</head>
<body>
<?php require("menu_body.php"); ?>
<h1>GESTION DES FRAIS</h1>
<h2>Bilan global</h2>

(sommes en euros, les sommes négatives sont dues)
<table id="bilanglobal">
  <tbody>
<?php
// récupère le min et le max
$query='SELECT MIN(solde) AS soldeMin, MAX(solde) AS soldeMax FROM membres WHERE login<>"root" AND site<>0';
$maxresult=mysql_query($query,$db) or die("Erreur lors de la récupération du solde max: ".mysql_error());
$soldeMax=mysql_result($maxresult,0,'soldeMax');
$soldeMin=mysql_result($maxresult,0,'soldeMin');

$query = 'SELECT * FROM membres WHERE login<>"root" AND site<>0';
$result=mysql_query($query,$db);
$n=0;
$maxperline=8;
while($ligne = mysql_fetch_array($result)) {
    if ($n%$maxperline==0) echo "<tr>";
    $solde=$ligne['solde'];
    switch ($solde) {
    case $soldeMax:
      echo '<td style="color:Green">';
      break;
    case $soldeMin:
      echo '<td style="color:Red">';
      break;
    default:
      echo '<td>';
    }
    echo $ligne['nomprofil'].": ".$solde."</td>";
    if ($n%$maxperline==$maxperline-1) echo "</tr>";
    $n++;
}
for (;$n%$maxperline!=0; $n++) echo "<td></td>";
echo "</tr>";
?>
  </tbody>
</table>

<form action="frais_nouveau.php">
<input type="submit" value="Déclarer une nouvelle dépense" title="pour déclarer de l'argent que vous avez dépensé pour (ou donné à) des membres du RCKL ou des personnes extérieures"/>
</form>
<form action="frais_recuexterieur.php">
<input type="submit" value="Déclarer un reçu extérieur" title="si vous avez reçu directement de l'argent d'une personne *extérieure* au RCKL"/>
</form>

<h2>Historique des paiements</h2>

<?php

$query = 'SELECT * FROM paiements ORDER BY date DESC';
$result=mysql_query($query, $db);
if (mysql_num_rows($result)==0) {
  echo 'L\'historique est vide';
}
else {
  $root=($_SESSION['userid']==1); 
  echo '<table id="historique">
    <thead><tr>
    <th>Date</th>
    <th>Auteur</th>
    <th>Somme</th>
    <th>Variations</th>
    <th>Commentaire</th>
    </tr></thead><tbody>';
  while($ligne = mysql_fetch_array($result)) {
    echo "<tr>";
    sscanf($ligne['date'],"%u-%u-%u",$annee,$mois,$jour);
    $date=$jour.'/'.$mois.'/'.$annee%100;
    echo "<td>" . $date. "</td>";
    echo "<td>" . $ligne['auteur'] . "</td>";
    echo "<td>" . $ligne['somme'];
    if ($root) {
      echo '<img class="icon" title="annuler les frais" src="ICONS/b_drop.png"';
      echo ' onclick="doCancel(' . $ligne['id'] . ')" />';
    }
    echo "</td>";
    echo "<td>" . $ligne['variations'] . "</td>";
    echo "<td>" . htmlspecialchars($ligne['commentaire']) . "</td>";
    echo "</tr>";
  }
  echo "</tbody></table>";
}
?> 

</body>
</html>
