<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" indent="yes" omit-xml-declaration="yes" encoding="utf-8"/>

<xsl:template match="/">
  <h2>Les membres actifs</h2>
  <xsl:apply-templates select="trombinoscopes/trombinoscope[@id='actifs']"/>
  <h2>Anciens membres</h2>
  <xsl:apply-templates select="trombinoscopes/trombinoscope[@id='anciens']"/>
</xsl:template>

<xsl:template match="trombinoscope">
  <div>
  <xsl:apply-templates select="membre">
    <xsl:sort select="nom"/>
  </xsl:apply-templates>
  </div>
</xsl:template>

<xsl:template match="membre">
  <table class="trombi">
    <tr>
      <td>
	<img src="{concat(../path,'/',photo)}" alt="photo" />
      </td>
    </tr>
    <tr>
      <td>
	<xsl:if test="email">
	  <a href="{concat('mailto:',email)}" title="contacter par mail">
	    <xsl:value-of select="nom"/>
	  </a>
	</xsl:if>
	<xsl:if test="not(email)">
	  <xsl:value-of select="nom"/>
	</xsl:if>
      </td>
    </tr>
  </table>
</xsl:template>

</xsl:stylesheet>
