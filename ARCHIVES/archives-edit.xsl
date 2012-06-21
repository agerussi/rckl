<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="html" indent="no" omit-xml-declaration="yes" encoding="utf-8"/>

<xsl:template match="/editsortie">
  <!-- <form accept-charset="utf-8" method="post" action="enregistre-archive.php" onsubmit="return validationArchive()"> -->
  <xsl:element name="form">
    <xsl:attribute name="accept-charset">utf-8</xsl:attribute>
    <xsl:attribute name="method">post</xsl:attribute>
    <xsl:attribute name="action">
      <xsl:value-of select="concat('enregistre-archive.php?id=',@id)" />
    </xsl:attribute>
    <xsl:attribute name="onsubmit">return validationArchive()</xsl:attribute>


    <xsl:apply-templates select="date"/>
    <xsl:apply-templates select="titre"/>
    <xsl:apply-templates select="participants"/>
    <xsl:apply-templates select="commentaire"/>
    <div id="listePhotos">
      <xsl:apply-templates select="photo"/>
    </div>  
    <div id="listeVideos">
      <xsl:apply-templates select="video"/> 
    </div>
    <p>Ajout de fichiers: <input type="file" id="ajoutFichiers" multiple="multiple" /></p>
    <p><input type="submit" name="archivesubmit" value="Modifier l'archive" /></p>
    <p><input type="button" id="cancel" value="Annuler" /></p>
  </xsl:element>
  <div id="zoneSaisie" style="display:none">
    <textarea cols="50" rows="5" id="inputCommentaire"/>
    <input type="button" value="Modifier" onclick="enregistrerCommentaire(true)"/>
    <input type="button" value="Annuler" onclick="enregistrerCommentaire(false)"/>
  </div>
</xsl:template>

<xsl:template match="date">
  <p>
    <label>Date de la sortie: </label>
    <xsl:element name="input">
      <xsl:attribute name="type">text</xsl:attribute>
      <xsl:attribute name="size">10</xsl:attribute>
      <xsl:attribute name="id">valeurdate</xsl:attribute>
      <xsl:attribute name="name">valeurdate</xsl:attribute>
      <xsl:attribute name="readonly">readonly</xsl:attribute>
      <xsl:attribute name="value">
	<xsl:value-of select="concat(@jour,'-',@mois,'-',@annee)" />
      </xsl:attribute>
    </xsl:element>
  </p>
  <p>
    <label>Date spéciale: </label>
    <xsl:element name="input">
      <xsl:attribute name="type">text</xsl:attribute>
      <xsl:attribute name="size">20</xsl:attribute>
      <xsl:attribute name="name">valeurtextedate</xsl:attribute>
      <xsl:attribute name="value">
        <xsl:value-of select="@texte"/>
      </xsl:attribute>
    </xsl:element>
  </p>
</xsl:template>

<xsl:template match="titre">
  <p>
    <label>Titre de la sortie: </label>
    <xsl:element name="input">
      <xsl:attribute name="type">text</xsl:attribute>
      <xsl:attribute name="size">20</xsl:attribute>
      <xsl:attribute name="name">valeurtitre</xsl:attribute>
      <xsl:attribute name="value">
	<xsl:value-of select="normalize-space(.)"/>
      </xsl:attribute>
    </xsl:element>
  </p>
</xsl:template>

<xsl:template match="participants">
  <p>
    <label>Liste des participants: </label>
    <input type="hidden" id="listeparticipants" name="listeparticipants"/>
    <xsl:for-each select="nom">
      <span class="participant" name="participant">
	<xsl:value-of select="normalize-space(.)"/>
        <img title="supprimer ce participant" src="FONDS/b_drop.png" name="supprimerparticipant"/> 
      </span>
    </xsl:for-each>
    <input type="text" size="10" id="nouveauparticipant"/>
    <img title="ajouter un participant" id="ajouterparticipant" src="FONDS/b_add.png"/> 
    <span id="suggestions"></span>
  </p>
</xsl:template>

<xsl:template match="commentaire">
  <p>
    <label>Commentaire: </label>
    <textarea cols="50" rows="5" name="valeurcommentaire" id="valeurcommentaire">
      <!-- <xsl:apply-templates select="text()|*"/> -->
      <xsl:value-of select="."/>
    </textarea>
  </p>
</xsl:template>

<xsl:template match="photo">
  <table>
    <tr>
      <td>
	<xsl:element name="img">
	  <xsl:attribute name="name">photo</xsl:attribute>
	  <xsl:attribute name="title">
	    <xsl:value-of select="@commentaire"/>
	  </xsl:attribute>
	  <xsl:attribute name="src">
	    <xsl:variable name="ext" select="substring(@fichier,string-length(@fichier)-3)"/>
	    <xsl:value-of select="concat(/editsortie/path,'/',substring-before(@fichier,$ext),/editsortie/mini,$ext)"/>
	  </xsl:attribute>
	  <xsl:attribute name="alt">
	    <xsl:value-of select="@fichier"/>
	  </xsl:attribute>
	</xsl:element>
      </td>
    </tr>
    <tr>
      <td>
        <img title="supprimer la photo" src="FONDS/b_drop.png" name="supprimerphoto"/> 
	<input type="hidden" name="typeMedia" value="3"/> <!-- 3 = On+Photo+!New -->
	<img title="éditer le commentaire" src="FONDS/b_edit.png" name="editercommentaire"/> 
	<xsl:element name="input">
	  <xsl:attribute name="type">hidden</xsl:attribute>
	  <xsl:attribute name="name">commentaireMedia</xsl:attribute>
	  <xsl:attribute name="value">
	    <xsl:value-of select="@commentaire"/>
	  </xsl:attribute>
	</xsl:element>
	<xsl:element name="input">
	  <xsl:attribute name="type">hidden</xsl:attribute>
	  <xsl:attribute name="name">nomMedia</xsl:attribute>
	  <xsl:attribute name="value">
	    <xsl:value-of select="@fichier"/>
	  </xsl:attribute>
	</xsl:element>
      </td>
    </tr>
  </table>
</xsl:template>

<xsl:template match="video">
  <table>
    <tr>
      <td>
	<img name="miniatureVideo" height="85px" title="{@commentaire}" src="{concat(/editsortie/path,'/',substring-before(@fichier,'.'),'-mini.jpg')}" alt="{@fichier}" />
        <img title="choisir une miniature" src="FONDS/insert_image.png" name="choisirminiature"/> 
	<input type="file" style="display:none" name="ajoutMiniature"/>
	<input type="hidden" name="uploadedMinis" value=""/> 
      </td>
    </tr>
    <tr>
      <td>
	<label>#VIMEO</label>
	<input type="text" size="10" id="vimeo" name="vimeo" value="{@vimeo}" />
      </td>
    </tr>
    <tr>
      <td>
        <img title="supprimer la vidéo" src="FONDS/b_drop.png" name="supprimervideo"/> 
	<input type="hidden" name="typeMedia" value="5"/> <!-- 5 = On+Video -->
	<img title="éditer le commentaire" src="FONDS/b_edit.png" name="editercommentaire"/> 
	<input type="hidden" name="commentaireMedia" value="{@commentaire}"/>
	<input type="hidden" name="nomMedia" value="{@fichier}"/>
      </td>
    </tr>
  </table>
</xsl:template>

<!-- <xsl:template match="text()">
  <xsl:value-of select="normalize-space(.)"/>
</xsl:template>
-->

<xsl:template match="*">
  <xsl:copy-of select="."/>
</xsl:template>

</xsl:stylesheet>

