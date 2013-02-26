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
// par le JS après chargement

  require("dbconnect.php");

  $query="INSERT INTO chat_members (id, nom) VALUES(".$_SESSION[userid].",'".$_SESSION['realname']."')";
  //echo $query."<br/>";
  mysql_query($query, $db) or die("Erreur lors de l'insertion du membre dans chat_members: ".mysql_error());
?>

<!-- #####################################
####### CODE HTML ########################
###################################### -->
<div id="chatpage">
  <div class="chatmessage"> 
    <span class="chatauteur">Titi</span>
    <span class="chatmessagebody">
      aze oiazeo paojzepl ajze j azpje jazpje japzje pjazepaze zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf 
    </span> 
  </div>
  <div class="chatmessage"> 
    <span class="chatauteur">Titi</span>
    <span class="chatmessagebody">
      aze oiazeo paojzepl ajze j azpje jazpje japzje pjazepaze zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf 
    </span> 
  </div>
  <div class="chatmessage"> 
    <span class="chatauteur">Titi</span>
    <span class="chatmessagebody">
      aze oiazeo paojzepl ajze j azpje jazpje japzje pjazepaze zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf 
    </span> 
  </div>
  <div class="chatmessage"> 
    <span class="chatauteur">Titi</span>
    <span class="chatmessagebody">
      aze oiazeo paojzepl ajze j azpje jazpje japzje pjazepaze zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf zerpoezrtopert eprto eprt oerpto epro pero peozesf 
    </span> 
  </div>
  <div id="chatfoot">
    <input type="text" id="chatbox">
    <div id="chatmembers">
      Actuellement dans le salon:
      <span class="chatmember">Alexandre</span>
      <span class="chatmember">Mathieu VDB</span>
      <span class="chatmember">Pierre</span>
      <span class="chatmember">Stefan</span>
    </div>
  </div>
</div>
</body>
</html>
