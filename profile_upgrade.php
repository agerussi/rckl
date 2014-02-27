<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
 <head>
  <?php require("menu_header.php"); ?>
  <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDyvgizLu1uatxqXBPomR4EHsMDipLin4s&sensor=false&langage=fr&region=FR"></script>
  <script type="text/javascript" src="OUTILS/JSDATEPICK/jsDatePick.min.1.3.js"></script>
  <link rel="stylesheet" type="text/css" media="all" href="OUTILS/JSDATEPICK/jsDatePick_ltr.min.css" />

  <script type="text/javascript">
    var newProfile=true;
    var upgradeProfile=true;
  </script>
  <script type="text/javascript" src="profile_edition.js"></script>
 </head>
<body>
 <?php require("menu_body.php"); ?>
<!-- <a target="_blank" href="help_profile_new.php"><img class="helpIcon" src="ICONS/help.png" alt="Icône d'aide" title="Aide pour cette page"/></a> -->

  <h1>Mise à jour d'un compte RCKL</h1>

  <h2><blink>Votre compte n'est plus à jour !</blink></h2>

 <p>La mise à jour de votre compte est indispensable; sans elle vous ne pourrez plus vous connecter. Le formulaire ci-dessous va vous permettre de renseigner les informations manquantes.
  Les renseignements marqués d'une étoile * sont <em>définitifs</em>: ils ne pourront pas être modifiés par la suite.
  D'autres informations complémentaires et facultatives pourront être renseignées dans un second temps, en choisissant «modifier votre profil» dans le menu des membres.
  </p>

  <form accept-charset="utf-8" method="post" action="profile_saveupgrade.php" id="profileForm">

<?php
  require("profile_common.php");
  // création du formulaire 
  profileEntry("Nom*",
    champTexte("nom",16,""),
    message("nom")
  );
  profileEntry("Prénom*",
    champTexte("prenom",16,""),
    message("prenom")
  );
  profileEntry("Identifiant*",
    commentaire("Ceci est le nom sous lequel vous apparaissez aux autres membres quand vous agissez sur le site. Ce n'est pas votre nom de connexion, ce dernier est resté inchangé. Vous pouvez modifier la proposition automatique ci-dessous, mais choisissez quelque chose ressemblant à vos véritables nom et prénom."),
    champTexte("nomprofil",16,""),
    message("nomprofil")
  );
  profileEntry("Date de Naissance*",
    commentaire("Seul votre âge apparaît aux autres membres."),
    '<input type="text" size="10" id="datenaissance" name="datenaissance" readonly="readonly" value=""/>',
    message("datenaissance")
  );
  profileEntry("Coordonnées GPS",
    commentaire("Placez le pointeur <em>grosso modo</em> sur votre domicile avec un clic-droit. Une précision «au kilomètre» est suffisante."),
    '<div id="map-canvas"></div>',
    message("gps"),
    '<input id="latitude" name="latitude" type="text" size="10" style="display:none">',
    '<input id="longitude" name="longitude" type="text" size="10" style="display:none">'
  );
?>

    <input type="button" id="submitbutton" value="Mettre le profil à jour" title="Relisez bien les informations avant de cliquer!" />
  </form>
 </body>
</html>
