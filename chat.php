<?php 
  session_start(); 
  //teste si l'utilisateur est connecté
  if (!isset($_SESSION['userid']) || empty($_SESSION['userid'])) {
    header("Location: news.php");
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
  <?php require("menu_header.php"); ?>
  <meta http-equiv="refresh" content="30;url=chat.php" />
</head>
<body>

<?php
  require("background.html");
  require("menu_body.php"); 
?>

<h1>SALONS DE DISCUSSION</h1>

<form action="chat_new.php" method="get">
<input type="submit" value="Entrer dans un nouveau salon" title="Cliquez pour créer et pénétrer un nouveau salon de discussion"/>
</form>

<h2>Les salons existants</h2>

<div id="listeSalons">
<?php
  require_once("dbconnect.php");
  // efface les anciens log de salons abandonnés
  ///////////////////////////////////////////////
  $query="DELETE FROM chat_rooms WHERE TIMESTAMPDIFF(SECOND,timestamp,NOW())>=20";
  $result=mysql_query($query, $db) or die("Erreur lors de l'effacement d'anciens salons: ".mysql_error());

  // récupère la liste des salons ouverts et leurs membres
  $query="SELECT id, nomprofil FROM chat_rooms WHERE TIMESTAMPDIFF(SECOND,timestamp,NOW())<20";
  $result=mysql_query($query, $db) or die("Erreur lors de la collecte des salons: ".mysql_error());
  while ($row=mysql_fetch_array($result)) {
    if (!isset($chatrooms[$row['id']])) $chatrooms[$row['id']]=array();
    array_push($chatrooms[$row['id']],$row['nomprofil']);
  }

  // effacement anciens messages
  ///////////////////////////////
  if (count($chatrooms)==0) {
    // efface tous les messages
    $query="TRUNCATE TABLE chat_messages";
  }
  else {
    $sqlTestTab=array();
    foreach ($chatrooms as $id=>$members) array_push($sqlTestTab,"idsalon<>".$id);
    $sqlTest=implode(" AND ",$sqlTestTab);
    $query="DELETE FROM chat_messages WHERE {$sqlTest}";
  }
  $result=mysql_query($query, $db) or die("Erreur lors de l'effacement d'anciens messages: ".mysql_error());
  
  // affiche les salons existants et leurs membres
  //////////////////////////////////////////////////
  //var_dump($chatrooms);
  if (count($chatrooms)==0)
    echo "Aucun salon n'est ouvert pour le moment, veuillez cliquer sur le bouton ci-dessus pour en créer un nouveau.";
  else {
    foreach ($chatrooms as $id=>$members) {
      echo '<div id="roomList">';
      echo <<<EOS
<form class="inline" action="chat_room.php" method="get">
<input type="submit" value="Entrer dans le salon n°{$id}" title="Cliquez rejoindre les membres"/>
<input type="hidden" name="id" value="{$id}"/>
</form>
EOS;
      echo "avec: ";
      echo implode("",
	array_map(
	  create_function('$m','return "<span class=\"chatmember\">{$m}</span>";'),
	  $members));
      echo "</div>";
    }
  }
?> 

</div>

</body>
</html>
