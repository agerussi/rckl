<?php 
  session_start(); 
  // tests préalables
  if (!isset($_SESSION['userid']) || empty($_SESSION['userid']) || !isset($_GET['id'])) {
    header("Location: news.php");
  }
  $id=$_GET['id']; // n° du salon
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("menu_header.php"); ?>
  <script type="text/javascript" src="chat_room.js"></script>
</head>
<body>

<?php
  require("menu_body.php"); 
  // communique le n° à chatroom.js
  echo '<input type="hidden" name="roomNum" value="'.$id.'"/>';

  // déclare le membre dans le salon
  // test pour voir s'il y est déjà
  require_once("dbconnect.php");
  $query="SELECT COUNT(*) as nb FROM chat_rooms WHERE id={$id} AND idmembre={$_SESSION['userid']}";
  $result=mysql_query($query, $db) or die("Erreur lors du test de présence: ".mysql_error());
  $row=mysql_fetch_array($result);
  if ($row['nb']==0) {
    // on crée une entrée
    $query="INSERT INTO  chat_rooms (id,idmembre,nomprofil) VALUES({$id},{$_SESSION['userid']},'{$_SESSION['profilename']}')";
    //echo $query;
    $result=mysql_query($query, $db) or die("Erreur lors de l'insertion du membre dans le salon: ".mysql_error());
  }

  // initialise l'id du dernier message lu
  $_SESSION["lastread"][$id]=0;
?>

<!-- #####################################
####### CODE HTML ########################
###################################### -->
<div id="chatpage">
  <!-- exemple de message 
  <div class="chatmessage"> 
    <span class="chatauteur">Titi</span>
    <span class="chatmessagebody"> Salut!  </span> 
  </div>
  -->
  <div id="chatfooter">
    <input type="text" id="chatbox"><img src="ICONS/Chat-icon.png" class="icon" id="sendButton" title="envoyer le message"/>
    <div id="chatmembers">
      <span>Actuellement dans le salon:</span>
      <!-- exemple de membre
      <span class="chatmember" name="chatmember">Alexandre</span>
      -->
    </div>
  </div>
</div>
</body>
</html>
