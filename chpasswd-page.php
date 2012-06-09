<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("head.html"); ?>
<script type="text/javascript">
  function checkpasswd() {
    var p1=(document.getElementsByName("passwordone"))[0].value;
    var p2=(document.getElementsByName("passwordtwo"))[0].value;
    if (p1=="" || p1!=p2) {
      document.getElementById("errorBox").innerHTML="les mots de passe sont différents !";
      return false;
    }
    return true;
  }
</script>
</head>
<body>
<div align="center">
  <h1>Changement du mot de passe</h1>
  <form accept-charset="utf-8" method="post" action="chpasswd.php" onsubmit="return checkpasswd()">
    <table>
      <tr>
	<td align=right>Nouveau mot de passe:</td>
	<td><input type="password" name="passwordone" size="20"/></td>
      </tr>
      <tr>
	<td align=right>Encore une fois:</td>
	<td><input type="password" name="passwordtwo" size="20"/></td>
      </tr>
      <tr>
	<td colspan=2 align=center><input type="submit" value="Modifier" name="chpasswd"/></td>
      </tr>
    </table>
   </form>
<p><small id="errorBox" style="color:Red"></small></p>
</div>
</body>
</html> 
