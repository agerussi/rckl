<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output indent="yes" omit-xml-declaration="yes" encoding="utf-8"/>

<xsl:template match="newslist">
  <xsl:apply-templates select="news"/>
</xsl:template>

<xsl:template match="news">
 <div class="news">
 <span class="date">
   <xsl:value-of select="date"/>
 </span>
 <span class="corps">
   <xsl:value-of select="corps"/>
 </span>
 <span class="auteur">
   <xsl:value-of select="auteur"/>
 </span>
 </div>
 <hr />
</xsl:template>

</xsl:stylesheet>
