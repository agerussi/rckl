<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php
  if (isset($_GET['menu'])) require("menuh.php"); 
  else require("head.html");
  <script type="text/javascript" src="chat.js"></script>
?>
</head>
<body>
<?php
  if (isset($_GET['menu'])) require("menub.php"); 
?>

<?php
// début du programme spécifique 'chatroom'
// on ajoute l'utilisateur à la liste chat_members
// en faisant attention de ne pas avoir de double
// le reste de la construction de la page est effectué
// par le JS.

?>

<!-- #####################################
####### CODE HTML ########################
###################################### -->
Welcome to the chatroom... !
</body>
</html>
