<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("head.html"); ?>
</head>
<body>
  <h1>Connexion des membres du RCKL</h1>
    <div align="center">

<?php 
$action="login.php?target=";
if (isset($_GET['target'])) $action.=$_GET['target'];
else $action.="news.php";

echo '<form accept-charset="utf-8" method="post" action="'.$action.'">';
?>
	<table>
	  <tr>
	    <td align=right>Nom de connexion:</td>
	    <td><input type="text" name="login" size="20"/></td>
	  </tr>
	  <tr>
	    <td align=right>Mot de passe:</td>
	    <td><input type="password" name="password" size="20"/></td>
	  </tr>
	  <tr>
	    <td colspan=2 align=center><input type="submit" value="Connexion" name="loginform"/></td>
	  </tr>
	</table>
	</form>
    </div>
</body>
</html> 
