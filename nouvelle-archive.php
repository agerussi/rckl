<?php
session_start();
// test de sécurité 
if ($_SESSION['login']!="root") header("Location: news.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("head.html"); ?>

<script type="text/javascript">
function validationId() {
  var id=document.getElementById("id").value;

  // crée une archive vierge si l'id est disponible
  var xhr = new XMLHttpRequest();
  xhr.open("GET",'createBlankArchive.php?id='+id,false);
  xhr.send();
  var dispo=xhr.responseText;

  if (dispo==1) { // il est disponible, et l'archive vierge a été créée
    window.location.replace("editer-archive.php?id="+id); 
  }
  else {
    window.alert("L'identifiant '"+id+"' n'est pas disponible.");
    return false;
  }
}
</script>
</head>
<body>
  <label>Choisissez un identifiant pour l'archive:</label>
  <input type="text" id="id" width="20" />
  <p><input type="submit" value="Nouvelle archive" onclick="validationId()"/></p>
  <form method="post" action="news.php">
    <p><input type="submit" value="Annuler" /></p>
  </form>
</body>
</html>


