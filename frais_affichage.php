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
$query = "SELECT * FROM paiements WHERE status='DONE' ORDER BY date DESC";
$resultPaiements=mysql_query($query, $db) or die("Erreur lors de la récupération des paiements: ".mysql_error());
// les déclarations en cours
$query = "SELECT *, DATEDIFF(DATE_ADD(date, INTERVAL 20 DAY),CURDATE()) AS echeance FROM paiements WHERE status='AUTH' ORDER BY date DESC";
$resultPending=mysql_query($query, $db) or die("Erreur lors de la récupération des paiements: ".mysql_error());
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
  <?php require("menu_header.php"); ?>
  <script type="text/javascript" src="frais_affichage.js"></script>
</head>
<body>
<?php require("menu_body.php"); 
?>
<a target="_blank" href="help_gestion_frais.php"><img class="helpIcon" src="ICONS/help.png" alt="Icône d'aide" title="Aide pour cette page"/></a>

<h1>GESTION DE VOS FRAIS</h1>

<form action="frais_nouveau.php">
<input type="submit" value="Déclarer une nouvelle dépense" title="pour déclarer de l'argent que vous avez dépensé pour (ou donné à) des membres du RCKL"/>
</form>

<h2>Bilan global personnel</h2>

<h4>(sommes en euros, les sommes négatives sont dues)</h4>
<?php
// affichage du bilan personnel global
// détermine les membres avec lesquels on a eu des frais
$relations=array();
while ($ligne=mysql_fetch_array($resultPaiements)) {
  $selectedList=unserialize($ligne['selected']);
  if (in_array($userId,$selectedList)) {
    foreach ($selectedList as $id) $relations[$id]=true;
  }
}
mysql_data_seek($resultPaiements,0); // remet l'index au départ pour un nouveau parcours

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
?>

<?php
  // crée le tableau des dépenses en cours 
  $isPending=false;
  $pendingTable="";
  while($ligne = mysql_fetch_array($resultPending)) {
    $selectedList=unserialize($ligne['selected']);
    $self=($ligne['idAuteur']==$userId);
    if ($isRoot || in_array($userId,$selectedList) || $self) {
      $isPending=true;
      $pendingTable.="<tr>";
      $pendingTable.="<td>".formatDate($ligne['date'])."</td>";
      $pendingTable.="<td>".$ligne['auteur']."</td>";
      $pendingTable.="<td>".$ligne['somme'];
      if ($isRoot || $self) {
	$pendingTable.='<img class="icon" title="annuler la dépense" src="ICONS/b_drop.png"';
	$pendingTable.=' name="cancelIcon" id="'.$ligne['id'].'" />';
      }
      $echeance=$ligne['echeance'];
      $pendingTable.="<td>".$echeance." jour".(($echeance==1) ? "":"s")."</td>";
      $pendingTable.="<td>";
      // affiche les auth
      $auth=unserialize($ligne['auth']);
      for ($i=0; $i<count($selectedList); $i++) {
	//if ($selectedList[$i]==$ligne['idAuteur']) continue;
	$query ="SELECT nomprofil FROM membres WHERE id='".$selectedList[$i]."'";
	$result=mysql_query($query,$db);
	if (mysql_num_rows($result)!=1) die("Erreur lors de la récupération des informations d'un membre: ".mysql_error());
        if ($selectedList[$i]==$userId && !$self) {
	  if ($auth[$i]) // proposer l'interdiction
	    $pendingTable.='<img class="icon" src="ICONS/b_drop.png" title="réfuter la dépense" name="authIcon" id="'.$userId.','.$ligne['id'].'"/>';
	  else  // proposer l'acceptation
	    $pendingTable.='<img class="icon" src="ICONS/b_add.png" title="accepter la dépense" name="authIcon" id="'.$userId.','.$ligne['id'].'"/>';
	  }
	$member = mysql_fetch_array($result);
        $pendingTable.='<span class="memberAuth" style="background-color:';
	$pendingTable.=($auth[$i]) ? "green;\"":"red;\"";
	$pendingTable.='">'.$member['nomprofil'].'</span>';
      }
      $pendingTable.="</td>";
      $pendingTable.="</td><td>".$ligne['commentaire']."</td>";
      $pendingTable.="</tr>";
      
    }
  }

  // affichage du tableau
  if ($isPending) {
    echo "<h2>Dépenses en cours de validation</h2>";
    echo '<table id="pending">
      <thead><tr>
      <th>Date</th>
      <th>Auteur</th>
      <th>Somme</th>
      <th>Échéance</th>
      <th>Accord des membres concernés</th>
      <th>Commentaire</th>
      </tr></thead><tbody>';
    echo $pendingTable;  
    echo '</tbody></table>';
  }
?>

<h2>Détail des dépenses passées vous concernant</h2>

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
    $selectedList=unserialize($ligne['selected']);
    $self=($ligne['idAuteur']==$userId);
    if ($isRoot || in_array($userId,$selectedList) || $self) {
      echo "<tr>";
      $date=formatDate($ligne['date']);
      echo "<td>" . $date. "</td>";
      echo "<td>" . $ligne['auteur'] . "</td>";
      echo "<td>" . $ligne['somme'];
      if ($isRoot || $self) {
	echo '<img class="icon" title="annuler les frais" src="ICONS/b_drop.png"';
	echo ' name="cancelIcon" id="'.$ligne['id'].'"/>';
      }
      echo "</td>";
      echo "<td>" . $ligne['variations'] . "</td>";
      echo "<td>" . htmlspecialchars($ligne['commentaire']) . "</td>";
      echo "</tr>";
    }
  }
  echo "</tbody></table>";
}

function formatDate($str) {
  sscanf($str,"%u-%u-%u",$annee,$mois,$jour);
  return $jour.'/'.$mois.'/'.$annee%100;
}
?> 

</body>
</html>
