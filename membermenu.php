<?php
// gestion de l'entrée "connexion" du menu

if(isset($_SESSION['login'])){ // on est loggé

  echo '<span style="color:red">'.$_SESSION['realname'].'</span>'; 
  echo <<<EOS
<ul>
<li><a href="newspost-page.php">poster une news</a></li>
<li><a href="editsortie.php?ids=-1">proposer une nouvelle sortie</a></li>
<li><a href="gestiondesfrais.php">gestion des frais</a></li>
<li><a href="chatroom.php?menu">salon de discussion (en développement)</a></li>
<li><a href="chpasswd-page.php">changer le mot de passe</a></li>
<li><a href="logout.php">déconnexion</a></li>
EOS;
  if ($_SESSION['login'] == 'root') { // additions spéciales pour root
    echo '<li><a href="nouvelle-archive.php">nouvelle archive</a></li>';
  }
  echo "</ul>";
}
else{
echo '<a href="loginpage.php">CONNEXION</a>';
}
?>

