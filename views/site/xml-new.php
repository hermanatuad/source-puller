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
$renderElement = static function (\DOMElement $element) use (&$renderElement, $extractDirectText): string {
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

    $searchText = trim($element->tagName . ' ' . $directText);
    foreach ($attributes as $attributeHtml) {
        $searchText .= ' ' . strip_tags($attributeHtml);
    }

    $html = '<div class="xml-node-item" data-xml-search="' . Html::encode($searchText) . '">';
    $html .= '<div class="xml-node">';
    $html .= '<details' . ($hasChildren ? '' : ' open') . '>';
    $html .= '<summary>';
    $html .= Html::tag('span', Html::encode($element->tagName), ['class' => 'badge bg-primary']);

    if (!empty($attributes)) {
        $html .= implode(' ', $attributes);
    }

    if ($hasChildren) {
        $html .= Html::tag('span', count($children) . ' child', ['class' => 'xml-meta']);
    }
    $html .= '</summary>';

    if ($directText !== '') {
        $html .= Html::tag(
            'div',
            Html::encode($directText),
            ['class' => 'xml-leaf-value mt-2']
        );
    }

    if (!$hasChildren && $directText === '') {
        $html .= Html::tag('div', '(empty)', ['class' => 'xml-meta mt-2']);
    }

    if ($hasChildren) {
        $html .= '<div class="xml-children-grid">';
        foreach ($children as $child) {
            $html .= $renderElement($child);
        }
        $html .= '</div>';
    }

    $html .= '</details>';
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
                <div class="mb-3">
                    <h6 class="fw-semibold mb-2">Structured Detail</h6>
                    <div class="xml-meta mb-2">Semua elemen ditampilkan hierarkis, ringkas, dan bisa di-expand/collapse.</div>
                    <div class="xml-children-grid">
                        <?= $renderElement($dom->documentElement) ?>
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
