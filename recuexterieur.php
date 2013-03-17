<?php
session_start();
// =========== tests préalables
//teste si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
  header("Location: news.php?menu");
}
require("dbconnect.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("head.html"); ?>
<script type="text/javascript" src="recuexterieur.js"></script>
</head>
<body>
<h2>Saisir un reçu d'argent extérieur</h2>
<div style="font-size:0.8em">
Mode d'emploi:
<ol> 
<li><u>Attention</u>: cette procédure doit être utilisée exclusivement si vous avez reçu de l'argent de personne(s) extérieure(s) au groupe loisirs: si un membre du groupe vous donne de l'argent, c'est à <em>lui</em> de le déclarer comme une dépense, et non à vous de le déclarer comme un reçu.</li>
<li>Saisissez la somme totale que vous avez dépensée;</li>
<li>Saisissez une description du reçu  (par exemple l'objet, la date, la sortie...);</li>
<li>Validez.</li>
</ol>
</div>

<div align="center">
<form accept-charset="UTF-8" method="post" onsubmit="return validation()" action="enregistrerecu.php">
 <p><label><b>Somme dépensée:</b></label>
 <input id="somme" name="somme" type="text" size="4" /><b>€</b></p>

 <p><label><b>Détails concernant le reçu:</b></label><br />
 <textarea name="commentaire" cols="50" rows="10"></textarea></p>
 <p><input type="submit" value="Valider le reçu"/></p>
</form>
<form method="post" action="gestiondesfrais.php">
 <p><input type="submit" name="cancel" value="Annuler" /></p>
</form>
</div>
</body>
</html>
