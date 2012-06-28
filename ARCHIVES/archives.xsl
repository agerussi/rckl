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
    </div>
    <div class="commentaire">
      <xsl:apply-templates select="commentaire"/>
    </div>
    <div class="galerie">
      <xsl:apply-templates select="photo"/>
      <xsl:apply-templates select="video"/>
    </div>
  </div>
  <hr/>
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
      <xsl:value-of select="concat(', ',normalize-space(.))"/>
    </xsl:for-each>
  </xsl:variable>
  <xsl:value-of select="concat(substring($liste,3),'.')"/>
</xsl:template>

<xsl:template match="commentaire">
  <!-- <xsl:value-of select="normalize-space(.)"> 
  </xsl:value-of> -->
    <xsl:apply-templates select="text()|*"/>
</xsl:template>

<xsl:template match="photo">
  <span class="miniature">
    <xsl:element name="a">
      <xsl:attribute name="title">
	<xsl:value-of select="@commentaire"/>
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
    <xsl:element name="table">
      <xsl:element name="tr">
	<xsl:element name="td">
	  <xsl:element name="a">
	    <xsl:attribute name="type">
	      <xsl:value-of select="@type"/>
	    </xsl:attribute>
	    <xsl:attribute name="title">
	      <xsl:value-of select="@commentaire"/>
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
	  </xsl:element>
	</xsl:element>
	<xsl:if test="@vimeo">
	  <xsl:element name="td">
	    <xsl:element name="a">
	      <xsl:attribute name="href">
		<xsl:value-of select="concat('http://vimeo.com/',@vimeo)"/>
	      </xsl:attribute>
	      <xsl:attribute name="title"> 
		<xsl:text>La vidéo sur VIMEO</xsl:text>
	      </xsl:attribute>
	      <xsl:element name="img">
		<xsl:attribute name="alt"/>
		<xsl:attribute name="src">
		  <xsl:value-of select="concat(/archive/path,'/vimeo-mini.jpg')"/>
		</xsl:attribute>	
	      </xsl:element>
	    </xsl:element>
	  </xsl:element>
	</xsl:if>
      </xsl:element>
    </xsl:element>
  </div>
</xsl:template>

<!-- <xsl:template match="text()">
  <xsl:value-of select="normalize-space(.)"/>
</xsl:template>
-->

<xsl:template match="*">
  <xsl:copy-of select="."/>
</xsl:template>

</xsl:stylesheet>
