<?php
session_start();
if (!isset($_SESSION['userid']) || !isset($_GET['ids'])) header("Location: sorties.php?menu");

// récupère la valeur des champs s'il s'agit d'une sortie existante
$ids=$_GET['ids']; // id de la sortie
$new=($ids==-1); // nouvelle sortie ?
if (!$new) {
  require("dbconnect.php"); // connexion à la base
  $query="SELECT deadline,datedebut,datefin,destination,objet,modalites FROM sorties WHERE id=$ids";
  $listesortie=mysql_query($query,$db) or die("Erreur lors de la récupération des données d'une sortie: ".mysql_error());
  $sortie=mysql_fetch_array($listesortie);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("head.html"); ?>
<link rel="stylesheet" type="text/css" media="all" href="OUTILS/JSDATEPICK/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="OUTILS/JSDATEPICK/jsDatePick.min.1.3.js"></script>
<script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"datedebut",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"datefin",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"deadline",
			dateFormat:"%d-%m-%Y"
		});
	};
</script>
<script type="text/javascript">
function verificationDate() {
  document.getElementById("MsgDateDebut").innerHTML="";
  document.getElementById("MsgDateFin").innerHTML="";
  document.getElementById("MsgDeadline").innerHTML="";
  // récupère les dates
  var dd=document.getElementById("datedebut").value;
  var df=document.getElementById("datefin").value;
  var dl=document.getElementById("deadline").value;
  
  // teste si la date de départ existe
  if (dd=="") {
    document.getElementById("MsgDateDebut").innerHTML="il manque la date de la sortie!";
    return false;
  }

  // gère les autres dates vides
  var dds=dd.split("-");
  var dated=new Date(dds[2],dds[1],dds[0]);
  if (df=="") document.getElementById("datefin").value=dd;
  else {
    var dfs=df.split("-");
    var datef=new Date(dfs[2],dfs[1],dfs[0]);
    if (datef<dated) {
      document.getElementById("MsgDateFin").innerHTML="la date de retour précède le début de la sortie!";
      return false;
    }
  }

  if (dl=="") document.getElementById("deadline").value=dd;
  else {
    var dls=dl.split("-");
    var datel=new Date(dls[2],dls[1],dls[0]);
    if (datel>dated) {
      document.getElementById("MsgDeadline").innerHTML="la deadline est postérieure au début de la sortie!";
      return false;
    }
  }

  return true;
}
</script>
</head>
<body>
<h1 align="center">Formulaire de saisie des sorties</h1>

<div align="center">
<?php
echo '<form accept-charset="utf-8" action="enregistresortie.php?ids='.$ids.'" method="post" onsubmit="return verificationDate()">';
?>
<p><label for="datedebut"><b>Date de départ</b></label> 
<br/><input type="text" size="10" name="datedebut" id="datedebut" readonly="readonly" /><small id="MsgDateDebut" style="color:Red"></small>
</p>
<p><label for="datefin"><b>Date de retour</b></label>
<br/><input type="text" size="10" name="datefin" id="datefin" readonly="readonly" /> <small id="MsgDateFin" style="color:Red"></small>
</p>
<p><label for="deadline"><b>Deadline des inscriptions</b></label>
<br/><input type="text" size="10" name="deadline" id="deadline" readonly="readonly" /> <small id="MsgDeadline" style="color:Red"></small>
</p>
<p><label for="destination"><b>Destination</b></label> 
<br/><input type="text" name="destination" id="destination" size="30" title="ville ou lieu de la sortie"/> 
</p>
<p><label for="description"><b>Description</b></label> 
<br/><textarea name="description" id="description" cols="50" rows="5" title="informations sur l'activité prévue pendant la sortie"></textarea>
</p>
<p><label for="modalites"><b>Modalités</b></label>
<br/><textarea name="modalites" id="modalites" cols="50" rows="5" title="informations pratiques liées à l'organisation"></textarea>
</p>
<p><input type="submit" name="sortiesubmit" value="Valider la sortie" /></p>
</form>
<form method="post" action="sorties.php?menu">
  <p><input type="submit" name="cancel" value="Annuler" /></p>
</form>
</div>

<?php // valeurs par défaut: initialisée par un script
if (!$new) {
  sscanf($sortie['datedebut'],"%u-%u-%u",$annee,$mois,$jour);
  $datedebut=$jour."-".$mois."-".$annee;
  sscanf($sortie['datefin'],"%u-%u-%u",$annee,$mois,$jour);
  $datefin=$jour."-".$mois."-".$annee;
  sscanf($sortie['deadline'],"%u-%u-%u",$annee,$mois,$jour);
  $deadline=$jour."-".$mois."-".$annee;
  echo '<script type="text/javascript">';
  echo 'document.getElementById("datedebut").value="'.$datedebut.'";';
  echo 'document.getElementById("datefin").value="'.$datefin.'";';
  echo 'document.getElementById("deadline").value="'.$deadline.'";';
  echo 'document.getElementById("destination").value="'.str_replace(array("\r\n", "\n", "\r"),"\\n",addslashes($sortie['destination'])).'";';
  echo 'document.getElementById("description").value="'.str_replace(array("\r\n", "\n", "\r"),"\\n",addslashes($sortie['objet'])).'";';
  echo 'document.getElementById("modalites").value="'.str_replace(array("\r\n", "\n", "\r"),"\\n",addslashes($sortie['modalites'])).'";';
  echo '</script>';
}
?>

</body>
</html>
