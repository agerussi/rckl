<?php
session_start();
// =========== tests préalables
//teste si l'utilisateur est connecté
if (!isset($_SESSION['userid'])) {
  header("Location: news.php");
}
$userId=$_SESSION['userid'];
$isRoot=($userId==1); 

// requêtes de la BD
require_once("dbconnect.php");
// le solde min et max
$query='SELECT MIN(solde) AS soldeMin, MAX(solde) AS soldeMax FROM membres WHERE login<>"root" AND site<>0';
$maxresult=mysql_query($query,$db) or die("Erreur lors de la récupération du solde max: ".mysql_error());
$soldeMax=mysql_result($maxresult,0,'soldeMax');
$soldeMin=mysql_result($maxresult,0,'soldeMin');
// les soldes des membres
$query = 'SELECT id, nomprofil, solde FROM membres WHERE login<>"root" AND site<>0';
$resultSoldes=mysql_query($query,$db) or die("Erreur lors de la récupération des soldes: ".mysql_error());
// les paiements
$query = 'SELECT * FROM paiements ORDER BY date DESC';
$resultPaiements=mysql_query($query, $db) or die("Erreur lors de la récupération des paiements: ".mysql_error());

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

<h1>GESTION DE VOS FRAIS</h1>

<form action="frais_nouveau.php">
<input type="submit" value="Déclarer une nouvelle dépense" title="pour déclarer de l'argent que vous avez dépensé pour (ou donné à) des membres du RCKL"/>
</form>

<h2>Bilan global personnel</h2>

<h4>(sommes en euros, les sommes négatives sont dues)</h4>
<table id="bilanglobal">
  <tbody>
<?php
// affichage du bilan personnel global
// détermine les membres avec lesquels on a eu des frais
$relations=array();
while ($ligne=mysql_fetch_array($resultPaiements)) {
  $cancelTab=explode(',',$ligne['cancel']);
  if (isConcerned($ligne['cancel'],$userId)) {
    for ($i=0; $i<count($cancelTab); $i+=2) 
      $relations[$cancelTab[$i]]=true;
  }
}
mysql_data_seek($resultPaiements,0); // remet l'index au départ pour un nouveau parcours
//var_dump($relations);

while($ligne = mysql_fetch_array($resultSoldes)) {
  if (($relations[$ligne['id']] && abs($ligne['solde'])>=1) || $isRoot) {
    echo '<span class="solde" style="';
    if ($ligne['id']==$userId) echo 'background-color: lightgray;';
    $solde=$ligne['solde'];
    switch ($solde) {
    case $soldeMax:
      echo 'color:Green;';
      break;
    case $soldeMin:
      echo 'color:Red;';
      break;
    }
    echo '">';
    echo $ligne['nomprofil'].": ".$solde."</span>";
  }
}
for (;$n%$maxperline!=0; $n++) echo "<td></td>";
echo "</tr>";
?>
  </tbody>
</table>

<h2>Détail des paiements vous concernant</h2>

<?php
// affichage de l'historique personnel des paiements
if (mysql_num_rows($resultPaiements)==0) {
  echo 'L\'historique est vide';
}
else {
  echo '<table id="historique">
    <thead><tr>
    <th>Date</th>
    <th>Auteur</th>
    <th>Somme</th>
    <th>Variations</th>
    <th>Commentaire</th>
    </tr></thead><tbody>';
  while($ligne = mysql_fetch_array($resultPaiements)) {
    if ($isRoot || isConcerned($ligne['cancel'],$userId)) {
      echo "<tr>";
      sscanf($ligne['date'],"%u-%u-%u",$annee,$mois,$jour);
      $date=$jour.'/'.$mois.'/'.$annee%100;
      echo "<td>" . $date. "</td>";
      echo "<td>" . $ligne['auteur'] . "</td>";
      echo "<td>" . $ligne['somme'];
      if ($isRoot) {
	echo '<img class="icon" title="annuler les frais" src="ICONS/b_drop.png"';
	echo ' onclick="doCancel(' . $ligne['id'] . ')" />';
      }
      echo "</td>";
      echo "<td>" . $ligne['variations'] . "</td>";
      echo "<td>" . htmlspecialchars($ligne['commentaire']) . "</td>";
      echo "</tr>";
    }
  }
  echo "</tbody></table>";
}

// helper functions
function isConcerned($cancel,$id) {
  $liste=explode(',',$cancel);
  for ($i=0; $i<count($liste); $i+=2) 
    if ($liste[$i]==$id) return true;
  return false;
}

?> 

</body>
</html>
