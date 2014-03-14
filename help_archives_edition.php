<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
<?php require("head.html"); ?>
</head>
<body id="helpPage">
<h1>Aide de la page d'édition des archives</h1>
La page d'édition permet de modifier une archive existante ou de créer une nouvelle archive.
Dans tous les cas, les modifications apportées ne seront effectives qu'après avoir cliqué sur le bouton «Modifier l'archive» au bas de la page.
Le bouton «Annuler» annule toutes les modifications, et même l'archive en entier si celle-ci vient d'être créée.

<h2>Le champ «Date de l'activité»</h2>
Ce champ est <em>obligatoire</em>. 
C'est la date de référence utilisée pour classer l'activité dans le calendrier.
Si le champ «Date spéciale» est laissé libre, c'est également la date qui sera affichée dans les archives.

<h2>Le champ «Date spéciale»</h2>
Ce champ est facultatif.
Dans certains cas on veut écrire quelque chose de spécial à la place de la date: c'est le cas par exemple si l'activité s'est déroulée pendant toute une semaine, on voudra par exemple écrire «du 1 au 8 août».
On utilise alors ce champ pour cela.
S'il est utilisé, il devient prioritaire par rapport au champ «date» précédent, qui est donc complètement ignoré.
De manière générale, omettez de préciser l'année afin de respecter le style normal d'affichage des dates.

<h2>Le champ «Titre de l'activité»</h2>
Ce champ est facultatif.

<h2>La liste des participants</h2>
La liste des participants est facultative.
Pour saisir un participant, on écrit son nom dans la boîte prévue à cet effet.
Des propositions de membres apparaissent alors.
Si le participant souhaité apparaît dans les propositions, <em>il est conseillé de cliquer afin d'avoir son nom complet écrit correctement et automatiquement</em>.
Dans le cas contraire il faut écrire le nom du participant à la main.<br/>
Pour valider le participant, cliquer sur l'icône <img class="icon" src="ICONS/b_add.png"/>.
Le participant apparaît alors dans la liste, et on peut le supprimer en cliquant sur l'icône <img class="icon" src="ICONS/b_drop.png"/> qui suit immédiatement le participant.
<br/>
On constitue ainsi la liste des participants un par un.

<h2>La champ «commentaire»</h2>
Ce champ est facultatif.
Dans ce champ, les balises HTML ne sont pas désactivées, elles peuvent donc être utilisées pour formater le commentaire de manière avancée.
Évitez cependant de vous en servir si vous ne savez pas vraiment ce que vous faites, et dans tous les cas, restreignez vous à n'utiliser que des balises simples (mises en valeur, listes, sauts de lignes ou paragraphes).

<h2>L'ajout de médias</h2>
Il existe 4 types de médias rattachables à une archive:
<ul>
<li>Les fichiers photos;</li>
<li>Les fichiers vidéos (à éviter);</li>
<li>Les liens vers des vidéos de Vimeo;</li>
<li>Les liens vers des vidéos de YouTube.</li>
</ul>
Les particularités de chaque type sont expliquées ci-dessous, mais tous les médias possèdent des propriétés communes:
<ul>
<li>Le média peut être supprimé en cliquant sur son icône <img class="icon" src="ICONS/b_drop.png"/>. 
Celle-ci se transforme alors en une icône <img class="icon" src="ICONS/b_add.png"/> qui permet à son tour d'annuler la suppression.
Suppression ou ajout ne sont définitifs qu'après avoir appuyé sur le bouton «modifier l'archive».</li> 
<li>Le média peut avoir un commentaire qui lui est rattaché.
L'édition de ce commentaire se fait par l'icône <img class="icon" src="ICONS/b_edit.png"/>.</li>
<li>L'ordre d'affichage des différents médias se définit en déplaçant les médias à l'aide des flèches <img class="icon" src="ICONS/19_Left_Arrow_16x16.png"/> et <img class="icon" src="ICONS/20_Right_Arrow_16x16.png"/>.</li>
</ul>

<h3>L'ajout de photos</h3>
Pour ajouter un fichier photo, on clique sur «parcourir».
Plusieurs fichiers sont sélectionnables en même temps, ils doivent tous être de type .jpg.
Une fois les fichiers transférés sur le serveur, les miniatures apparaissent.
<br/>
La taille maximale autorisée pour un fichier photo est de 400KB.
Ceci est largement suffisant pour un affichage de bonne qualité sur l'écran d'un ordinateur.
Les archives du RCKL ne sont pas le lieu pour partager des photos HD.
Elles doivent s'afficher rapidement et en entier même sur des écrans de taille modeste.
La largeur préconisée est de 1280 pixels environ, pour une taille de l'ordre de 250KB.
On peut utiliser un logiciel comme The GIMP pour régler les dimensions d'une photo, et choisir la qualité de compression afin de réduire la taille du fichier.
<p>
On peut également transférer des fichiers vidéos, <em>mais ceci est fortement découragé au profit des liens Vimeo ou YouTube</em>. 
N'utilisez cette possibilité qu'à titre exceptionnel pour de petits fichiers (moins de 30MB).
Les médias de type «fichier vidéo» possèdent une icône <img class="icon" src="ICONS/insert_image.png"/> qui permet de spécifier une miniature pour la vidéo. La taille maximale pour les miniatures est de 10KB, sous la forme d'un fichier .jpg.
</p>

<h3>L'ajout d'une vidéo Vimeo</h3>
Il suffit de saisir le numéro de la vidéo Vimeo (qui apparaît à la fin de l'URL, pas l'URL en entier) puis d'appuyer sur l'icône <img class="icon" src="ICONS/b_add.png"/>.
La miniature est alors automatiquement récupérée depuis le site de Vimeo, et le titre de la vidéo est utilisé en commentaire.
Ce commentaire peut être modifié par la suite.

<h3>L'ajout d'une vidéo YouTube</h3>
Il suffit de saisir l'identifiant de la vidéo YouTube (qui apparaît à la fin de l'URL, pas l'URL en entier) puis d'appuyer sur l'icône <img class="icon" src="ICONS/b_add.png"/>.
La miniature est alors automatiquement récupérée depuis le site de YouTube, et le titre de la vidéo est utilisé en commentaire.
Ce commentaire peut être modifié par la suite.
</body>
</html>


