<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("head.html"); ?>
</head>
<body>
  <h1>Poster une news</h1>
  <div align="center">
  <p>Entrez le texte de votre news:</p>
  <form accept-charset="utf-8" method="post" action="newspost.php">
    <p><textarea name="newsbody" cols="50" rows="10"></textarea></p>
    <p><input type="submit" value="Poster"/></p>
  </form>
  <form method="post" action="news.php">
    <p><input type="submit" value="Annuler"/></p>
  </form>
  </div>
</body>
</html> 
