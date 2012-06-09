<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php
  if (isset($_GET['menu'])) require("menuh.php"); 
  else require("head.html");
?>
</head>
<body>
<?php
  if (isset($_GET['menu'])) require("menub.php"); 
?>
<h1>PRÉSENTATION</h1>
Le <b>groupe loisirs</b> est une structure intégrée au club de canoë-kayak de Saint Laurent Blangy et regroupe des personnes, pour la plupart majeures, qui souhaitent pratiquer le kayak (sous toutes ses formes) dans un but de détente et de découverte.
Le <b>groupe loisirs</b> ne dispose pas d'un cadre diplômé spécialement rattaché: c'est un groupe qui s'auto-gère, mutualise les ressources, et chaque membre du groupe progresse au contact des autres membres plus expérimentés.
<p>
Les activités du <b>groupe loisirs</b> se déclinent sous diverses formes:</p>
<ul>
<li> des séances en eau vive dans le cadre de la base nautique de SLB: kayak principalement, mais aussi canoë, raft, nage en eau vive...</li>
<li> des sorties allant d'une demi-journée à plusieurs jours, au minimum une fois par mois;</li>
<li> des séances en eau plate: travail technique, kayak-polo, kayak de descente ou de course en ligne; </li>
<li> des séances d'esquimautage à la piscine d'Avion, en semaine, de 21h à 22h30, pendant la saison hivernale.</li>
</ul>

Un membre du <b>groupe loisirs</b> a également la possibilité de profiter des autres activités proposées aux membres du club: accès à la salle de musculation,  VTT, footing.
<p>
<u>Horaires</u>: le samedi après midi, de 14h à 17h environ. Possibilité de venir en semaine également pour les pratiquants débrouillés.</p>
<p>
<u>Organisation des séances</u>: 
Les séances ne sont pas organisées: chaque membre est libre de venir ou non, et de pratiquer comme bon lui semble.
La personne qui désire être plus encadrée et profiter de conseils se tournera vers un membre expérimenté en début de séance.
</p>
<p>
<u>Cotisation</u>: la saison s'étend de début septembre à fin août. Le prix est légèrement variable d'années en années, mais à titre indicatif:</p>
<ul>
<li> un chèque d'environ 100 euros pour la licence FFCK et l'inscription au club, à l'ordre de "ASL Saint-Laurent";</li>
<li> un chèque d'environ 50 euros pour la base (accès à l'eau vive), à l'ordre du "Trésor Public";</li>
<li> un certificat médical attestant la non contre-indication de la pratique du Canoë-Kayak;</li>
<li> 6 timbres.</li>
</ul>
<u>Liste de diffusion</u>
La liste de diffusion <b>gl.asl@ml.free.fr</b> est le moyen privilégié de communication au sein du groupe loisirs.
<ul>
  <li> Pour vous inscrire, envoyez ce mail: <a href="mailto:gl.asl-request@ml.free.fr?subject=subscribe">gl.asl-request@ml.free.fr?subject=subscribe</a>.</li>
  <li> Pour poster sur la liste: <a href="mailto:gl.asl@ml.free.fr">gl.asl@ml.free.fr</a></li>
  <li> Pour vous désinscrire, envoyez ce mail: <a href="mailto:gl.asl-request@ml.free.fr?subject=unsubscribe">gl.asl-request@ml.free.fr?subject=unsubscribe</a>.</li>
  <li> Enfin, pour contacter spécialement l'administrateur de la liste: <a href="mailto:gl.asl-owner@ml.free.fr">gl.asl-owner@ml.free.fr</a>.</li>
</ul>
</body>
</html>
