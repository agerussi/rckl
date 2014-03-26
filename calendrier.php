<?php
session_start();

function adddrop() { // crée un bouton d'annulation cablé
  echo '<img class="icon" title="effacer la sortie" src="ICONS/b_drop.png" />';
}

function bissextile($annee) {
  return ($annee%400==0 || ($annee%4==0 && $annee%100!=0));
}

function analysedate($date,$a,$m,$j,$jds) {
  sscanf($date,"%u-%u-%u",$annee,$mois,$jour);

  // calcule le nombre de jours séparant la date de la date référence du samedi 1er janvier 2011 
  $nbj=0;
  for ($i=2011; $i<$annee; $i++) $nbj+=(bissextile($i) ? 366 : 365);

  $joursenplus=array(0,31,59,90,120,151,181,212,243,273,304,334);
  $nbj+=$joursenplus[$mois-1];
  if ($mois>2 && bissextile($annee)) $nbj+=1;
  
  $nbj+=$jour-1;

  $GLOBALS[$a]=$annee;
  $GLOBALS[$m]=$mois;
  $GLOBALS[$j]=$jour;
  $jourdelasemaine=array("Sa", "Di", "Lu", "Ma", "Me", "Je", "Ve");
  $GLOBALS[$jds]=$jourdelasemaine[$nbj%7];
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("menu_header.php"); ?>
  <script type="text/javascript">
    function deleteSortie(ids) {
      if (confirm("Êtes-vous sûr(e) ?")) window.location.replace("effacesortie.php?ids="+ids);
    }
  </script>
</head>

<body>
<?php
require("background.html");
require("menu_body.php"); 
?>

<h1>LES ACTIVITÉS À VENIR</h1>

<?php // affichage de la liste des sorties existantes
$logged=isset($_SESSION['login']);
// récupère la liste des sorties
require_once("dbconnect.php");
$query="SELECT * FROM sorties ORDER BY datedebut";
$listesorties=mysql_query($query, $db) or die("Erreur pendant la récupération de la liste des sorties: ".mysql_error());

if (mysql_num_rows($listesorties)==0) {
  echo '<h2>Pas de sorties ou activités programmées.</h2>';
} 
else {
  echo '
    <table id="sorties">
    <thead><tr>
      <th>Mois</th>
      <th>Jour(s)</th>
      <th>Destination</th>
      <th>Description</th>
      <th>Participants&nbsp;inscrits</th>
      <th>Modalités</th>
      <th>Organisateur</th>
      <th>Deadline</th>
    </tr></thead>';

  $nomdesmois = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");

  while($sortie=mysql_fetch_array($listesorties)) {
    if ($logged) $own=($sortie['idresponsable']==$_SESSION['userid'] || $_SESSION['userid']==1);
    else $own=false;
    $deadline=(date("Y-m-d")>$sortie['deadline']); // deadline atteinte ??
    $datedebut=$sortie['datedebut'];
    $datefin=$sortie['datefin'];
    echo '<tr>';
    // le mois de la sortie
    analysedate($datedebut,'annee','mois','jour','jds');
    echo "<td>".$nomdesmois[$mois-1]."</td>";
    // la ou les dates de la sortie 
    analysedate($datedebut,'annee','mois','jour','jds');
    if ($datedebut==$datefin) 
      echo "<td>".$jds." ".$jour."</td>";
    else {
      echo "<td>du&nbsp;".$jds."&nbsp;".$jour;
      analysedate($datefin,'annee','mois','jour','jds');
      echo "<br />au&nbsp;".$jds."&nbsp;".$jour."</td>";
    }
    // la destination
    echo "<td>".htmlspecialchars($sortie['destination'])."</td>";
    // l'objet de la sortie
    echo "<td>".htmlspecialchars($sortie['objet'])."</td>";

    // la liste des participants
    $inscrit=false; 
    if (empty($sortie['participants']))
      echo "<td>Aucun inscrit pour le moment";
    else {
      $liste=explode(',',$sortie['participants']);
      $n=count($liste);
      echo "<td>";
      for ($i=0; $i<$n; $i+=2) {
	echo $liste[$i];
	if ($logged) {
	  $id=$liste[$i+1]; // id du membre
	  if ($id==$_SESSION['userid'] && !$deadline ) {
	    // rajouter la possibilité de supprimer son nom
            $inscrit=true;
	    echo '<a href="supprimermembresortie.php?ids='.$sortie['id'].'">';
	    echo '<img class="icon" title="supprimer son nom" src="ICONS/b_drop.png" /></a>';
	  }
	}
	if ($i!=$n-2) echo ", ";
      }
    }
    if (!$inscrit && !$deadline && $logged) { // proposer de s'inscrire
      echo '<br /><a href="ajoutermembresortie.php?ids='.$sortie['id'].'">';
      echo '<img class="icon" title="rajouter son nom" src="ICONS/b_add.png" /></a>';
    }
    if ($deadline) echo '<br /><img class="icon" title="la deadline est passée" src="ICONS/file-locked-icon.png" />';
    echo "</td>";

      // les modalités   
    echo "<td>".htmlspecialchars($sortie['modalites'])."</td>";

    // le responsable
    $query="SELECT nomprofil FROM membres WHERE id=".$sortie['idresponsable'];
    $noms=mysql_query($query, $db) or die("Erreur pendant la récupération du nom de l'organisateur.".mysql_error());
    $nom=mysql_fetch_array($noms);
    echo "<td>".$nom['nomprofil'];
    if ($own) {
      echo "<br />";
      echo '<img onclick="deleteSortie('.$sortie['id'].')" class="icon" title="supprimer la sortie" src="ICONS/b_drop.png" />';
      echo '<a href="editsortie.php?ids='.$sortie['id'].'">';
      echo '<img class="icon" title="éditer la sortie" src="ICONS/b_edit.png" /></a>';
    } 
    echo "</td>";

    // la deadline
    analysedate($sortie['deadline'],'annee','mois','jour','jds');
    echo "<td>".$jds."&nbsp;".$jour."/".$mois."</td>";
    echo '</tr>';
  }

  echo '</table>';
}
?>

</body>
</html>
