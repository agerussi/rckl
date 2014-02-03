<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="html" indent="yes" omit-xml-declaration="yes" encoding="utf-8"/>

<xsl:template match="/archive">
  <xsl:apply-templates select="sortie">
    <xsl:sort data-type="number" order="descending" select="date/@annee"/>
    <xsl:sort data-type="number" order="descending" select="date/@mois"/>
    <xsl:sort data-type="number" order="descending" select="date/@jour"/>
  </xsl:apply-templates>
</xsl:template>

<xsl:template match="sortie">
  <div class="archive">
    <div class="entete">
      <xsl:apply-templates select="date"/>
      <xsl:apply-templates select="titre"/>
      <xsl:apply-templates select="participants"/>
      <xsl:if test="@edit='yes'">
	<xsl:element name="img">
	  <xsl:attribute name="class">icon</xsl:attribute>
	  <xsl:attribute name="title">effacer la sortie</xsl:attribute>
	  <xsl:attribute name="src">ICONS/b_drop.png</xsl:attribute>
	  <xsl:attribute name="onclick">areYouSure("<xsl:value-of select="@id"/>")</xsl:attribute>
	</xsl:element>
	<xsl:element name="a">
	  <xsl:attribute name="href">
	    <xsl:value-of select="concat('archives_edition.php?id=',@id)"/>
	  </xsl:attribute>
	  <img class="icon" title="éditer la sortie" src="ICONS/b_edit.png" />
	</xsl:element>
      </xsl:if>
      <xsl:apply-templates select="auteur"/>
    </div>
    <div class="commentaire">
      <xsl:apply-templates select="commentaire"/>
    </div>
    <div class="galerie">
      <xsl:apply-templates select="photo|video|vimeo|youtube"/>
    </div>
  </div>
  <hr/>
</xsl:template>

<xsl:template match="auteur">
  <span class="auteur">
    <xsl:text>créée par </xsl:text>
    <xsl:value-of select="."/>
  </span>
</xsl:template>

<xsl:template match="date">
  <span class="date">
    <xsl:choose>
      <xsl:when test="@texte">
	<xsl:value-of select="@texte"/>
      </xsl:when>
      <xsl:otherwise>
	<xsl:value-of select="@jour"/>
	<xsl:text> </xsl:text>
	<xsl:choose>
	  <xsl:when test="@mois=1">janvier</xsl:when>
	  <xsl:when test="@mois=2">février</xsl:when>
	  <xsl:when test="@mois=3">mars</xsl:when>
	  <xsl:when test="@mois=4">avril</xsl:when>
	  <xsl:when test="@mois=5">mai</xsl:when>
	  <xsl:when test="@mois=6">juin</xsl:when>
	  <xsl:when test="@mois=7">juillet</xsl:when>
	  <xsl:when test="@mois=8">août</xsl:when>
	  <xsl:when test="@mois=9">septembre</xsl:when>
	  <xsl:when test="@mois=10">octobre</xsl:when>
	  <xsl:when test="@mois=11">novembre</xsl:when>
	  <xsl:when test="@mois=12">décembre</xsl:when>
	</xsl:choose>
      </xsl:otherwise>
    </xsl:choose>
  </span>
</xsl:template>

<xsl:template match="titre">
  <span class="titre">
    <xsl:value-of select="normalize-space(.)"/>
  </span>
</xsl:template>

<xsl:template match="participants">
  <xsl:text>avec </xsl:text>
  <xsl:variable name="liste">
    <xsl:for-each select="nom">
      <xsl:sort select="normalize-space(.)"/>
	<xsl:text>, </xsl:text>
	<xsl:call-template name="replace-string">
	  <xsl:with-param name="text" select="."/>
	  <xsl:with-param name="replace" select="'[dq]'" />
	  <xsl:with-param name="with" select="'&amp;quot;'"/>
	</xsl:call-template>
    </xsl:for-each>
  </xsl:variable>
  <xsl:value-of select="concat(substring($liste,3),'.')"/>
</xsl:template>

<xsl:template match="commentaire">
  <xsl:value-of select="."/>
</xsl:template>

<xsl:template match="vimeo|youtube">
  <span class="miniature">
    <xsl:element name="a">
      <xsl:attribute name="title">
	<xsl:call-template name="replace-string">
	  <xsl:with-param name="text" select="@commentaire"/>
	  <xsl:with-param name="replace" select="'[dq]'" />
	  <xsl:with-param name="with" select="'&amp;quot;'"/>
	</xsl:call-template>
      </xsl:attribute>
      <xsl:attribute name="href">
	  <xsl:value-of select="@url"/>
      </xsl:attribute>
      <img src="{@miniurl}" alt="{@id}" />
      <img src="ICONS/playable2.png" class="playableIcon"/>
    </xsl:element>
  </span>
</xsl:template>

<xsl:template match="photo">
  <span class="miniature">
    <xsl:element name="a">
      <xsl:attribute name="title">
	<xsl:call-template name="replace-string">
	  <xsl:with-param name="text" select="@commentaire"/>
	  <xsl:with-param name="replace" select="'[dq]'" />
	  <xsl:with-param name="with" select="'&amp;quot;'"/>
	</xsl:call-template>
      </xsl:attribute>
      <xsl:attribute name="rel">
	<xsl:value-of select="concat('lightbox-',parent::sortie/@id)"/>
      </xsl:attribute>
      <xsl:attribute name="href">
	  <xsl:value-of select="concat(/archive/path,'/',@fichier)"/>
      </xsl:attribute>
      <xsl:element name="img">
	<xsl:attribute name="src">
	  <xsl:variable name="ext" select="substring(@fichier,string-length(@fichier)-3)"/>
	  <xsl:value-of select="concat(/archive/path,'/',substring-before(@fichier,$ext),/archive/mini,$ext)"/>
	</xsl:attribute>
	<xsl:attribute name="alt">
	  <xsl:value-of select="@fichier"/>
	</xsl:attribute>
      </xsl:element>
    </xsl:element>
  </span>
</xsl:template>

<xsl:template match="video">
  <div class="miniature">
    <xsl:element name="a">
      <xsl:attribute name="type">
	<xsl:value-of select="@type"/>
      </xsl:attribute>
      <xsl:attribute name="title">
	<xsl:call-template name="replace-string">
	  <xsl:with-param name="text" select="@commentaire"/>
	  <xsl:with-param name="replace" select="'[dq]'" />
	  <xsl:with-param name="with" select="'&amp;quot;'"/>
	</xsl:call-template>
      </xsl:attribute>
      <xsl:attribute name="href">
	  <xsl:value-of select="concat(/archive/path,'/',@fichier)"/>
      </xsl:attribute>
      <xsl:element name="img">
	<xsl:attribute name="src">
	  <xsl:value-of select="concat(/archive/path,'/',substring-before(@fichier,'.'),/archive/mini,'.jpg')"/>
	</xsl:attribute>
	<xsl:attribute name="alt">
	  <xsl:value-of select="@fichier"/>
	</xsl:attribute>
      </xsl:element> <!-- img -->
      <img src="ICONS/playable.png" class="playableIcon"/>
    </xsl:element>
  </div>
</xsl:template>

<xsl:template name="replace-string">
  <xsl:param name="text"/>
  <xsl:param name="replace"/>
  <xsl:param name="with"/>
  <xsl:choose>
    <xsl:when test="contains($text,$replace)">
      <xsl:value-of select="substring-before($text,$replace)"/>
      <xsl:value-of select="$with"/>
      <xsl:call-template name="replace-string">
	<xsl:with-param name="text" select="substring-after($text,$replace)"/>
	<xsl:with-param name="replace" select="$replace"/>
	<xsl:with-param name="with" select="$with"/>
      </xsl:call-template>
    </xsl:when>
    <xsl:otherwise>
      <xsl:value-of select="$text"/>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

</xsl:stylesheet>

