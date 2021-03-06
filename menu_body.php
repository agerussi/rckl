<div id="topPagePadding"></div>
<div id="header"><img id="logo" src="LOGO/rckl-logo.svg"/><div id="headerBody"><img id="rckl-short" src="LOGO/rckl-short.png" /><img id="rckl-long" src="LOGO/rckl-long.png" />
<ul id="mainmenu" class="menuinline">

  <li><a href="news.php">NEWS</a></li>

  <li><a href="calendrier.php" title="les sorties ou autres activités en préparation" >CALENDRIER</a></li>

  <li><a href="documents.php" title="quelques documents et ressources">DOCUMENTS</a></li>

  <li><a href="trombinoscope.php" title="membres du RCKL">TROMBINOSCOPE</a></li>

  <li title="photos/vidéos des sorties"><span class="iphonefix">ARCHIVES</span>
    <ul>
      <li><a href="archives.php?y=2014">année 2014</a></li>
      <li><a href="archives.php?y=2013">année 2013</a></li>
      <li><a href="archives.php?y=2012">année 2012</a></li>
      <li><a href="archives.php?y=2011">année 2011</a></li>
      <li><a href="archives.php?y=2010">année 2010</a></li>
      <li><a href="archives.php?y=2009">année 2009</a></li>
      <li><a href="archives.php?y=2008">année 2008</a></li>
      <li><a href="archives.php?y=2007">année 2007</a></li>
      <li><a href="archives.php?y=2006">année 2006</a></li>
      <li><a href="archives.php?y=2005">année 2005</a></li>
    </ul>
  </li>

  <li>RCKL
    <ul>
      <li><a href="presentation.php" title="présentation du RCKL">PRÉSENTATION (WIP)</a></li> 
      <li><a href="liste-diffusion.php" title="gestion de votre inscription à la liste">LISTE DE DIFFUSION</a></li>
      <li><a href="profile_new.php">CRÉER UN NOUVEAU COMPTE</a></li>
      <li><a href="mailto:rckl@free.fr" title="demande d'informations par mail">CONTACT</a></li>
    </ul>
  </li>

  <li>LIENS
    <ul>
      <li><a href="http://www.eauxvives.org" target="_blank" title="l'incontournable site francophone pour l'eau vive">EAUXVIVES.ORG</a></li>
      <li><a href="http://www.kayaksportif.be" target="_blank" title="un site belge similaire au RCKL">KayakSportif.be</a></li>
    </ul>
   </li>
  
<?php 
// parties du menu réservées aux membres connectés
/////////////////////////////////////////////////////
if(isset($_SESSION['login'])){ // on est loggé
  echo <<<EOS
<li>
 <span class="iphonefix" style="color:red">{$_SESSION['profilename']}</span>
 <ul>
  <li><a href="profile_edition.php">modifier son profil</a></li>
  <li><a href="logout.php">se déconnecter</a></li>
 </ul>
</li>
<li><a href="frais_affichage.php" title="gérer ses frais">FRAIS</a></li>
<li><a href="chat.php" title="accéder aux salons de discussion">CHAT</a></li>
EOS;
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
modifier son profil,
visionner le profil d'un autre membre,
s'abonner au flux RSS,
accéder au salon de discussion"
>CONNEXION</a>
EOS;
}
?>
  </ul>
</div>
</div>
<?php 
// affichage de l'icône RSS si on est loggé
if (isset($_SESSION['login'])) {
  echo '<a href="rcklrss.xml" type="application/rss+xml"><img id="rssicon" border="0" src="ICONS/RSS-icon.png" class="icon" /></a>';
}
?>

<script type="text/javascript">
  div=document.getElementById("topPagePadding");
  div.style.height=document.getElementById("header").clientHeight+"px";
  //div.style.backgroundColor="green";
</script>
