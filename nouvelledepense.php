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
</head>
<body>
<h2>Saisir un nouveau paiement</h2>
<div style="font-size:0.8em">
Mode d'emploi:
<ol> 
<li> Saisissez la somme totale que vous avez dépensée;</li>
<li> Sélectionnez les membres bénéficiaires (<b>vous y compris</b>, le cas échéant);</li>
<li> Saisissez une description de la dépense (par exemple l'objet, la date, la sortie...);</li>
<li> Validez la demande (une confirmation sera demandée).</li>
</ol>
</div>

<div align="center">
<form accept-charset="UTF-8" method="post" action="validationpaiement.php">
 <p><label><b>Somme dépensée:</b></label>
 <input name="somme" type="text" size="4" /><b>€</b></p>
 <p><label><b>Membre(s) bénéficiaire(s)</b></label></p>

<?php // construction de la liste des membres sélectionnables 
$query = 'SELECT id,nom FROM membres WHERE login<>"root" AND site<>0';
$result=mysql_query($query,$db);
echo '<table><tr>';
$num=0;
while($ligne = mysql_fetch_array($result)) {
  if ($num%5 ==0 && $num!=0) { //nouvelle ligne
    echo "</tr><tr>";
  }
  echo '<td>';
  echo '<input type="checkbox" name="id'.$ligne['id'].'" />';
  echo '<label>'.$ligne['nom'].'</label>';
  echo '</td>';
  $num++;
}
echo "</tr></table>"
?>
 <p>
   <label> <b>Bénéficiaire(s) extérieur(s)</b></label>
 </p>
 <p>
  <input type="hidden" name="NB_BENEF_EXT" value="0" />
  <img class="edit" title="ajouter un bénéficiaire" src="FONDS/b_add.png" />
 </p>
 <p><label><b>Détails concernant la dépense:</b></label><br />
 <textarea name="commentaire" cols="50" rows="10"></textarea></p>
 <p><input type="submit" name="validerpaiement" value="Valider le paiement"/></p>
</form>
<form method="post" action="gestiondesfrais.php">
 <p><input type="submit" name="cancel" value="Annuler" /></p>
</form>
</div>
</body>
</html>
