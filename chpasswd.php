<?php
session_start();
// =========== tests préalables
//teste si l'utilisateur est connecté
if (!isset($_SESSION['userid'])) {
  header("Location: news.php?menu");
}
//check that the user is calling the page from the passwd form and not accessing it directly
if (!isset($_POST['passwordone']) || !isset($_POST['passwordtwo'])) {
  header( "Location: chpasswd-page.php" );
}
$pass1 = $_POST['passwordone'];
$pass2 = $_POST['passwordtwo'];
// teste si les deux mots de passe sont égaux et non vides
if (empty($pass1) || $pass1 != $pass2) {
  header( "Location: chpasswd-page.php" );
}

// ======= changement du mot de passe

$newpass = md5($pass1); // code le mot de passe
$id=$_SESSION['userid'];

require("dbconnect.php");

//echo 'debug: Changement du mot de passe id n° '.$_SESSION['userid'];

$query="UPDATE membres SET motdepasse='$newpass' WHERE id='$id'";
mysql_query($query, $db) or die("erreur lors du changement de mot de passe: ".mysql_error());
header("Location: news.php?menu");
?> 
