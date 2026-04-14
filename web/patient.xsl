<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    exclude-result-prefixes="xsi">

    <xsl:output method="html" encoding="UTF-8" indent="yes"/>
    <xsl:strip-space elements="*"/>

    <xsl:template match="/">
        <html>
            <head>
                <meta charset="UTF-8" />
                <title>Patient New XML</title>
                <style>
                    body {
                        margin: 0;
                        font-family: "Segoe UI", Arial, sans-serif;
                        background: #f3f6fb;
                        color: #1f2937;
                    }
                    .wrap {
                        max-width: 1100px;
                        margin: 24px auto;
                        padding: 0 16px;
                    }
                    .card {
                        background: #ffffff;
                        border: 1px solid #d8e1ee;
                        border-radius: 12px;
                        box-shadow: 0 6px 16px rgba(16, 24, 40, 0.06);
                        overflow: hidden;
                    }
                    .head {
                        padding: 14px 16px;
                        border-bottom: 1px solid #e7edf7;
                        background: linear-gradient(135deg, #f8fbff, #eef5ff);
                        font-weight: 700;
                    }
                    .content {
                        margin: 0;
                        padding: 16px;
                        white-space: pre-wrap;
                        word-break: break-word;
                        font-family: Consolas, "Courier New", monospace;
                        font-size: 13px;
                        line-height: 1.45;
                    }
                    .hint {
                        font-size: 12px;
                        color: #5f6f86;
                        margin-top: 10px;
                    }
                    .tag { color: #0f4fa8; }
                    .attr { color: #8f2d56; }
                    .value { color: #0a6f4f; }
                </style>
            </head>
            <body>
                <div class="wrap">
                    <div class="card">
                        <div class="head">Patient New XML Viewer</div>
                        <pre class="content"><xsl:apply-templates select="*" mode="pretty"/></pre>
                    </div>
                    <div class="hint">Rendered with patient.xsl so browser does not show default XML warning text.</div>
                </div>
            </body>
        </html>
    </xsl:template>

    <xsl:template match="*" mode="pretty">
        <xsl:param name="depth" select="0"/>
        <xsl:call-template name="indent">
            <xsl:with-param name="n" select="$depth"/>
        </xsl:call-template>
        <span class="tag">&lt;<xsl:value-of select="name()"/></span>
        <xsl:for-each select="@*">
            <span class="attr"> <xsl:value-of select="name()"/>="</span><span class="value"><xsl:value-of select="."/></span><span class="attr">"</span>
        </xsl:for-each>

        <xsl:choose>
            <xsl:when test="*">
                <span class="tag">&gt;</span>
                <xsl:text>&#10;</xsl:text>
                <xsl:for-each select="*">
                    <xsl:apply-templates select="." mode="pretty">
                        <xsl:with-param name="depth" select="$depth + 1"/>
                    </xsl:apply-templates>
                </xsl:for-each>
                <xsl:call-template name="indent">
                    <xsl:with-param name="n" select="$depth"/>
                </xsl:call-template>
                <span class="tag">&lt;/<xsl:value-of select="name()"/>&gt;</span>
                <xsl:text>&#10;</xsl:text>
            </xsl:when>
            <xsl:when test="normalize-space(text()) != ''">
                <span class="tag">&gt;</span><span class="value"><xsl:value-of select="normalize-space(text())"/></span><span class="tag">&lt;/<xsl:value-of select="name()"/>&gt;</span>
                <xsl:text>&#10;</xsl:text>
            </xsl:when>
            <xsl:otherwise>
                <span class="tag">/&gt;</span>
                <xsl:text>&#10;</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="indent">
        <xsl:param name="n" select="0"/>
        <xsl:if test="$n &gt; 0">
            <xsl:text>  </xsl:text>
            <xsl:call-template name="indent">
                <xsl:with-param name="n" select="$n - 1"/>
            </xsl:call-template>
        </xsl:if>
    </xsl:template>

</xsl:stylesheet>
