<?php
// gestion de l'entrée "connexion" du menu

if(isset($_SESSION['login'])){ // on est loggé

  echo '<span class="iphonefix" style="color:red">'.$_SESSION['realname'].'</span>'; 
  echo <<<EOS
<ul>
<li><a href="newspost-page.php">poster une news</a></li>
<li><a href="editsortie.php?ids=-1">proposer une activité</a></li>
<li><a href="archives_new.php">créer une archive</a></li>
<li><a href="gestiondesfrais.php">gérer ses frais</a></li>
<li><a href="chatroom.php">accéder au salon de discussion</a></li>
<li><a href="chpasswd-page.php">changer le mot de passe</a></li>
<li><a href="logout.php">se déconnecter</a></li>
EOS;
  if ($_SESSION['login'] == 'root') { // additions spéciales pour root
  }
  echo "</ul>";
}
else{
  echo <<<EOS
<a href="loginpage.php"
title="permet de
proposer une activité,
s'inscrire à une activité,
écrire une news,
créer ou modifier une archive,
gérer ses frais,
contacter un membre par mail,
s'abonner au flux RSS,
accéder au salon de discussion"
>MEMBRES</a>
EOS;
}
?>

