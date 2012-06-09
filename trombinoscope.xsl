<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" indent="yes" omit-xml-declaration="yes" encoding="utf-8"/>

<xsl:template match="/">
  <h1>TROMBINOSCOPE</h1>
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
	<xsl:element name="img">
	  <xsl:attribute name="src">
	    <xsl:value-of select="concat(../path,'/',photo)"/>
	  </xsl:attribute>
	  <xsl:attribute name="alt">photo</xsl:attribute>
	</xsl:element>
      </td>
    </tr>
    <tr>
      <td>
	<xsl:value-of select="nom"/>
      </td>
    </tr>
  </table>
</xsl:template>

</xsl:stylesheet>
