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
<?php require("menuh.php"); ?>
  <script type="text/javascript" src="chat.js"></script>
</head>
<body>

<?php
  require("menub.php"); 
  // début du programme spécifique 'chatroom'
  require("dbconnect.php");
  
  // teste s'il s'agit d'une nouvelle conversation
  $query="SELECT id FROM membres WHERE TIMESTAMPDIFF(SECOND,chattimestamp,NOW())<60";
  $result=mysql_query($query, $db) or die("Erreur lors de la collecte des membres présents: ".mysql_error());
  // si la liste est vide, une nouvelle conversation commence
  if (mysql_num_rows($result)==0) { // on efface les anciens messages
    $query="TRUNCATE TABLE chat_messages";
    mysql_query($query, $db) or die("Erreur lors de la suppression des messages de chat_messages: ".mysql_error());
  }
  
  // initialise le nombre de messages déjà envoyés
  $_SESSION['numsent']=0;
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
    <input type="text" id="chatbox"><img src="ICONS/Chat-icon.png" id="sendButton" title="envoyer le message"/>
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
