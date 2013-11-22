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
  $connected=isset($_SESSION['login']);
?>
<h1>DOCUMENTS</h1>

  <span style="background-color:gray">Dossier d'inscription au GL, saison 2013</span>: <a href="DOCS/inscription-page1sur2.png">page 1</a> et <a href="DOCS/inscription-page2sur2.png">page 2</a>.

<h3>Ressources CK</h3>
<ul>
  <li> <a href="ik.html">Initiation Kayak</a> - les bases du kayak en vidéo.</li>
  <li> Une compilation des techniques d'<a href="http://alexandre.gerussi.free.fr/ROLL/roll.html">esquimautage</a>.</li>
  <li> Les <a href="DOCS/Nage-en-eau-vive.pdf">fondamentaux de la nage en eau vive</a> (NEV), par Patrick Delvallée.</li>
  <li> Un <a href="http://www.rivieres.info">site instructif et complet</a> sur divers aspects du Canoë-Kayak.</li>
</ul>

<h3>Référence Pagaies Couleurs</h3>
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

<h3>AG du groupe loisirs</h3> 
<?php
  if (!$connected) echo '<p><a href="loginpage.php?target=documents.php?menu">Connectez-vous</a> pour accéder aux comptes-rendus.</p>';
  else echo <<<EOS
<ul>
  <li> <a href="DOCS/cr13.pdf">Compte rendu</a> AG groupe loisirs 2013.</li>
  <li> <a href="DOCS/cr10.pdf">Compte rendu</a> AG groupe loisirs 2010.</li>
  <li> <a href="DOCS/cr07.pdf">Compte rendu</a> AG groupe loisirs 2007.</li>
  <li> <a href="DOCS/cr06.pdf">Compte rendu</a> AG groupe loisirs 2006.</li>
  <li> <a href="DOCS/cr05.pdf">Compte rendu</a> AG groupe loisirs 2005.</li>
</ul>
EOS;
?>
<h3>Documents et liens divers</h3>
<ul>
  <li> La <a href="DOCS/fichedesortie.pdf">fiche de sortie</a> à l'usage de l'organisateur d'une sortie officielle GL.</li>
  <li>
    <?php
      if (!$connected) echo '<a href="loginpage.php?target=documents.php?menu">Connectez-vous</a> pour accéder au règlement du groupe loisirs.';
      else echo 'Le <a href="DOCS/règlement-GL.pdf">règlement</a> du groupe: vos droits et devoirs en tant que membre (en cours de validation)';
    ?>
  </li>
</ul>
</body>
</html>
