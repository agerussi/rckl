<?php
session_start();
// =========== tests préalables
//teste si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
  header("Location: news.php?menu");
}
// teste si l'utilisateur provient de la page "recuexterieur.php"
if (!isset($_POST['somme']) || !isset($_POST['commentaire'])) {
  header("Location: nouveaupaiement.php");
}

// somme arrondie au centime près
$somme=round(100*$_POST['somme'])/100;

// connexion à la base de données
require("dbconnect.php");

// calcul du nouveau solde
$query = "SELECT solde FROM membres WHERE id=".$_SESSION['userid'];
echo "debug: ".$query."<br/>";
$result = mysql_query($query,$db) || die("Erreur lors de la récupération du solde");
echo "debug: ".$result."<br/>";
$ligne = mysql_fetch_array($result);
echo "debug: ".$ligne."<br/>";
$solde = $ligne['solde'];
$solde -= $somme; // nouveau solde, à sauvegarder

$query = "UPDATE membres SET solde=".$solde." WHERE id=".$_SESSION['userid'];
echo "debug: ".$query."<br/>";
//mysql_query($query,$db) || die("Erreur lors de l'enregistrement du nouveau solde");

// rajout dans l'historique
$listevariations=$_SESSION['realname'].'(-'.$somme.'), ';
$cancel=$_SESSION['userid'].','.$somme;
echo "debug: listevariations=".$listevariations.'<br/>';
echo "debug: cancel=".$cancel.'<br/>';

$query="INSERT INTO paiements (date, auteur, somme, variations, commentaire, cancel) VALUES(CURDATE(),'".$_SESSION['realname']."',".$somme.",'".$listevariations."','".$_POST['commentaire']."','".$cancel."')";
echo "debug: query=".$query.'<br/>';
//mysql_query($query, $db) or die("erreur lors de l'ajout dans l'historique: ".mysql_error());

?>
</div>
</body>
</html>
