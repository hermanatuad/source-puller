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
    --xml-border: #e2e8f0;
    --xml-muted: #64748b;
    --xml-bg: #f8fafc;
    --xml-accent: #0ea5e9;
    --xml-accent-soft: #e0f2fe;
}

.xml-toolbar {
    position: sticky;
    top: 10px;
    z-index: 9;
}

.xml-node details {
    border: 1px solid var(--xml-border);
    border-radius: 8px;
    background: #ffffff;
    padding: 6px 8px;
    box-shadow: 0 1px 0 rgba(15, 23, 42, 0.03);
}

.xml-node-plain {
    border: 1px solid var(--xml-border);
    border-radius: 8px;
    background: #ffffff;
    padding: 8px;
}

.xml-node-head {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 4px;
}

.xml-node details[open] {
    background: var(--xml-bg);
}

.xml-node summary {
    cursor: pointer;
    list-style: none;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.xml-node summary::-webkit-details-marker {
    display: none;
}

.xml-children-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 8px;
    margin-top: 8px;
}

.xml-dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 12px;
}

.xml-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 8px;
}

.xml-stat-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
    border: 1px solid var(--xml-border);
    border-radius: 10px;
    padding: 10px;
}

.xml-stat-value {
    font-size: 20px;
    font-weight: 700;
    line-height: 1.1;
    color: #0f172a;
}

.xml-stat-label {
    font-size: 12px;
    color: var(--xml-muted);
}

.xml-section-tabs {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 2px;
    margin-bottom: 10px;
}

.xml-section-tab {
    border: 1px solid var(--xml-border);
    background: #ffffff;
    color: #0f172a;
    border-radius: 999px;
    padding: 6px 10px;
    font-size: 12px;
    line-height: 1;
    white-space: nowrap;
}

.xml-section-tab.active {
    background: var(--xml-accent-soft);
    border-color: #7dd3fc;
    color: #075985;
}

.xml-section-panel {
    display: none;
}

.xml-section-panel.active {
    display: block;
}

.xml-side-card {
    border: 1px solid var(--xml-border);
    border-radius: 10px;
    padding: 10px;
    background: #ffffff;
}

.xml-side-card h6 {
    font-size: 13px;
    margin-bottom: 8px;
}

.xml-attr-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.xml-leaf-value {
    color: #0f172a;
    font-size: 12px;
    line-height: 1.35;
    word-break: break-word;
}

.xml-meta {
    color: var(--xml-muted);
    font-size: 12px;
}

.xml-raw {
    max-height: 80vh;
    overflow: auto;
    margin: 0;
    border-radius: 8px;
}

@media (max-width: 991.98px) {
    .xml-dashboard-grid {
        grid-template-columns: 1fr;
    }

    .xml-stats-grid {
        grid-template-columns: 1fr;
    }
}
CSS);

$this->registerJs(<<<JS
(() => {
    const shell = document.querySelector('.xml-compact-shell');
    if (!shell) return;

    const toggleNodes = (open) => {
        shell.querySelectorAll('.xml-node details').forEach((el) => {
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
$renderElement = static function (\DOMElement $element, int $depth = 1) use (&$renderElement, $extractDirectText): string {
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
    $hasChildren = !empty($children);
    $isComplexNode = $hasChildren && (count($children) > 2 || $depth > 2);

    $searchText = trim($element->tagName . ' ' . $directText);
    foreach ($attributes as $attributeHtml) {
        $searchText .= ' ' . strip_tags($attributeHtml);
    }

    $html = '<div class="xml-node-item" data-xml-search="' . Html::encode($searchText) . '">';
    $html .= '<div class="xml-node">';
    if ($isComplexNode) {
        $html .= '<details' . ($depth <= 2 ? ' open' : '') . '>';
        $html .= '<summary>';
        $html .= Html::tag('span', Html::encode($element->tagName), ['class' => 'badge bg-primary']);

        if (!empty($attributes)) {
            $html .= implode(' ', $attributes);
        }

        $html .= Html::tag('span', count($children) . ' child', ['class' => 'xml-meta']);
        $html .= '</summary>';

        if ($directText !== '') {
            $html .= Html::tag(
                'div',
                Html::encode($directText),
                ['class' => 'xml-leaf-value mt-2']
            );
        }

        $html .= '<div class="xml-children-grid">';
        foreach ($children as $child) {
            $html .= $renderElement($child, $depth + 1);
        }
        $html .= '</div>';
        $html .= '</details>';
    } else {
        $html .= '<div class="xml-node-plain">';
        $html .= '<div class="xml-node-head">';
        $html .= Html::tag('span', Html::encode($element->tagName), ['class' => 'badge bg-primary']);

        if (!empty($attributes)) {
            $html .= implode(' ', $attributes);
        }

        if ($hasChildren) {
            $html .= Html::tag('span', count($children) . ' child', ['class' => 'xml-meta']);
        }
        $html .= '</div>';

        if ($directText !== '') {
            $html .= Html::tag('div', Html::encode($directText), ['class' => 'xml-leaf-value']);
        }

        if (!$hasChildren && $directText === '') {
            $html .= Html::tag('div', '(empty)', ['class' => 'xml-meta']);
        }

        if ($hasChildren) {
            $html .= '<div class="xml-children-grid mt-2">';
            foreach ($children as $child) {
                $html .= $renderElement($child, $depth + 1);
            }
            $html .= '</div>';
        }

        $html .= '</div>';
    }

    $html .= '</div>';
    $html .= '</div>';

    return $html;
};
?>

<div class="col-12 xml-compact-shell">
    <div class="card">
        <div class="card-header border-0 pb-0">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h5 class="card-title mb-1">Patient New XML Detail Viewer</h5>
                    <p class="text-muted mb-0">Tampilan padat untuk seluruh detail data patient-new.xml.</p>
                </div>
                <div class="xml-toolbar d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-soft-primary" data-xml-expand>Expand all</button>
                    <button type="button" class="btn btn-sm btn-soft-secondary" data-xml-collapse>Collapse all</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-lg-7">
                    <div class="alert alert-info mb-0 py-2">
                        <strong>File:</strong> <?= Html::encode($xmlPath) ?>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="alert alert-light mb-0 border py-2">
                        <strong>Last updated:</strong>
                        <?= $lastModifiedAt ? Html::encode(Yii::$app->formatter->asDatetime($lastModifiedAt)) : 'Unknown' ?>
                    </div>
                </div>
                <div class="col-lg-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Cari tag/value" data-xml-filter>
                </div>
            </div>

            <?php if (!empty($parseErrors)): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>XML parse error:</strong>
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
                        <h6 class="fw-semibold mb-2">Structured Explorer</h6>
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
                            <h6 class="fw-semibold">Root Element</h6>
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

            <div>
                <details>
                    <summary class="fw-semibold">Raw XML</summary>
                    <pre class="xml-raw border p-3 bg-dark text-light mt-2"><code><?= Html::encode($xmlContent) ?></code></pre>
                </details>
            </div>
        </div>
    </div>
</div>
