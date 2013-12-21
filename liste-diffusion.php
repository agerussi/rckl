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
<h1>LISTE DE DIFFUSION</h1>
<p>La liste de diffusion <b>gl.asl@ml.free.fr</b> est, en parallèle avec ce site, le moyen privilégié de communication au sein du RCKL.
Par ailleurs vous pouvez communiquer avec un membre particulier par l'intermédiaire du trombinoscope.
<br/>
L'inscription à cette liste est réservée aux membres inscrits, ou à titre exceptionnel à des personnes extérieures connues.
Cependant, des personnes non inscrites peuvent poster sur la liste mais leur message sera d'abord modéré.
</p>
<p>Attention: <em>vérifiez bien que l'adresse mail que vous utilisez pour envoyer le mail d'inscription est celle par laquelle vous voulez recevoir et envoyer les messages à l'avenir.</em>
Pour utiliser plusieurs adresses il faut vous inscrire plusieurs fois, pour chacune d'entre elles.
</p>
<ul>
  <li> Pour vous inscrire, envoyez ce mail: <a href="mailto:gl.asl-request@ml.free.fr?subject=subscribe">gl.asl-request@ml.free.fr?subject=subscribe</a>.</li>
  <li> Pour vous désinscrire, envoyez ce mail: <a href="mailto:gl.asl-request@ml.free.fr?subject=unsubscribe">gl.asl-request@ml.free.fr?subject=unsubscribe</a>.</li>
  <li> Une fois inscrit(e), vous pourrez poster sur la liste via cette adresse: <a href="mailto:gl.asl@ml.free.fr">gl.asl@ml.free.fr</a></li>
  <li> Enfin, pour contacter spécialement l'administrateur de la liste en cas de problème: <a href="mailto:gl.asl-owner@ml.free.fr">gl.asl-owner@ml.free.fr</a>.</li>
</ul>
NB: si l'utilisation de ces liens pose problème vous pouvez aussi envoyer un mail de votre propre chef. 
Par exemple pour vous inscrire, envoyez un mail à l'adresse <i>gl.asl-request@ml.free.fr</i> avec comme titre le seul et unique mot <i>subscribe</i>.
</body>
</html>
