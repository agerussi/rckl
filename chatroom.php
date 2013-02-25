<?php 
  session_start(); 
  //teste si l'utilisateur est connecté
  if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
    header("Location: news.php?menu");
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php
  if (isset($_GET['menu'])) require("menuh.php"); 
  else require("head.html");
?>
  <script type="text/javascript" src="chat.js"></script>
</head>
<body>
<?php
  if (isset($_GET['menu'])) require("menub.php"); 
?>

<?php
// début du programme spécifique 'chatroom'
// on ajoute l'utilisateur à la liste chat_members
// le reste de la construction de la page est effectué
// par le JS.

  require("dbconnect.php");

  // teste si le membre est déjà inséré (peut arriver si de multiples chatroom sont ouverts)
  /* $query="SELECT id FROM chat_members WHERE id=".$_SESSION[userid];
  echo $query."<br/>";
  $result=mysql_query($query, $db) or die("Erreur lors de la vérification d'unicité: ".mysql_error());
  */

  // si le membre n'est pas déjà présent, l'insère dans la liste
  //if (mysql_num_rows($result)==0) {
  if (true) {
    $query="INSERT INTO chat_members (id, nom) VALUES(".$_SESSION[userid].",'".$_SESSION['realname']."')";
    echo $query."<br/>";
    mysql_query($query, $db) or die("Erreur lors de l'insertion du membre dans chat_members: ".mysql_error());
  }
?>

<!-- #####################################
####### CODE HTML ########################
###################################### -->
Welcome to the chatroom... !
</body>
</html>
