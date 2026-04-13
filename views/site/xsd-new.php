<?php

/** @var yii\web\View $this */
/** @var string $xsdPath */
/** @var string $xsdContent */
/** @var int|null $lastModifiedAt */

use yii\helpers\Html;

$this->title = 'Patient New XSD Viewer';

$schemaDom = new DOMDocument('1.0', 'UTF-8');
$schemaDom->preserveWhiteSpace = false;
$schemaDom->formatOutput = true;
$schemaDom->loadXML($xsdContent, LIBXML_NOBLANKS);

$xpath = new DOMXPath($schemaDom);
$xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');

$collectDirectText = static function (DOMElement $element): string {
    $parts = [];
    foreach ($element->childNodes as $childNode) {
        if ($childNode->nodeType === XML_TEXT_NODE || $childNode->nodeType === XML_CDATA_SECTION_NODE) {
            $value = trim((string) $childNode->nodeValue);
            if ($value !== '') {
                $parts[] = $value;
            }
        }
    }

    return trim(implode(' ', $parts));
};

$globalElements = [];
$complexTypes = [];
$simpleTypes = [];
$attributes = [];

foreach ($xpath->query('/xs:schema/xs:element') as $element) {
    if ($element instanceof DOMElement) {
        $globalElements[] = $element;
    }
}

foreach ($xpath->query('/xs:schema/xs:complexType') as $typeNode) {
    if ($typeNode instanceof DOMElement) {
        $complexTypes[] = $typeNode;
    }
}

foreach ($xpath->query('/xs:schema/xs:simpleType') as $typeNode) {
    if ($typeNode instanceof DOMElement) {
        $simpleTypes[] = $typeNode;
    }
}

foreach ($xpath->query('/xs:schema/xs:attribute') as $attributeNode) {
    if ($attributeNode instanceof DOMElement) {
        $attributes[] = $attributeNode;
    }
}

$schemaStats = [
    'globalElements' => count($globalElements),
    'complexTypes' => count($complexTypes),
    'simpleTypes' => count($simpleTypes),
    'attributes' => count($attributes),
];

$renderParticle = null;
$renderParticle = static function (DOMElement $node, int $depth = 0) use (&$renderParticle, $collectDirectText): string {
    $label = $node->tagName;
    $name = $node->getAttribute('name');
    $type = $node->getAttribute('type');
    $ref = $node->getAttribute('ref');
    $minOccurs = $node->getAttribute('minOccurs');
    $maxOccurs = $node->getAttribute('maxOccurs');
    $use = $node->getAttribute('use');
    $fixed = $node->getAttribute('fixed');
    $default = $node->getAttribute('default');
    $children = [];

    foreach ($node->childNodes as $childNode) {
        if ($childNode instanceof DOMElement) {
            $children[] = $childNode;
        }
    }

    $directText = $collectDirectText($node);
    $cardClass = 'xsd-node-card';
    $pathLabel = $name !== '' ? $name : ($ref !== '' ? $ref : $label);

    $html = '<article class="' . $cardClass . '" data-xsd-node="' . Html::encode(strtolower($pathLabel)) . '">';
    $html .= '<div class="d-flex align-items-start justify-content-between gap-2 flex-wrap mb-2">';
    $html .= '<div>';
    $html .= Html::tag('div', Html::encode($pathLabel), ['class' => 'xsd-node-title']);
    $html .= Html::tag('div', Html::encode($label), ['class' => 'xsd-node-subtitle']);
    $html .= '</div>';
    $html .= Html::tag('span', $depth === 0 ? 'Schema Root' : 'Level ' . $depth, ['class' => 'badge bg-soft-info text-info']);
    $html .= '</div>';

    $meta = [];
    if ($type !== '') {
        $meta[] = Html::tag('span', 'type: ' . Html::encode($type), ['class' => 'badge bg-soft-primary text-primary']);
    }
    if ($ref !== '') {
        $meta[] = Html::tag('span', 'ref: ' . Html::encode($ref), ['class' => 'badge bg-soft-secondary text-secondary']);
    }
    if ($minOccurs !== '') {
        $meta[] = Html::tag('span', 'min: ' . Html::encode($minOccurs), ['class' => 'badge bg-light text-dark']);
    }
    if ($maxOccurs !== '') {
        $meta[] = Html::tag('span', 'max: ' . Html::encode($maxOccurs), ['class' => 'badge bg-light text-dark']);
    }
    if ($use !== '') {
        $meta[] = Html::tag('span', 'use: ' . Html::encode($use), ['class' => 'badge bg-soft-warning text-warning']);
    }
    if ($fixed !== '') {
        $meta[] = Html::tag('span', 'fixed: ' . Html::encode($fixed), ['class' => 'badge bg-soft-success text-success']);
    }
    if ($default !== '') {
        $meta[] = Html::tag('span', 'default: ' . Html::encode($default), ['class' => 'badge bg-soft-success text-success']);
    }

    if (!empty($meta)) {
        $html .= '<div class="d-flex flex-wrap gap-1 mb-2">' . implode('', $meta) . '</div>';
    }

    if ($directText !== '') {
        $html .= Html::tag('div', Html::encode($directText), ['class' => 'xsd-node-text mb-2']);
    }

    if (!empty($children)) {
        $html .= '<div class="xsd-child-grid">';
        foreach ($children as $child) {
            $html .= $renderParticle($child, $depth + 1);
        }
        $html .= '</div>';
    }

    $html .= '</article>';

    return $html;
};

$renderTypeSummary = static function (DOMElement $typeNode) use ($xpath): array {
    $name = $typeNode->getAttribute('name');
    $sequenceNodes = [];
    foreach ($xpath->query('.//xs:sequence/xs:element | .//xs:choice/xs:element | .//xs:all/xs:element', $typeNode) as $element) {
        if ($element instanceof DOMElement) {
            $sequenceNodes[] = $element;
        }
    }

    $attributeNodes = [];
    foreach ($xpath->query('.//xs:attribute', $typeNode) as $attributeNode) {
        if ($attributeNode instanceof DOMElement) {
            $attributeNodes[] = $attributeNode;
        }
    }

    return [
        'name' => $name,
        'elements' => $sequenceNodes,
        'attributes' => $attributeNodes,
    ];
};

$typeSummaries = [];
foreach ($complexTypes as $typeNode) {
    $typeSummaries[] = $renderTypeSummary($typeNode);
}

$this->registerCss(<<<CSS
.xsd-shell {
    --xsd-border: #d5ddeb;
    --xsd-muted: #5a687c;
    --xsd-title: #0b1726;
    --xsd-card: #ffffff;
    background: radial-gradient(circle at top right, #edf7ff 0%, #f8fbff 40%, #f4f7fb 100%);
    padding: 14px;
    border-radius: 16px;
}

.xsd-page-card {
    border: 1px solid var(--xsd-border);
    border-radius: 16px;
    background: var(--xsd-card);
    box-shadow: 0 10px 28px rgba(15, 23, 38, 0.07);
}

.xsd-title {
    font-size: 30px;
    font-weight: 800;
    color: var(--xsd-title);
    line-height: 1.1;
}

.xsd-subtitle {
    font-size: 15px;
    color: var(--xsd-muted);
}

.xsd-info-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 10px;
}

.xsd-info-card, .xsd-panel-card, .xsd-node-card {
    border: 1px solid var(--xsd-border);
    border-radius: 12px;
    background: #fff;
}

.xsd-info-card {
    padding: 12px;
}

.xsd-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
    margin-bottom: 4px;
}

.xsd-value {
    font-size: 15px;
    color: #0f172a;
    word-break: break-word;
}

.xsd-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
}

.xsd-stat-card {
    padding: 14px;
    background: linear-gradient(155deg, #ffffff 0%, #eef7ff 100%);
    border: 1px solid var(--xsd-border);
    border-radius: 12px;
}

.xsd-stat-value {
    font-size: 30px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1;
}

.xsd-stat-label {
    margin-top: 4px;
    font-size: 13px;
    color: var(--xsd-muted);
    font-weight: 600;
}

.xsd-section-title {
    font-size: 22px;
    font-weight: 800;
    color: var(--xsd-title);
    margin-bottom: 10px;
}

.xsd-panel-card {
    padding: 14px;
}

.xsd-node-title {
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
}

.xsd-node-subtitle {
    font-size: 12px;
    color: var(--xsd-muted);
}

.xsd-node-text {
    font-size: 14px;
    color: #1f2937;
    line-height: 1.5;
}

.xsd-child-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 10px;
}

.xsd-type-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 10px;
}

.xsd-pill-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.xsd-code-box {
    border-radius: 12px;
    max-height: 80vh;
    overflow: auto;
    font-size: 13px;
}

@media (max-width: 991.98px) {
    .xsd-info-grid,
    .xsd-stats-grid {
        grid-template-columns: 1fr;
    }

    .xsd-title {
        font-size: 24px;
    }

    .xsd-type-list,
    .xsd-child-grid {
        grid-template-columns: 1fr;
    }
}
CSS);
?>

<div class="col-12 xsd-shell">
    <div class="card xsd-page-card">
        <div class="card-header border-0 pb-0">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <h1 class="xsd-title mb-1">Patient New XSD Viewer</h1>
                    <p class="xsd-subtitle mb-0">Struktur schema ditampilkan dalam kartu, ringkasan tipe, dan tree deklarasi elemen.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-sm btn-soft-primary" href="#global-elements">Global Elements</a>
                    <a class="btn btn-sm btn-soft-secondary" href="#complex-types">Complex Types</a>
                    <a class="btn btn-sm btn-soft-dark" href="#raw-xsd">Raw XSD</a>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="xsd-info-grid mb-3">
                <div class="xsd-info-card">
                    <div class="xsd-label">File</div>
                    <div class="xsd-value"><?= Html::encode($xsdPath) ?></div>
                </div>
                <div class="xsd-info-card">
                    <div class="xsd-label">Last Updated</div>
                    <div class="xsd-value"><?= $lastModifiedAt ? Html::encode(Yii::$app->formatter->asDatetime($lastModifiedAt)) : 'Unknown' ?></div>
                </div>
                <div class="xsd-info-card">
                    <div class="xsd-label">Root</div>
                    <div class="xsd-value">xs:schema</div>
                </div>
            </div>

            <div class="xsd-stats-grid mb-3">
                <div class="xsd-stat-card">
                    <div class="xsd-stat-value"><?= Html::encode((string) $schemaStats['globalElements']) ?></div>
                    <div class="xsd-stat-label">Global Elements</div>
                </div>
                <div class="xsd-stat-card">
                    <div class="xsd-stat-value"><?= Html::encode((string) $schemaStats['complexTypes']) ?></div>
                    <div class="xsd-stat-label">Complex Types</div>
                </div>
                <div class="xsd-stat-card">
                    <div class="xsd-stat-value"><?= Html::encode((string) $schemaStats['simpleTypes']) ?></div>
                    <div class="xsd-stat-label">Simple Types</div>
                </div>
                <div class="xsd-stat-card">
                    <div class="xsd-stat-value"><?= Html::encode((string) $schemaStats['attributes']) ?></div>
                    <div class="xsd-stat-label">Global Attributes</div>
                </div>
            </div>

            <div class="xsd-panel-card mb-3" id="global-elements">
                <div class="xsd-section-title">Global Elements</div>
                <div class="xsd-child-grid">
                    <?php foreach ($globalElements as $globalElement): ?>
                        <?php
                            $name = $globalElement->getAttribute('name');
                            $type = $globalElement->getAttribute('type');
                            $ref = $globalElement->getAttribute('ref');
                            $minOccurs = $globalElement->getAttribute('minOccurs');
                            $maxOccurs = $globalElement->getAttribute('maxOccurs');
                            $children = [];
                            foreach ($globalElement->childNodes as $childNode) {
                                if ($childNode instanceof DOMElement) {
                                    $children[] = $childNode;
                                }
                            }
                        ?>
                        <article class="xsd-node-card p-3">
                            <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap mb-2">
                                <div>
                                    <div class="xsd-node-title"><?= Html::encode($name !== '' ? $name : $globalElement->tagName) ?></div>
                                    <div class="xsd-node-subtitle">Global xs:element</div>
                                </div>
                                <?php if ($type !== ''): ?><span class="badge bg-soft-primary text-primary">type: <?= Html::encode($type) ?></span><?php endif; ?>
                            </div>
                            <div class="xsd-pill-list mb-2">
                                <?php if ($ref !== ''): ?><span class="badge bg-soft-secondary text-secondary">ref: <?= Html::encode($ref) ?></span><?php endif; ?>
                                <?php if ($minOccurs !== ''): ?><span class="badge bg-light text-dark">min: <?= Html::encode($minOccurs) ?></span><?php endif; ?>
                                <?php if ($maxOccurs !== ''): ?><span class="badge bg-light text-dark">max: <?= Html::encode($maxOccurs) ?></span><?php endif; ?>
                            </div>
                            <?php if (!empty($children)): ?>
                                <div class="xsd-child-grid">
                                    <?php foreach ($children as $child): ?>
                                        <?= $renderParticle($child) ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="xsd-panel-card mb-3" id="complex-types">
                <div class="xsd-section-title">Complex Types</div>
                <div class="xsd-type-list">
                    <?php foreach ($typeSummaries as $summary): ?>
                        <article class="xsd-node-card p-3">
                            <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap mb-2">
                                <div>
                                    <div class="xsd-node-title"><?= Html::encode($summary['name']) ?></div>
                                    <div class="xsd-node-subtitle">complexType</div>
                                </div>
                                <span class="badge bg-soft-info text-info"><?= count($summary['elements']) ?> elements</span>
                            </div>

                            <?php if (!empty($summary['attributes'])): ?>
                                <div class="mb-2">
                                    <div class="xsd-label">Attributes</div>
                                    <div class="xsd-pill-list">
                                        <?php foreach ($summary['attributes'] as $attributeNode): ?>
                                            <?php $attributeName = $attributeNode->getAttribute('name'); ?>
                                            <span class="badge bg-soft-secondary text-secondary"><?= Html::encode($attributeName !== '' ? $attributeName : $attributeNode->tagName) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($summary['elements'])): ?>
                                <div class="xsd-child-grid">
                                    <?php foreach ($summary['elements'] as $elementNode): ?>
                                        <?= $renderParticle($elementNode) ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="xsd-node-text text-muted">No nested elements declared.</div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (!empty($simpleTypes) || !empty($attributes)): ?>
                <div class="xsd-panel-card mb-3">
                    <div class="xsd-section-title">Supporting Declarations</div>
                    <div class="xsd-child-grid">
                        <?php foreach ($simpleTypes as $simpleType): ?>
                            <article class="xsd-node-card p-3">
                                <div class="xsd-node-title"><?= Html::encode($simpleType->getAttribute('name') ?: $simpleType->tagName) ?></div>
                                <div class="xsd-node-subtitle">simpleType</div>
                                <?php $directText = $collectDirectText($simpleType); ?>
                                <?php if ($directText !== ''): ?>
                                    <div class="xsd-node-text mt-2"><?= Html::encode($directText) ?></div>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>

                        <?php foreach ($attributes as $attributeNode): ?>
                            <article class="xsd-node-card p-3">
                                <div class="xsd-node-title"><?= Html::encode($attributeNode->getAttribute('name') ?: $attributeNode->tagName) ?></div>
                                <div class="xsd-node-subtitle">global attribute</div>
                                <div class="xsd-pill-list mt-2">
                                    <?php if ($attributeNode->getAttribute('type') !== ''): ?><span class="badge bg-soft-primary text-primary">type: <?= Html::encode($attributeNode->getAttribute('type')) ?></span><?php endif; ?>
                                    <?php if ($attributeNode->getAttribute('use') !== ''): ?><span class="badge bg-light text-dark">use: <?= Html::encode($attributeNode->getAttribute('use')) ?></span><?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="xsd-panel-card" id="raw-xsd">
                <div class="xsd-section-title">Raw XSD</div>
                <pre class="xsd-code-box bg-dark text-light p-3 mb-0"><code><?= Html::encode($xsdContent) ?></code></pre>
            </div>
        </div>
    </div>
</div>
