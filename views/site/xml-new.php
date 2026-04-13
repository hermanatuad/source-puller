<?php

/** @var yii\web\View $this */
/** @var string $xmlPath */
/** @var string $xmlContent */
/** @var \DOMDocument|null $dom */
/** @var string[] $parseErrors */
/** @var int|null $lastModifiedAt */

use yii\helpers\Html;

$this->title = 'Patient New XML Viewer';

$this->registerCss(<<<CSS
.xml-compact-shell {
    --xml-border: #d4dce8;
    --xml-muted: #526074;
    --xml-bg: #f2f6fb;
    --xml-accent: #0f6e9f;
    --xml-accent-soft: #dff1fb;
    --xml-title: #0b1726;
    --xml-card: #ffffff;
    background: radial-gradient(circle at top right, #e8f5ff 0%, #f7fbff 45%, #f3f7fc 100%);
    padding: 14px;
    border-radius: 16px;
}

.xml-page-card {
    border: 1px solid var(--xml-border);
    border-radius: 16px;
    background: var(--xml-card);
    box-shadow: 0 10px 30px rgba(15, 23, 38, 0.07);
}

.xml-page-title {
    font-size: 30px;
    font-weight: 800;
    line-height: 1.15;
    color: var(--xml-title);
}

.xml-page-subtitle {
    font-size: 15px;
    color: var(--xml-muted);
}

.xml-toolbar {
    position: sticky;
    top: 10px;
    z-index: 9;
}

.xml-info-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 10px;
}

.xml-info-card {
    border: 1px solid var(--xml-border);
    border-radius: 12px;
    background: #ffffff;
    padding: 12px;
}

.xml-info-label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #66758a;
    margin-bottom: 4px;
}

.xml-info-value {
    font-size: 15px;
    color: #0f172a;
    word-break: break-word;
}

.xml-explorer-controls {
    display: grid;
    grid-template-columns: 1.2fr repeat(4, minmax(0, 1fr));
    gap: 8px;
    margin-bottom: 10px;
}

.xml-control-card {
    border: 1px solid var(--xml-border);
    border-radius: 10px;
    background: #fff;
    padding: 8px 10px;
}

.xml-control-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    margin-bottom: 4px;
}

.xml-inline-check {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #1e293b;
}

.xml-mini-stats {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 8px;
    margin-top: 8px;
}

.xml-mini-card {
    border: 1px solid var(--xml-border);
    border-radius: 10px;
    background: #fff;
    padding: 8px;
}

.xml-mini-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
}

.xml-mini-meta {
    font-size: 12px;
    color: #64748b;
}

.xml-children-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
    gap: 10px;
    margin-top: 10px;
}

.xml-stage-lane {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(380px, 460px);
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 8px;
    margin-top: 10px;
}

.xml-stage-detail-row {
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: minmax(280px, 360px);
    gap: 8px;
    overflow-x: auto;
    margin-top: 8px;
    padding-bottom: 6px;
}

.xml-dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 12px;
}

.xml-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}

.xml-stat-card {
    background: linear-gradient(155deg, #ffffff 0%, #eef7ff 100%);
    border: 1px solid var(--xml-border);
    border-radius: 12px;
    padding: 14px;
}

.xml-stat-value {
    font-size: 30px;
    font-weight: 800;
    line-height: 1;
    color: #0f172a;
}

.xml-stat-label {
    margin-top: 4px;
    font-size: 13px;
    color: var(--xml-muted);
    font-weight: 600;
}

.xml-section-tabs {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 4px;
    margin-bottom: 10px;
}

.xml-section-tab {
    border: 1px solid var(--xml-border);
    background: #ffffff;
    color: #0f172a;
    border-radius: 10px;
    padding: 10px 12px;
    font-size: 14px;
    font-weight: 700;
    line-height: 1;
    white-space: nowrap;
}

.xml-section-tab.active {
    background: var(--xml-accent-soft);
    border-color: #82caef;
    color: var(--xml-accent);
}

.xml-section-panel {
    display: none;
}

.xml-section-panel.active {
    display: block;
}

.xml-side-card {
    border: 1px solid var(--xml-border);
    border-radius: 12px;
    padding: 14px;
    background: #ffffff;
}

.xml-side-card h6 {
    font-size: 17px;
    margin-bottom: 10px;
    color: var(--xml-title);
}

.xml-attr-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.xml-node-item {
    min-width: 0;
}

.xml-data-card {
    border: 1px solid var(--xml-border);
    border-radius: 12px;
    background: #ffffff;
    padding: 10px;
}

.xml-data-card details {
    border: 0;
    padding: 0;
    background: transparent;
}

.xml-data-card details[open] {
    background: transparent;
}

.xml-data-summary {
    cursor: pointer;
    list-style: none;
}

.xml-data-summary::-webkit-details-marker {
    display: none;
}

.xml-node-head {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 4px;
}

.xml-tag-name {
    font-size: 16px;
    font-weight: 800;
    color: #0f172a;
}

.xml-leaf-value {
    color: #1e293b;
    font-size: 14px;
    line-height: 1.45;
    word-break: break-word;
}

.xml-meta {
    color: var(--xml-muted);
    font-size: 12px;
    font-weight: 600;
}

.xml-path {
    display: block;
    font-size: 11px;
    color: #64748b;
    margin-top: 4px;
    word-break: break-all;
}

.xml-raw {
    max-height: 80vh;
    overflow: auto;
    margin: 0;
    border-radius: 10px;
    font-size: 14px;
}

@media (max-width: 1199.98px) {
    .xml-info-grid {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 991.98px) {
    .xml-dashboard-grid {
        grid-template-columns: 1fr;
    }

    .xml-stats-grid {
        grid-template-columns: 1fr;
    }

    .xml-info-grid {
        grid-template-columns: 1fr;
    }

    .xml-explorer-controls {
        grid-template-columns: 1fr;
    }

    .xml-page-title {
        font-size: 24px;
    }
}
CSS);

$this->registerJs(<<<JS
(() => {
    const shell = document.querySelector('.xml-compact-shell');
    if (!shell) return;

    const toggleNodes = (open) => {
        shell.querySelectorAll('.xml-node-item details').forEach((el) => {
            el.open = open;
        });
    };

    const expandBtn = shell.querySelector('[data-xml-expand]');
    const collapseBtn = shell.querySelector('[data-xml-collapse]');
    if (expandBtn) expandBtn.addEventListener('click', () => toggleNodes(true));
    if (collapseBtn) collapseBtn.addEventListener('click', () => toggleNodes(false));

    shell.querySelectorAll('[data-xml-tab]').forEach((tab) => {
        tab.addEventListener('click', () => {
            const target = tab.getAttribute('data-xml-tab');
            shell.querySelectorAll('[data-xml-tab]').forEach((el) => el.classList.remove('active'));
            shell.querySelectorAll('[data-xml-panel]').forEach((panel) => panel.classList.remove('active'));
            tab.classList.add('active');
            const panel = shell.querySelector('[data-xml-panel="' + target + '"]');
            if (panel) panel.classList.add('active');
        });
    });

    const filterInput = shell.querySelector('[data-xml-filter]');
    if (filterInput) {
        filterInput.addEventListener('input', () => {
            const needle = filterInput.value.trim().toLowerCase();
            shell.querySelectorAll('.xml-node-item').forEach((item) => {
                const haystack = (item.getAttribute('data-xml-search') || '').toLowerCase();
                item.style.display = needle === '' || haystack.includes(needle) ? '' : 'none';
            });
        });
    }
})();
JS);

$stats = [
    'elements' => 0,
    'leaves' => 0,
    'maxDepth' => 0,
];

$topLevelChildren = [];
$rootAttributes = [];
$sectionStats = [];

if ($dom !== null && $dom->documentElement !== null) {
    foreach ($dom->documentElement->childNodes as $childNode) {
        if ($childNode instanceof \DOMElement) {
            $topLevelChildren[] = $childNode;
        }
    }

    if ($dom->documentElement->hasAttributes()) {
        foreach ($dom->documentElement->attributes as $attribute) {
            $rootAttributes[] = $attribute->name . '="' . $attribute->value . '"';
        }
    }

    $collectStats = null;
    $collectStats = static function (\DOMElement $element, int $depth = 1) use (&$collectStats, &$stats): void {
        $stats['elements']++;
        if ($depth > $stats['maxDepth']) {
            $stats['maxDepth'] = $depth;
        }

        $childElements = [];
        foreach ($element->childNodes as $childNode) {
            if ($childNode instanceof \DOMElement) {
                $childElements[] = $childNode;
            }
        }

        if (empty($childElements)) {
            $stats['leaves']++;
            return;
        }

        foreach ($childElements as $childElement) {
            $collectStats($childElement, $depth + 1);
        }
    };

    $collectStats($dom->documentElement);

    $collectMetrics = null;
    $collectMetrics = static function (\DOMElement $element, int $depth = 1) use (&$collectMetrics): array {
        $elements = 1;
        $leaves = 0;
        $maxDepth = $depth;

        $children = [];
        foreach ($element->childNodes as $childNode) {
            if ($childNode instanceof \DOMElement) {
                $children[] = $childNode;
            }
        }

        if (empty($children)) {
            $leaves = 1;
        }

        foreach ($children as $child) {
            $result = $collectMetrics($child, $depth + 1);
            $elements += $result['elements'];
            $leaves += $result['leaves'];
            if ($result['maxDepth'] > $maxDepth) {
                $maxDepth = $result['maxDepth'];
            }
        }

        return [
            'elements' => $elements,
            'leaves' => $leaves,
            'maxDepth' => $maxDepth,
        ];
    };

    foreach ($topLevelChildren as $child) {
        $sectionStats[] = [
            'name' => $child->tagName,
            'metrics' => $collectMetrics($child),
        ];
    }
}

$extractDirectText = static function (\DOMElement $element): string {
    $parts = [];
    foreach ($element->childNodes as $childNode) {
        if (in_array($childNode->nodeType, [XML_TEXT_NODE, XML_CDATA_SECTION_NODE], true)) {
            $value = trim((string) $childNode->nodeValue);
            if ($value !== '') {
                $parts[] = $value;
            }
        }
    }

    return trim(implode(' ', $parts));
};

$renderElement = null;
$renderElement = static function (\DOMElement $element, int $depth = 1, string $path = '') use (&$renderElement, $extractDirectText): string {
    $attributes = [];
    if ($element->hasAttributes()) {
        foreach ($element->attributes as $attribute) {
            $attributes[] = Html::tag(
                'span',
                Html::encode($attribute->name . '="' . $attribute->value . '"'),
                ['class' => 'badge bg-soft-info text-info']
            );
        }
    }

    $children = [];
    foreach ($element->childNodes as $childNode) {
        if ($childNode instanceof \DOMElement) {
            $children[] = $childNode;
        }
    }

    $directText = $extractDirectText($element);
    $tagName = $element->tagName;
    $currentPath = $path === '' ? $tagName : $path . '/' . $tagName;
    $hasChildren = !empty($children);
    $isStageNode = $tagName === 'stage';
    $isComplexNode = $hasChildren && (count($children) > 2 || $depth > 2 || $isStageNode);
    $childrenClass = 'xml-children-grid';
    $isLeaf = !$hasChildren;
    $isEmpty = $isLeaf && $directText === '';

    if ($tagName === 'stages') {
        $childrenClass = 'xml-stage-lane';
    } elseif ($isStageNode) {
        $childrenClass = 'xml-stage-detail-row';
    }

    $searchText = trim($tagName . ' ' . $directText . ' ' . $currentPath);
    foreach ($attributes as $attributeHtml) {
        $searchText .= ' ' . strip_tags($attributeHtml);
    }

    $html = '<div class="xml-node-item" data-xml-search="' . Html::encode($searchText) . '" data-xml-tag="' . Html::encode(strtolower($tagName)) . '" data-xml-value="' . Html::encode(strtolower($directText)) . '" data-xml-path="' . Html::encode(strtolower($currentPath)) . '" data-xml-depth="' . $depth . '" data-xml-leaf="' . ($isLeaf ? '1' : '0') . '" data-xml-empty="' . ($isEmpty ? '1' : '0') . '">';
    $html .= '<article class="xml-data-card">';
    if ($isComplexNode) {
        $html .= '<details' . ($depth <= 2 ? ' open' : '') . '>';
        $html .= '<summary class="xml-data-summary">';
        $html .= '<div class="xml-node-head">';
        $html .= Html::tag('span', Html::encode($tagName), ['class' => 'xml-tag-name']);

        if (!empty($attributes)) {
            $html .= implode(' ', $attributes);
        }

        $html .= Html::tag('span', count($children) . ' child', ['class' => 'xml-meta']);
        $html .= '</div>';
        $html .= Html::tag('span', Html::encode($currentPath), ['class' => 'xml-path']);
        $html .= '</summary>';

        if ($directText !== '') {
            $html .= Html::tag(
                'div',
                Html::encode($directText),
                ['class' => 'xml-leaf-value mt-2']
            );
        }

        $html .= '<div class="' . $childrenClass . '">';
        foreach ($children as $child) {
            $html .= $renderElement($child, $depth + 1, $currentPath);
        }
        $html .= '</div>';
        $html .= '</details>';
    } else {
        $html .= '<div class="xml-node-head">';
        $html .= Html::tag('span', Html::encode($tagName), ['class' => 'xml-tag-name']);

        if (!empty($attributes)) {
            $html .= implode(' ', $attributes);
        }

        if ($hasChildren) {
            $html .= Html::tag('span', count($children) . ' child', ['class' => 'xml-meta']);
        }
        $html .= '</div>';
        $html .= Html::tag('span', Html::encode($currentPath), ['class' => 'xml-path']);

        if ($directText !== '') {
            $html .= Html::tag('div', Html::encode($directText), ['class' => 'xml-leaf-value']);
        }

        if (!$hasChildren && $directText === '') {
            $html .= Html::tag('div', '(empty)', ['class' => 'xml-meta']);
        }

        if ($hasChildren) {
            $html .= '<div class="' . $childrenClass . ' mt-2">';
            foreach ($children as $child) {
                $html .= $renderElement($child, $depth + 1, $currentPath);
            }
            $html .= '</div>';
        }
    }

    $html .= '</article>';
    $html .= '</div>';

    return $html;
};
?>

<div class="col-12 xml-compact-shell">
    <div class="card xml-page-card">
        <div class="card-header border-0 pb-0">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h1 class="xml-page-title mb-1">Patient New XML Detail Viewer</h1>
                    <p class="xml-page-subtitle mb-0">Semua data XML ditampilkan dalam format card dengan tulisan lebih besar dan jelas.</p>
                </div>
                <div class="xml-toolbar d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-soft-primary" data-xml-expand>Expand all</button>
                    <button type="button" class="btn btn-sm btn-soft-secondary" data-xml-collapse>Collapse all</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="xml-info-grid mb-3">
                <div class="xml-info-card">
                    <span class="xml-info-label">File</span>
                    <div class="xml-info-value"><?= Html::encode($xmlPath) ?></div>
                </div>
                <div class="xml-info-card">
                    <span class="xml-info-label">Last Updated</span>
                    <div class="xml-info-value"><?= $lastModifiedAt ? Html::encode(Yii::$app->formatter->asDatetime($lastModifiedAt)) : 'Unknown' ?></div>
                </div>
                <div class="xml-info-card">
                    <span class="xml-info-label">Filter</span>
                    <input type="text" class="form-control" placeholder="Cari tag atau value" data-xml-filter>
                </div>
            </div>

            <?php if (!empty($parseErrors)): ?>
                <div class="xml-side-card border-danger" role="alert">
                    <h6 class="text-danger fw-bold mb-2">XML Parse Error</h6>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($parseErrors as $error): ?>
                            <li><?= Html::encode($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($dom !== null && $dom->documentElement !== null): ?>
                <div class="xml-dashboard-grid mb-3">
                    <div>
                        <h2 class="fw-bold fs-4 mb-2">Structured Explorer</h2>
                        <div class="xml-section-tabs">
                            <button type="button" class="xml-section-tab active" data-xml-tab="root">root</button>
                            <?php foreach ($topLevelChildren as $index => $section): ?>
                                <button type="button" class="xml-section-tab" data-xml-tab="section-<?= $index ?>">
                                    <?= Html::encode($section->tagName) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <div class="xml-section-panel active" data-xml-panel="root">
                            <div class="xml-children-grid">
                                <?= $renderElement($dom->documentElement) ?>
                            </div>
                        </div>

                        <?php foreach ($topLevelChildren as $index => $section): ?>
                            <div class="xml-section-panel" data-xml-panel="section-<?= $index ?>">
                                <div class="xml-children-grid">
                                    <?= $renderElement($section) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <div class="xml-stats-grid">
                            <div class="xml-stat-card">
                                <div class="xml-stat-value"><?= Html::encode((string) $stats['elements']) ?></div>
                                <div class="xml-stat-label">Total Elements</div>
                            </div>
                            <div class="xml-stat-card">
                                <div class="xml-stat-value"><?= Html::encode((string) $stats['leaves']) ?></div>
                                <div class="xml-stat-label">Leaf Nodes</div>
                            </div>
                            <div class="xml-stat-card">
                                <div class="xml-stat-value"><?= Html::encode((string) $stats['maxDepth']) ?></div>
                                <div class="xml-stat-label">Max Depth</div>
                            </div>
                        </div>

                        <div class="xml-side-card">
                            <h6 class="fw-bold">Root Element</h6>
                            <div class="mb-2">
                                <?= Html::tag('span', Html::encode($dom->documentElement->tagName), ['class' => 'badge bg-primary']) ?>
                            </div>
                            <div class="xml-meta">Attributes</div>
                            <div class="xml-attr-list mt-1">
                                <?php if (!empty($rootAttributes)): ?>
                                    <?php foreach ($rootAttributes as $attributeText): ?>
                                        <?= Html::tag('span', Html::encode($attributeText), ['class' => 'badge bg-soft-info text-info']) ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="xml-meta">(none)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="xml-side-card">
                <details>
                    <summary class="fw-semibold fs-5">Raw XML</summary>
                    <pre class="xml-raw border p-3 bg-dark text-light mt-2"><code><?= Html::encode($xmlContent) ?></code></pre>
                </details>
            </div>
        </div>
    </div>
</div>
