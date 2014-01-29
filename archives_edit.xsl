<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="html" indent="no" omit-xml-declaration="yes" encoding="utf-8"/>

<xsl:template match="/editsortie">
  <form enctype="multipart/form-data" accept-charset="utf-8" method="post" action="{concat('archives_save.php?id=',@id)}" onsubmit="return validationArchive()"> 
    <xsl:apply-templates select="date"/>
    <xsl:if test="not(titre)">
      <xsl:call-template name="titreDefault"/>
    </xsl:if>
    <xsl:apply-templates select="titre"/>
    <xsl:if test="not(participants)">
      <xsl:call-template name="participantsDefault"/>
    </xsl:if>
    <xsl:apply-templates select="participants"/>
    <xsl:if test="not(commentaire)">
      <xsl:call-template name="commentaireDefault"/>
    </xsl:if>
    <xsl:apply-templates select="commentaire"/>
    <div id="listeMedias">
    </div>
    <input type="hidden" id="xmlmedias" name="xmlmedias"/>
    <script type="text/javascript">
      function createMedias() { 
        <xsl:apply-templates select="photo|video|vimeo"/>
      }
    </script>
    <div id="validannulation">
      <p>
	Ajouter une photo: 
	<input type="file" id="ajoutFichiers" multiple="multiple" />
      </p>
      <p>
	Ajouter une vidéo Vimeo: 
	<input type="text" id="VimeoId"/>
        <img title="ajouter une vidéo Vimeo" id="ajouterVimeo" src="ICONS/b_add.png"/> 
      </p>
      <p>
	Ajouter une vidéo YouTube:
	<input type="text" id="YouTubeId"/>
	<img title="ajouter une vidéo YouTube" id="ajouterYouTube" src="ICONS/b_add.png"/> 
      </p>
      <p>
	<input type="submit" name="archivesubmit" value="Modifier l'archive" />
      </p>
      <p>
	<input type="button" id="cancel" value="Annuler" />
      </p>
    </div>
  </form>
  <input type="file" id="ajoutMiniature" style="display:none"/>
  <div id="zoneSaisie">
    <textarea cols="50" rows="5" id="inputCommentaire"/>
    <input type="button" value="Modifier" id="boutonModifierCommentaire"/>
    <input type="button" value="Annuler" id="boutonAnnulerCommentaire"/>
  </div>
</xsl:template>

<xsl:template match="date">
  <label class="archiveedit">Date de la sortie: </label>
  <input class="archiveedit" type="text" size="10" id="valeurdate" name="valeurdate" readonly="readonly" value="{concat(@jour,'-',@mois,'-',@annee)}"/>
  <div class="bigskip"/>
  <label class="archiveedit">Date spéciale: </label>
  <input class="archiveedit" type="text" size="20" name="valeurtextedate" value="{@texte}" />
  <div class="bigskip"/>
</xsl:template>

<xsl:template match="titre">
  <label class="archiveedit">Titre de la sortie: </label>
  <input class="archiveedit" type="text" size="20" name="valeurtitre" value="{normalize-space(.)}" />
  <div class="bigskip"/>
</xsl:template>

<xsl:template name="titreDefault">
  <label class="archiveedit">Titre de la sortie: </label>
  <input class="archiveedit" type="text" size="20" name="valeurtitre" value="" />
  <div class="bigskip"/>
</xsl:template>

<xsl:template match="participants">
  <label class="archiveedit">Liste des participants: </label>
  <input type="hidden" id="listeparticipants" name="listeparticipants"/>
  <div id="ligneparticipants">
    <xsl:for-each select="nom">
      <span class="participant" name="participant">
	<xsl:value-of select="normalize-space(.)"/>
	<img title="supprimer ce participant" src="ICONS/b_drop.png" name="supprimerparticipant"/> 
      </span>
    </xsl:for-each>
  </div>
  <div id="ligneajout">
    <input type="text" size="10" id="nouveauparticipant"/>
    <img title="ajouter un participant" id="ajouterparticipant" src="ICONS/b_add.png"/> 
  </div>
  <div id="suggestions"></div>
  <div class="bigskip"/>
</xsl:template>

<xsl:template name="participantsDefault">
  <label class="archiveedit">Liste des participants: </label>
  <input type="hidden" id="listeparticipants" name="listeparticipants"/>
  <div id="ligneparticipants">
  </div>
  <div id="ligneajout">
    <input type="text" size="10" id="nouveauparticipant"/>
    <img title="ajouter un participant" id="ajouterparticipant" src="ICONS/b_add.png"/> 
  </div>
  <div id="suggestions"></div>
  <div class="bigskip"/>
</xsl:template>

<xsl:template match="commentaire">
  <label class="archiveedit">Commentaire (code HTML explicite): </label>
  <textarea class="archiveedit" cols="80" rows="5" name="valeurcommentaire" id="valeurcommentaire">
    <xsl:apply-templates select="text()|*"/>
  </textarea>
  <div class="bigskip"/>
</xsl:template>

<xsl:template name="commentaireDefault">
  <label class="archiveedit">Commentaire (code HTML explicite): </label>
  <textarea class="archiveedit" cols="80" rows="5" name="valeurcommentaire" id="valeurcommentaire">
  </textarea>
  <div class="bigskip"/>
</xsl:template>

<xsl:template match="photo">
  mediaList.push(new Photo("<xsl:value-of select="@commentaire"/>","<xsl:value-of select="@fichier"/>"));
</xsl:template>

<xsl:template match="video">
  mediaList.push(new Video("<xsl:value-of select="@commentaire"/>","<xsl:value-of select="@fichier"/>"));
</xsl:template>

<xsl:template match="vimeo">
  mediaList.push(new Vimeo("<xsl:value-of select="@commentaire"/>","<xsl:value-of select="@url"/>", "<xsl:value-of select="@miniurl"/>"));
</xsl:template>

<!--
<xsl:template match="text()">
  <xsl:value-of select="normalize-space(.)"/>
</xsl:template>
-->

<xsl:template match="*">
  <xsl:copy-of select="."/>
</xsl:template>

</xsl:stylesheet>

