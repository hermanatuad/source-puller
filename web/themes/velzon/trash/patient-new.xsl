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
                    :root {
                        --bg: #f4f8ff;
                        --panel: #ffffff;
                        --line: #d6e2f4;
                        --ink: #1f2937;
                        --muted: #64748b;
                        --tag: #0b4ea2;
                        --attr: #a61b58;
                        --value: #0b6e4f;
                    }
                    body {
                        margin: 0;
                        font-family: "Segoe UI", Arial, sans-serif;
                        font-size: 17px;
                        background: radial-gradient(circle at 10% 0%, #eaf2ff, var(--bg));
                        color: var(--ink);
                    }
                    .wrap {
                        max-width: 1200px;
                        margin: 20px auto;
                        padding: 0 16px;
                    }
                    .card {
                        background: var(--panel);
                        border: 1px solid var(--line);
                        border-radius: 14px;
                        box-shadow: 0 10px 28px rgba(16, 24, 40, 0.08);
                        overflow: hidden;
                    }
                    .head {
                        padding: 14px 16px;
                        border-bottom: 1px solid #e8effb;
                        background: linear-gradient(120deg, #f8fbff, #edf4ff);
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        gap: 8px;
                        flex-wrap: wrap;
                    }
                    .title {
                        font-weight: 700;
                        font-size: 22px;
                    }
                    .toolbar {
                        display: flex;
                        gap: 8px;
                    }
                    .btn {
                        border: 1px solid #c5d8f2;
                        background: #ffffff;
                        color: #23406a;
                        border-radius: 8px;
                        padding: 8px 12px;
                        font-size: 14px;
                        cursor: pointer;
                    }
                    .btn:hover {
                        background: #eef5ff;
                    }
                    .tree {
                        padding: 14px;
                        font-family: Consolas, "Courier New", monospace;
                        font-size: 16px;
                        line-height: 1.55;
                    }
                    .node {
                        margin-left: 14px;
                        border-left: 1px dashed #d7e3f7;
                        padding-left: 10px;
                    }
                    details.node-detail {
                        margin: 2px 0;
                    }
                    details.node-detail > summary {
                        cursor: pointer;
                        list-style: none;
                        user-select: none;
                    }
                    details.node-detail > summary::-webkit-details-marker {
                        display: none;
                    }
                    .close-line,
                    .leaf {
                        margin: 2px 0 4px 0;
                    }
                    .meta {
                        color: var(--muted);
                        margin-left: 8px;
                        font-size: 13px;
                    }
                    .bracket { color: #334155; }
                    .tag { color: var(--tag); }
                    .attr { color: var(--attr); }
                    .value { color: var(--value); }
                    .hint {
                        font-size: 14px;
                        color: var(--muted);
                        padding: 10px 16px 14px;
                        border-top: 1px solid #edf2fb;
                    }
                </style>
                <script>
                    function toggleAll(openState) {
                        var nodes = document.querySelectorAll('details.node-detail');
                        for (var i = 0; i &lt; nodes.length; i++) {
                            nodes[i].open = openState;
                        }
                    }
                </script>
            </head>
            <body>
                <div class="wrap">
                    <div class="card">
                        <div class="head">
                            <div class="title">Patient New XML Viewer</div>
                            <div class="toolbar">
                                <button class="btn" type="button" onclick="toggleAll(true)">Expand All</button>
                                <button class="btn" type="button" onclick="toggleAll(false)">Collapse All</button>
                            </div>
                        </div>
                        <div class="tree">
                            <xsl:apply-templates select="*" mode="tree"/>
                        </div>
                        <div class="hint">Klik setiap tag untuk buka/tutup isi. Gunakan tombol Expand All/Collapse All untuk navigasi cepat.</div>
                    </div>
                </div>
            </body>
        </html>
    </xsl:template>

    <xsl:template match="@*" mode="attrs">
        <span class="attr"> <xsl:value-of select="name()"/>="</span><span class="value"><xsl:value-of select="."/></span><span class="attr">"</span>
    </xsl:template>

    <xsl:template match="*" mode="tree">
        <xsl:choose>
            <xsl:when test="*">
                <div class="node">
                    <details class="node-detail" open="open">
                        <summary>
                            <span class="bracket">&lt;</span><span class="tag"><xsl:value-of select="name()"/></span><xsl:apply-templates select="@*" mode="attrs"/><span class="bracket">&gt;</span>
                            <span class="meta"><xsl:value-of select="count(*)"/> child nodes</span>
                        </summary>
                        <xsl:apply-templates select="*" mode="tree"/>
                        <div class="close-line">
                            <span class="bracket">&lt;/</span><span class="tag"><xsl:value-of select="name()"/></span><span class="bracket">&gt;</span>
                        </div>
                    </details>
                </div>
            </xsl:when>
            <xsl:when test="normalize-space(text()) != ''">
                <div class="leaf">
                    <span class="bracket">&lt;</span><span class="tag"><xsl:value-of select="name()"/></span><xsl:apply-templates select="@*" mode="attrs"/><span class="bracket">&gt;</span><span class="value"><xsl:value-of select="normalize-space(text())"/></span><span class="bracket">&lt;/</span><span class="tag"><xsl:value-of select="name()"/></span><span class="bracket">&gt;</span>
                </div>
            </xsl:when>
            <xsl:otherwise>
                <div class="leaf">
                    <span class="bracket">&lt;</span><span class="tag"><xsl:value-of select="name()"/></span><xsl:apply-templates select="@*" mode="attrs"/><span class="bracket">/&gt;</span>
                </div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

</xsl:stylesheet>
