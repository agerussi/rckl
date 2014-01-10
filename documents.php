<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("menuh.php"); ?>
</head>
<body>
<?php
  require("background.html");
  require("menub.php"); 
  $connected=isset($_SESSION['login']);
?>
<h1>DOCUMENTS</h1>

<h3>Ressources CK</h3>
<ul>
  <li><a href="http://alexandre.gerussi.free.fr/ik.html">Initiation Kayak</a> - les bases du kayak en vidéo.</li>
  <li>Une compilation des techniques d'<a href="http://alexandre.gerussi.free.fr/ROLL/roll.html">esquimautage</a>.</li>
  <li>Les <a href="DOCS/Nage-en-eau-vive.pdf">fondamentaux de la nage en eau vive</a> (NEV), par Patrick Delvallée.</li>
  <li>Un <a href="http://www.rivieres.info">site instructif et complet</a> sur divers aspects du Canoë-Kayak.</li>
</ul>

<h3>Pagaies Couleurs FFCK - Référence</h3>
<ul>
  <li> La <a href="DOCS/FE_TM_B.pdf">fiche</a> des compétences pour la pagaie BLANCHE, tous milieux.</li>
  <li> La <a href="DOCS/FE_TM_J.pdf">fiche</a> des compétences pour la pagaie JAUNE, tous milieux.</li>
  <li> La <a href="DOCS/FE_EC_V.pdf">fiche</a> des compétences pour la pagaie VERTE en EAUX CALMES.</li>
  <li> La <a href="DOCS/FE_EV_V.pdf">fiche</a> des compétences pour la pagaie VERTE en EAUX VIVES.</li>
  <li> La <a href="DOCS/FE_EC_B.pdf">fiche</a> des compétences pour la pagaie BLEUE en EAUX CALMES.</li>
  <li> La <a href="DOCS/FE_EV_B.pdf">fiche</a> des compétences pour la pagaie BLEUE en EAUX VIVES.</li>
  <li> La <a href="DOCS/FE_EC_R.pdf">fiche</a> des compétences pour la pagaie ROUGE en EAUX CALMES.</li>
  <li> La <a href="DOCS/FE_EV_R.pdf">fiche</a> des compétences pour la pagaie ROUGE en EAUX VIVES.</li>
</ul>
</body>
</html>
