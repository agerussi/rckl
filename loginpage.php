<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("head.html"); ?>
</head>
<body>
  <h1>Connexion des membres du GL</h1>
    <div align="center">
      <form accept-charset="utf-8" method="post" action="login.php">
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
